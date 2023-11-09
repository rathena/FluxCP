<?php
require_once 'Flux/Config.php';
require_once 'Flux/Error.php';
require_once 'Flux/Connection.php';
require_once 'Flux/LoginServer.php';
require_once 'Flux/CharServer.php';
require_once 'Flux/MapServer.php';
require_once 'Flux/Athena.php';
require_once 'Flux/LoginAthenaGroup.php';
require_once 'Flux/Addon.php';
require_once 'functions/getReposVersion.php';
require_once 'functions/discordwebhook.php';

// Get the SVN revision or GIT hash of the top-level directory (FLUX_ROOT).
define('FLUX_REPOSVERSION', getReposVersion());

/**
 * The Flux class contains methods related to the application on the larger
 * scale. For the most part, it handles application initialization such as
 * parsing the configuration files and whatnot.
 */
class Flux {
	/**
	 * Current version.
	 */
	const VERSION = '2.0.0';

	/**
	 * Repository SVN version or GIT hash of the top-level revision.
	 */
	const REPOSVERSION = FLUX_REPOSVERSION;

	/**
	 * Application-specific configuration object.
	 *
	 * @access public
	 * @var Flux_Config
	 */
	public static $appConfig;

	/**
	 * Servers configuration object.
	 *
	 * @access public
	 * @var Flux_Config
	 */
	public static $serversConfig;

	/**
	 * Messages configuration object.
	 *
	 * @access public
	 * @var Flux_Config
	 */
	public static $messagesConfig;

	/**
	 * Collection of Flux_Athena objects.
	 *
	 * @access public
	 * @var array
	 */
	public static $servers = array();

	/**
	 * Registry where Flux_LoginAthenaGroup instances are kept for easy
	 * searching.
	 *
	 * @access public
	 * @var array
	 */
	public static $loginAthenaGroupRegistry = array();

	/**
	 * Registry where Flux_Athena instances are kept for easy searching.
	 *
	 * @access public
	 * @var array
	 */
	public static $athenaServerRegistry = array();

	/**
	 * Object containing all of Flux's session data.
	 *
	 * @access public
	 * @var Flux_SessionData
	 */
	public static $sessionData;

	/**
	 *
	 */
	public static $numberOfQueries = 0;

	/**
	 *
	 */
	public static $addons = array();

	/**
	 * Initialize Flux application. This will handle configuration parsing and
	 * instanciating of objects crucial to the control panel.
	 *
	 * @param array $options Options to pass to initializer.
	 * @throws Flux_Error Raised when missing required options.
	 * @access public
	 */
	public static function initialize($options = array())
	{
		$required = array('appConfigFile', 'serversConfigFile');
		foreach ($required as $option) {
			if (!array_key_exists($option, $options)) {
				self::raise("Missing required option `$option' in Flux::initialize()");
			}
		}

		// Parse application and server configuration files, this will also
		// handle configuration file normalization. See the source for the
		// below methods for more details on what's being done.
		self::$appConfig      = self::parseAppConfigFile($options['appConfigFile']);
		self::$serversConfig  = self::parseServersConfigFile($options['serversConfigFile']);

		if (array_key_exists('appConfigFileImport', $options) && file_exists($options['appConfigFileImport'])) {
			$importAppConfig = self::parseAppConfigFile($options['appConfigFileImport'], true);
			self::$appConfig->merge($importAppConfig, true, true);
		}

		// Server configuration files are not merged, instead they replace the original.
		if (array_key_exists('serversConfigFileImport', $options) && file_exists($options['serversConfigFileImport'])) {
			$importServersConfig = self::parseServersConfigFile($options['serversConfigFileImport'], true);
			self::$serversConfig = $importServersConfig;
		}

		// Using newer language system.
		self::$messagesConfig = self::parseLanguageConfigFile();

		// Initialize server objects.
		self::initializeServerObjects();

		// Initialize add-ons.
		self::initializeAddons();
	}

	/**
	 * Initialize each Login/Char/Map server object and contain them in their
	 * own collective Athena object.
	 *
	 * This is also part of the Flux initialization phase.
	 *
	 * @access public
	 */
	public static function initializeServerObjects()
	{
		foreach (self::$serversConfig->getChildrenConfigs() as $key => $config) {
			$connection  = new Flux_Connection($config->getDbConfig(), $config->getLogsDbConfig(), $config->getWebDbConfig());
			$loginServer = new Flux_LoginServer($config->getLoginServer());

			// LoginAthenaGroup maintains the grouping of a central login
			// server and its underlying Athena objects.
			self::$servers[$key] = new Flux_LoginAthenaGroup($config->getServerName(), $connection, $loginServer);

			// Add into registry.
			self::registerServerGroup($config->getServerName(), self::$servers[$key]);

			foreach ($config->getCharMapServers()->getChildrenConfigs() as $charMapServer) {
				$charServer = new Flux_CharServer($charMapServer->getCharServer());
				$mapServer  = new Flux_MapServer($charMapServer->getMapServer());

				// Create the collective server object, Flux_Athena.
				$athena = new Flux_Athena($charMapServer, $loginServer, $charServer, $mapServer);
				self::$servers[$key]->addAthenaServer($athena);

				// Add into registry.
				self::registerAthenaServer($config->getServerName(), $charMapServer->getServerName(), $athena);
			}
		}
	}

	/**
	 *
	 */
	public static function initializeAddons()
	{
		if (!is_dir(FLUX_ADDON_DIR)) {
			return false;
		}

		foreach (glob(FLUX_ADDON_DIR.'/*') as $addonDir) {
			if (is_dir($addonDir)) {
				$addonName   = basename($addonDir);
				$addonObject = new Flux_Addon($addonName, $addonDir);
				self::$addons[$addonName] = $addonObject;

				// Merge configurations.
				self::$appConfig->merge($addonObject->addonConfig);
				self::$messagesConfig->merge($addonObject->messagesConfig, false);
			}
		}
	}

	/**
	 * Wrapper method for setting and getting values from the appConfig.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param array $options
	 * @access public
	 */
	public static function config($key, $value = null, $options = array())
	{
		if (!is_null($value)) {
			return self::$appConfig->set($key, $value, $options);
		}
		else {
			return self::$appConfig->get($key);
		}
	}

	/**
	 * Wrapper method for setting and getting values from the messagesConfig.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param array $options
	 * @access public
	 */
	public static function message($key, $value = null, $options = array())
	{
		if (!is_null($value)) {
			return self::$messagesConfig->set($key, $value, $options);
		}
		if (!is_null($tmp=self::$messagesConfig->get($key)))
			return $tmp;
		else
			return ' '.$key;
	}

	/**
	 * Convenience method for raising Flux_Error exceptions.
	 *
	 * @param string $message Message to pass to constructor.
	 * @throws Flux_Error
	 * @access public
	 */
	public static function raise($message)
	{
		throw new Flux_Error($message);
	}

	/**
	 * Parse PHP array into Flux_Config instance.
	 *
	 * @param array $configArr
	 * @access public
	 */
	public static function parseConfig(array $configArr)
	{
		return new Flux_Config($configArr);
	}

	/**
	 * Parse a PHP array returned as the result of an included file into a
	 * Flux_Config configuration object.
	 *
	 * @param string $filename
	 * @access public
	 */
	public static function parseConfigFile($filename, $cache=true)
	{
		$basename  = basename(str_replace(' ', '', ucwords(str_replace(array('/', '\\', '_'), ' ', $filename))), '.php').'.cache.php';
		$cachefile = FLUX_DATA_DIR."/tmp/$basename";
		$directory = FLUX_DATA_DIR.'/tmp';
		if (!is_dir($directory))
			mkdir($directory, 0600);
		if ($cache && file_exists($cachefile) && filemtime($cachefile) > filemtime($filename)) {
			return unserialize(file_get_contents($cachefile, false, null, 28));
		}
		else {
			ob_start();
			// Uses require, thus assumes the file returns an array.
			$config = require $filename;
			ob_end_clean();

			// Cache config file.
			$cf = self::parseConfig($config);

			if ($cache) {
				$fp = fopen($cachefile, 'w');
				if ( !$fp ){
					self::raise("Failed to write ".$cachefile." permission error or data/tmp not exist in Flux::parseConfigFile()");
				}
				fwrite($fp, '<?php exit("Forbidden."); ?>');
				fwrite($fp, $s=serialize($cf), strlen($s));
				fclose($fp);
			}

			return $cf;
		}
	}

	/**
	 * Parse a file in an application-config specific manner.
	 *
	 * @param string $filename
	 * @param bool $import Whether this is an import config or not
	 * @access public
	 */
	public static function parseAppConfigFile($filename, $import = false)
	{
		$config = self::parseConfigFile($filename, false);

		if (!$config->getServerAddress() && !$import) {
			self::raise("ServerAddress must be specified in your application config.");
		}
		$themes = $config->get('ThemeName', false);
		if ((!$themes || count($themes) < 1) && !$import) {
			self::raise('ThemeName is required in application configuration.');
		}
		if ($themes) {
			foreach ($themes as $themeName) {
				if (!self::themeExists($themeName)) {
					self::raise("The selected theme '$themeName' does not exist.");
				}
			}
		}
		if (!($config->getPayPalReceiverEmails() instanceof Flux_Config)
			&& !($import && $config->getPayPalReceiverEmails() === null)) {
			self::raise("PayPalReceiverEmails must be an array.");
		}

		// Sanitize BaseURI. (leading forward slash is mandatory.)
		$baseURI = $config->get('BaseURI');
		if (!is_null($baseURI)) {
			if (strlen($baseURI) && $baseURI[0] != '/') {
				$config->set('BaseURI', "/$baseURI");
			}
			elseif (trim($baseURI) === '') {
				$config->set('BaseURI', '/');
			}
		}

		return $config;
	}

	/**
	 * Parse a file in a servers-config specific manner. This method gets a bit
	 * nasty so beware of ugly code ;)
	 *
	 * @param string $filename
	 * @param bool $import Whether this is an import config or not
	 * @access public
	 */
	public static function parseServersConfigFile($filename, $import = false)
	{
		$config            = self::parseConfigFile($filename);
		$options           = array('overwrite' => false, 'force' => true); // Config::set() options.
		$serverNames       = array();
		$athenaServerNames = array();

		if (!count($config->toArray()) && !$import) {
			self::raise('At least one server configuration must be present.');
		}

		foreach ($config->getChildrenConfigs() as $topConfig) {
			//
			// Top-level normalization.
			//

			if (!($serverName = $topConfig->getServerName())) {
				self::raise('ServerName is required for each top-level server configuration, check your servers configuration file.');
			}
			elseif (in_array($serverName, $serverNames)) {
				self::raise("The server name '$serverName' has already been configured. Please use another name.");
			}

			$serverNames[] = $serverName;
			$athenaServerNames[$serverName] = array();

			$topConfig->setDbConfig(array(), $options);
			$topConfig->setLogsDbConfig(array(), $options);
			$topConfig->setWebDbConfig(array(), $options);
			$topConfig->setLoginServer(array(), $options);
			$topConfig->setCharMapServers(array(), $options);

			$dbConfig     = $topConfig->getDbConfig();
			$logsDbConfig = $topConfig->getLogsDbConfig();
			$webDbConfig  = $topConfig->getWebDbConfig();
			$loginServer  = $topConfig->getLoginServer();

			foreach (array($dbConfig, $logsDbConfig, $webDbConfig) as $_dbConfig) {
				$_dbConfig->setHostname('localhost', $options);
				$_dbConfig->setUsername('ragnarok', $options);
				$_dbConfig->setPassword('ragnarok', $options);
				$_dbConfig->setPersistent(true, $options);
			}

			$loginServer->setDatabase($dbConfig->getDatabase(), $options);
			$loginServer->setUseMD5(true, $options);

			// Raise error if missing essential configuration directives.
			if (!$loginServer->getAddress()) {
				self::raise('Address is required for each LoginServer section in your servers configuration.');
			}
			elseif (!$loginServer->getPort()) {
				self::raise('Port is required for each LoginServer section in your servers configuration.');
			}

			if (!$topConfig->getCharMapServers() || !count($topConfig->getCharMapServers()->toArray())) {
				self::raise('CharMapServers must be an array and contain at least 1 char/map server entry.');
			}

			foreach ($topConfig->getCharMapServers()->getChildrenConfigs() as $charMapServer) {
				//
				// Char/Map normalization.
				//
				$expRates = array(
					'Base'        => 100,
					'Job'         => 100,
					'Mvp'         => 100
				);
				$dropRates = array(
					'DropRateCap' => 9000,
					'Common'      => 100,
					'CommonBoss'  => 100,
					'CommonMVP'   => 100,
					'CommonMin'   => 1,
					'CommonMax'   => 10000,
					'Heal'        => 100,
					'HealBoss'    => 100,
					'HealMVP'     => 100,
					'HealMin'     => 1,
					'HealMax'     => 10000,
					'Useable'     => 100,
					'UseableBoss' => 100,
					'UseableMVP'  => 100,
					'UseableMin'  => 1,
					'UseableMax'  => 10000,
					'Equip'       => 100,
					'EquipBoss'   => 100,
					'EquipMVP'    => 100,
					'EquipMin'    => 1,
					'EquipMax'    => 10000,
					'Card'        => 100,
					'CardBoss'    => 100,
					'CardMVP'     => 100,
					'CardMin'     => 1,
					'CardMax'     => 10000,
					'MvpItem'     => 100,
					'MvpItemMin'  => 1,
					'MvpItemMax'  => 10000,
					'MvpItemMode' => 0
				);
				$charMapServer->setExpRates($expRates, $options);
				$charMapServer->setDropRates($dropRates, $options);
				$charMapServer->setRenewal(true, $options);
				$charMapServer->setCharServer(array(), $options);
				$charMapServer->setMapServer(array(), $options);
				$charMapServer->setDatabase($dbConfig->getDatabase(), $options);

				if (!($athenaServerName = $charMapServer->getServerName())) {
					self::raise('ServerName is required for each CharMapServers pair in your servers configuration.');
				}
				elseif (in_array($athenaServerName, $athenaServerNames[$serverName])) {
					self::raise("The server name '$athenaServerName' under '$serverName' has already been configured. Please use another name.");
				}

				$athenaServerNames[$serverName][] = $athenaServerName;
				$charServer = $charMapServer->getCharServer();

				if (!$charServer->getAddress()) {
					self::raise('Address is required for each CharServer section in your servers configuration.');
				}
				elseif (!$charServer->getPort()) {
					self::raise('Port is required for each CharServer section in your servers configuration.');
				}

				$mapServer = $charMapServer->getMapServer();
				if (!$mapServer->getAddress()) {
					self::raise('Address is required for each MapServer section in your servers configuration.');
				}
				elseif (!$mapServer->getPort()) {
					self::raise('Port is required for each MapServer section in your servers configuration.');
				}
			}
		}

		return $config;
	}

	/**
	 * Parses a messages configuration file. (Deprecated)
	 *
	 * @param string $filename
	 * @access public
	 */
	public static function parseMessagesConfigFile($filename)
	{
		$config = self::parseConfigFile($filename);
		// Nothing yet.
		return $config;
	}

	/**
	 * Parses a language configuration file, can also parse a language config
	 * for any addon.
	 *
	 * @param string $addonName
	 * @access public
	 */
	public static function parseLanguageConfigFile($addonName=null)
	{
		$default = $addonName ? FLUX_ADDON_DIR."/$addonName/lang/en_us.php" : FLUX_LANG_DIR.'/en_us.php';
		$current = $default;

		if ($lang=self::config('DefaultLanguage')) {
			$current = $addonName ? FLUX_ADDON_DIR."/$addonName/lang/$lang.php" : FLUX_LANG_DIR."/$lang.php";
		}

		$languages = self::getAvailableLanguages();

		if(!empty($_COOKIE["language"]) && array_key_exists($_COOKIE["language"], $languages))
		{
			$lang = $_COOKIE["language"];
			$current = $addonName ? FLUX_ADDON_DIR."/$addonName/lang/$lang.php" : FLUX_LANG_DIR."/$lang.php";
		}

		if (file_exists($default)) {
			$def = self::parseConfigFile($default);
		}
		else {
			$tmp = array();
			$def = new Flux_Config($tmp);
		}

		if ($current != $default && file_exists($current)) {
			$cur = self::parseConfigFile($current);
			$def->merge($cur, false);
		}

		return $def;
	}

	/**
	 * Check whether or not a theme exists.
	 *
	 * @return bool
	 * @access public
	 */
	public static function themeExists($themeName)
	{
		return is_dir(FLUX_THEME_DIR."/$themeName");
	}

	/**
	 * Register the server group into the registry.
	 *
	 * @param string $serverName Server group's name.
	 * @param Flux_LoginAthenaGroup Server group object.
	 * @return Flux_LoginAthenaGroup
	 * @access private
	 */
	private static function registerServerGroup($serverName, Flux_LoginAthenaGroup $serverGroup)
	{
		self::$loginAthenaGroupRegistry[$serverName] = $serverGroup;
		return $serverGroup;
	}

	/**
	 * Register the Athena server into the registry.
	 *
	 * @param string $serverName Server group's name.
	 * @param string $athenaServerName Athena server's name.
	 * @param Flux_Athena $athenaServer Athena server object.
	 * @return Flux_Athena
	 * @access private
	 */
	private static function registerAthenaServer($serverName, $athenaServerName, Flux_Athena $athenaServer)
	{
		if (!array_key_exists($serverName, self::$athenaServerRegistry) || !is_array(self::$athenaServerRegistry[$serverName])) {
			self::$athenaServerRegistry[$serverName] = array();
		}

		self::$athenaServerRegistry[$serverName][$athenaServerName] = $athenaServer;
		return $athenaServer;
	}

	/**
	 * Get Flux_LoginAthenaGroup server object by its ServerName.
	 *
	 * @param string $serverName Server group name.
	 * @return mixed Returns Flux_LoginAthenaGroup instance or false on failure.
	 * @access public
	 */
	public static function getServerGroupByName($serverName)
	{
		$registry = &self::$loginAthenaGroupRegistry;

		if (array_key_exists($serverName, $registry) && $registry[$serverName] instanceOf Flux_LoginAthenaGroup) {
			return $registry[$serverName];
		}
		else {
			return false;
		}
	}

	/**
	 * Get Flux_Athena instance by its group/server names.
	 *
	 * @param string $serverName Server group name.
	 * @param string $athenaServerName Athena server name.
	 * @return mixed Returns Flux_Athena instance or false on failure.
	 * @access public
	 */
	public static function getAthenaServerByName($serverName, $athenaServerName)
	{
		$registry = &self::$athenaServerRegistry;
		if (array_key_exists($serverName, $registry) && array_key_exists($athenaServerName, $registry[$serverName]) &&
			$registry[$serverName][$athenaServerName] instanceOf Flux_Athena) {

			return $registry[$serverName][$athenaServerName];
		}
		else {
			return false;
		}
	}

	/**
	 * Hashes a password for use in comparison with the login.user_pass column.
	 *
	 * @param string $password Plain text password.
	 * @return string Returns hashed password.
	 * @access public
	 */
	public static function hashPassword($password)
	{
		// Default hashing schema is MD5.
		return md5($password);
	}

	/**
	 * Get the job class name from a job ID.
	 *
	 * @param int $id
	 * @return mixed Job class or false.
	 * @access public
	 */
	public static function getJobClass($id)
	{
		$key   = "JobClasses.$id";
		$class = self::config($key);

		if ($class) {
			return $class;
		}
		else {
			return false;
		}
	}

	/**
	 * Get the job ID from a job class name.
	 *
	 * @param string $class
	 * @return mixed Job ID or false.
	 * @access public
	 */
	public static function getJobID($class)
	{
		$index = self::config('JobClassIndex')->toArray();
		if (array_key_exists($class, $index)) {
			return $index[$class];
		}
		else {
			return false;
		}
	}

	/**
	 * Get the homunculus class name from a homun class ID.
	 *
	 * @param int $id
	 * @return mixed Class name or false.
	 * @access public
	 */
	public static function getHomunClass($id)
	{
		$key   = "HomunClasses.$id";
		$class = self::config($key);

		if ($class) {
			return $class;
		}
		else {
			return false;
		}
	}

	/**
	 * Get the item type name from an item type.
	 *
	 * @return Item Type or false.
	 * @access public
	 */
	public static function getItemType($id1)
	{
		if (is_null($id1))
			return false;

		$type = self::config("ItemTypes")->toArray();

		if ($type[strtolower($id1)] != NULL) {
			return $type[strtolower($id1)];
		}
		else {
			return false;
		}
	}
	public static function getItemSubType($id1, $id2)
	{
		$subtype = "ItemSubTypes.$id1.$id2";
		$result = self::config($subtype);

		if ($result) {
			return $result;
		}
		else {
			return false;
		}
	}

	/**
	 * return random option description.
	 */
	public static function getRandOption($id1)
	{
		$key   = "RandomOptions.$id1";
		$option = self::config($key);

		if ($option) {
			return $option;
		}
		else {
			return false;
		}
	}

	/**
	 * Get the equip location combination name from an equip location combination type.
	 *
	 * @param int $id
	 * @return mixed Equip Location Combination or false.
	 * @access public
	 */
	public static function getEquipLocationCombination()
	{
		$equiplocations = Flux::config('EquipLocationCombinations')->toArray();
		return $equiplocations;
	}

	/**
	 * Process donations that have been put on hold.
	 */
	public static function processHeldCredits()
	{
		$txnLogTable            = self::config('FluxTables.TransactionTable');
		$trustTable             = self::config('FluxTables.DonationTrustTable');
		$loginAthenaGroups      = self::$loginAthenaGroupRegistry;
		list ($cancel, $accept) = array(array(), array());

		foreach ($loginAthenaGroups as $loginAthenaGroup) {
			$sql  = "SELECT account_id, payer_email, credits, mc_gross, txn_id, hold_until ";
			$sql .= "FROM {$loginAthenaGroup->loginDatabase}.$txnLogTable ";
			$sql .= "WHERE account_id > 0 AND hold_until IS NOT NULL AND payment_status = 'Completed'";
			$sth  = $loginAthenaGroup->connection->getStatement($sql);

			if ($sth->execute() && ($txn=$sth->fetchAll())) {
				foreach ($txn as $t) {
					$sql  = "SELECT id FROM {$loginAthenaGroup->loginDatabase}.$txnLogTable ";
					$sql .= "WHERE payment_status IN ('Cancelled_Reversed', 'Reversed', 'Refunded') AND parent_txn_id = ? LIMIT 1";
					$sth  = $loginAthenaGroup->connection->getStatement($sql);

					if ($sth->execute(array($t->txn_id)) && ($r=$sth->fetch()) && $r->id) {
						$cancel[] = $t->txn_id;
					}
					elseif (strtotime($t->hold_until) <= time()) {
						$accept[] = $t;
					}
				}
			}

			if (!empty($cancel)) {
				$ids  = implode(', ', array_fill(0, count($cancel), '?'));
				$sql  = "UPDATE {$loginAthenaGroup->loginDatabase}.$txnLogTable ";
				$sql .= "SET credits = 0, hold_until = NULL WHERE txn_id IN ($ids)";
				$sth  = $loginAthenaGroup->connection->getStatement($sql);
				$sth->execute($cancel);
			}

			$sql2   = "INSERT INTO {$loginAthenaGroup->loginDatabase}.$trustTable (account_id, email, create_date)";
			$sql2  .= "VALUES (?, ?, NOW())";
			$sth2   = $loginAthenaGroup->connection->getStatement($sql2);

			$sql3   = "SELECT id FROM {$loginAthenaGroup->loginDatabase}.$trustTable WHERE ";
			$sql3  .= "delete_date IS NULL AND account_id = ? AND email = ? LIMIT 1";
			$sth3   = $loginAthenaGroup->connection->getStatement($sql3);

			$idvals = array();

			foreach ($accept as $txn) {
				$loginAthenaGroup->loginServer->depositCredits($txn->account_id, $txn->credits, $txn->mc_gross);
				$sth3->execute(array($txn->account_id, $txn->payer_email));
				$row = $sth3->fetch();

				if (!$row) {
					$sth2->execute(array($txn->account_id, $txn->payer_email));
				}

				$idvals[] = $txn->txn_id;
			}

			if (!empty($idvals)) {
				$ids  = implode(', ', array_fill(0, count($idvals), '?'));
				$sql  = "UPDATE {$loginAthenaGroup->loginDatabase}.$txnLogTable ";
				$sql .= "SET hold_until = NULL WHERE txn_id IN ($ids)";
				$sth  = $loginAthenaGroup->connection->getStatement($sql);

				$sth->execute($idvals);
			}
		}
	}

	/**
	 *
	 */
	public static function pruneUnconfirmedAccounts()
	{
		$tbl    = Flux::config('FluxTables.AccountCreateTable');

		foreach (self::$loginAthenaGroupRegistry as $loginAthenaGroup) {
			$db   = $loginAthenaGroup->loginDatabase;
			$sql  = "DELETE $db.login, $db.$tbl FROM $db.login INNER JOIN $db.$tbl ";
			$sql .= "WHERE login.account_id = $tbl.account_id AND $tbl.confirmed = 0 ";
			$sql .= "AND $tbl.confirm_code IS NOT NULL AND $tbl.confirm_expire <= NOW()";
			$sth  = $loginAthenaGroup->connection->getStatement($sql);

			$sth->execute();
		}
	}

	/**
	 * Get array of equip_location bits. (bit => loc_name pairs)
	 * @return array
	 */
	public static function getEquipLocationList()
	{
		$equiplocations = Flux::config('EquipLocations')->toArray();
		return $equiplocations;
	}

	/**
	 * Get array of equip_upper bits. (bit => upper_name pairs)
	 * @return array
	 */
	public static function getEquipUpperList($isRenewal = 1)
	{
		$equipupper = Flux::config('EquipUpper.0')->toArray();

		if($isRenewal)
			$equipupper = array_merge($equipupper, Flux::config('EquipUpper.1')->toArray());

		return $equipupper;
	}

	/**
	 * Get array of equip_jobs bits. (bit => job_name pairs)
	 */
	public static function getEquipJobsList($isRenewal = 1)
	{
		$equipjobs = Flux::config('EquipJobs.0')->toArray();

		if($isRenewal)
			$equipjobs = array_merge($equipjobs, Flux::config('EquipJobs.1')->toArray());

		return $equipjobs;
	}

	/**
	 * Get array of trade restrictions
	 */
	public static function getTradeRestrictionList()
	{
		$restrictions = Flux::config('TradeRestriction')->toArray();
		return $restrictions;
	}

	/**
	 * Get array of item flags
	 */
	public static function getItemFlagList()
	{
		$flags = Flux::config('ItemFlags')->toArray();
		return $flags;
	}

	/**
	 * Check whether a particular item type is stackable.
	 * @param int $type
	 * @return bool
	 */
	public static function isStackableItemType($type)
	{
		$nonstackables = array(1, 4, 5, 7, 8, 9);
		return !in_array($type, $nonstackables);
	}

	/**
	 * Perform a bitwise AND from each bit in getEquipUpperList() on $bitmask
	 * to determine which bits have been set.
	 * @param int $bitmask
	 * @return array
	 */
	public static function equipUpperToArray($bitmask, $isRenewal = 1)
	{
		$arr  = array();
		$bits = self::getEquipUpperList($isRenewal);

		foreach ($bits as $bit => $name) {
			if ($bitmask & $bit) {
				$arr[] = $bit;
			}
		}

		return $arr;
	}

	/**
	 * Perform a bitwise AND from each bit in getEquipJobsList() on $bitmask
	 * to determine which bits have been set.
	 * @param int $bitmask
	 * @return array
	 */
	public static function equipJobsToArray($bitmask)
	{
		$arr  = array();
		$bits = self::getEquipJobsList();

		foreach ($bits as $bit => $name) {
			if ($bitmask & $bit) {
				$arr[] = $bit;
			}
		}

		return $arr;
	}

	/**
	 *
	 */
	public static function monsterModeToArray($bitmask)
	{
		$arr  = array();
		$bits = self::config('MonsterModes')->toArray();

		foreach ($bits as $name) {
				$arr[] = $name;
		}

		return $arr;
	}

	/**
	 *
	 */
	public static function elementName($ele)
	{
		$element = Flux::config("Elements")->toArray();
		return is_null($element[$ele]) ? $element['Neutral'] : $element[$ele];
	}

	/**
	 *
	 */
	public static function monsterRaceName($race)
	{
		$races = Flux::config("MonsterRaces")->toArray();
		return is_null($races[$race]) ? $races['Formless'] : $races[$race];
	}

	/**
	 *
	 */
	public static function monsterSizeName($size)
	{
		$sizes = Flux::config("MonsterSizes")->toArray();
		return is_null($sizes[$size]) ? $sizes['Small'] : $sizes[$size];
	}

	public static function getAvailableLanguages()
	{
		$langs_available = array_diff(scandir(FLUX_LANG_DIR), array('..', '.'));

		$dictionary = [];
		foreach($langs_available as $lang_file) {
			$lang_key = str_replace('.php', '', $lang_file);
			$lang_conf = self::parseConfigFile(FLUX_LANG_DIR.'/'.$lang_file);
			$lang_name = $lang_conf->get('Language');

			$dictionary[$lang_key] = $lang_name;
		}

		return $dictionary;
	}
}
?>
