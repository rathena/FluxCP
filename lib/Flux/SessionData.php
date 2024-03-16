<?php
require_once 'Flux/DataObject.php';
require_once 'Flux/ItemShop/Cart.php';
require_once 'Flux/LoginError.php';

/**
 * Contains all of Flux's session data.
 */
class Flux_SessionData {
	/**
	 * Actual session data array.
	 *
	 * @access private
	 * @var array
	 */
	private $sessionData;
	
	/**
	 * Session data filters.
	 *
	 * @access private
	 * @var array
	 */
	private $dataFilters = array();
	
	/**
	 * Selected login server group.
	 *
	 * @access public
	 * @var Flux_LoginAthenaGroup
	 */
	public $loginAthenaGroup;
	
	/**
	 * Selected login server.
	 *
	 * @access public
	 * @var Flux_LoginServer
	 */
	public $loginServer;
	
	/**
	 * Account object.
	 *
	 * @access public
	 * @var Flux_DataObject
	 */
	public $account;

	
	/**
	 * Create new SessionData instance.
	 *
	 * @param array $sessionData
	 * @access public
	 */
	public function __construct(array &$sessionData, $logout = false)
	{
		$this->sessionData = &$sessionData;
		if ($logout) {
			$this->logout();
		}
		else {
			$this->initialize();
		}
	}
	
	/**
	 * Initialize session data.
	 *
	 * @param bool $force
	 * @return bool
	 * @access private
	 */
	private function initialize($force = false)
	{	
		$keysToInit = array('username', 'serverName', 'athenaServerName', 'securityCode', 'theme');
		foreach ($keysToInit as $key) {
			if ($force || !$this->{$key}) {
				$method = ucfirst($key);
				$method = "set{$method}Data";
				$this->$method(null);
			}
		}

		$loggedIn = true;
		if (!$this->username) {
			$loggedIn = false;
			$cfgAthenaServerName = Flux::config('DefaultCharMapServer');
			$cfgLoginAthenaGroup = Flux::config('DefaultLoginGroup');
			
			if (Flux::getServerGroupByName($cfgLoginAthenaGroup)){
				$this->setServerNameData($cfgLoginAthenaGroup);
			}
			else {
				$defaultServerName = current(array_keys(Flux::$loginAthenaGroupRegistry));
				$this->setServerNameData($defaultServerName);
			}
		}
		
		
		if ($this->serverName && ($this->loginAthenaGroup = Flux::getServerGroupByName($this->serverName))) {
			$this->loginServer = $this->loginAthenaGroup->loginServer;
			
			if (!$loggedIn && $cfgAthenaServerName && $this->getAthenaServer($cfgAthenaServerName)) {
				$this->setAthenaServerNameData($cfgAthenaServerName);
			}
			
			if (!$this->athenaServerName || ((!$loggedIn && !$this->getAthenaServer($cfgAthenaServerName)) || !$this->getAthenaServer($this->athenaServerName))) {
				$this->setAthenaServerNameData(current($this->getAthenaServerNames()));
			}
		}
		
		// Get new account data every request.
		if ($this->loginAthenaGroup && $this->username && ($account = $this->getAccount($this->loginAthenaGroup, $this->username))) {	
			$this->account = $account;
			$this->account->group_level = AccountLevel::getGroupLevel($account->group_id);
			
			// Automatically log out of account when detected as banned.
			$permBan = ($account->state == 5 && !Flux::config('AllowPermBanLogin'));
			$tempBan = (($account->unban_time > 0 && $account->unban_time < time()) && !Flux::config('AllowTempBanLogin'));
			
			if ($permBan || $tempBan) {
				$this->logout();
			}
		}
		else {
			$this->account = new Flux_DataObject(null, array('group_level' => AccountLevel::UNAUTH));
		}
		
		if (!is_array($this->cart)) {
			$this->setCartData(array());
		}
		
		if ($this->account->account_id && $this->loginAthenaGroup) {
			if (!array_key_exists($this->loginAthenaGroup->serverName, $this->cart)) {
				$this->cart[$this->loginAthenaGroup->serverName] = array();
			}

			foreach ($this->getAthenaServerNames() as $athenaServerName) {
				$athenaServer = $this->getAthenaServer($athenaServerName);
				$cartArray    = &$this->cart[$this->loginAthenaGroup->serverName];
				$accountID    = $this->account->account_id;
				
				if (!array_key_exists($accountID, $cartArray)) {
					$cartArray[$accountID] = array();
				}
				
				if (!array_key_exists($athenaServerName, $cartArray[$accountID])) {
					$cartArray[$accountID][$athenaServerName] = new Flux_ItemShop_Cart();
				}
				$cartArray[$accountID][$athenaServerName]->setAccount($this->account);
				$athenaServer->setCart($cartArray[$accountID][$athenaServerName]);
			}
		}
		
		if (!$this->theme || $this->theme === 'installer') { // always update if coming from installer
			$this->setThemeData(Flux::config('ThemeName.0'));
		}

		return true;
	}
	
	/**
	 * Log current user out.
	 * 
	 * @return bool
	 * @access public
	 */
	public function logout()
	{
		$this->loginAthenaGroup = null;
		$this->loginServer = null;
		return $this->initialize(true);
	}
	
	public function __call($method, $args)
	{
		if (count($args) && preg_match('/set(.+?)Data/', $method, $m)) {
			$arg     = current($args);
			$meth    = $m[1];
			$meth[0] = strtolower($meth[0]);
			
			if (array_key_exists($meth, $this->dataFilters)) {
				foreach ($this->dataFilters[$meth] as $callback) {
					$arg = call_user_func($callback, $arg);
				}
			}
			
			$this->sessionData[$meth] = $arg;
		}
	}
	
	public function &__get($prop)
	{
		$value = null;
		if (array_key_exists($prop, $this->sessionData)) {
			$value = &$this->sessionData[$prop];
		}
		return $value;
	}
	
	/**
	 * Set session data.
	 *
	 * @param array $keys Session keys to be affected.
	 * @param mixed $value Value to be assigned to all specified keys.
	 * @return mixed whatever was set
	 * @access public
	 */
	public function setData(array $keys, $value)
	{
		foreach ($keys as $key) {
			$key = ucfirst($key);
			$this->{"set{$key}Data"}($value);
		}
		return $value;
	}
	
	/**
	 * Add a session data setter filter.
	 *
	 * @param string $key Which session key
	 * @param string $callback Function callback.
	 * @return string Callback
	 * @access public
	 */
	public function addDataFilter($key, $callback)
	{
		if (!array_key_exists($key, $this->dataFilters)) {
			$this->dataFilters[$key] = array();
		}
		
		$this->dataFilters[$key][] = $callback;
		return $callback;
	}
	
	/**
	 * Checks whether the current user is logged in.
	 */
	public function isLoggedIn()
	{
		return $this->account->group_level >= AccountLevel::NORMAL;
	}
	
	/**
	 * Check securityCode from user with $this->securityCode or reCaptcha
	 * @param $securityCode Code from user
	 * @param $recaptcha True if check using recaptcha
	 * @return true on success and false on failure
	 */
	public function checkSecurityCode($securityCode, $recaptcha = false) {
		if ($recaptcha) {
			if (Flux::config('ReCaptchaPrivateKey') && Flux::config('ReCaptchaPublicKey')) {
				$responseKeys = array();
				if (isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response'] != "") {
					$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".Flux::config('ReCaptchaPrivateKey')."&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']);
					$responseKeys = json_decode($response,true);
				}
				if (count($responseKeys) && intval($responseKeys["success"]) == 1) {
					return true;
				}
			}
		}
		else if ($securityCode && !empty($securityCode) && strtolower($securityCode) == strtolower($this->securityCode)) {
			return true;
		}
		return false;
	}
	
	/**
	 * User login.
	 *
	 * @param string $server Server name
	 * @param string $username
	 * @param string $password
	 * @throws Flux_LoginError
	 * @access public
	 */
	public function login($server, $username, $password, $securityCode = null)
	{
		$loginAthenaGroup = Flux::getServerGroupByName($server);
		if (!$loginAthenaGroup) {
			throw new Flux_LoginError('Invalid server.', Flux_LoginError::INVALID_SERVER);
		}
		
		if ($loginAthenaGroup->loginServer->isIpBanned() && !Flux::config('AllowIpBanLogin')) {
			throw new Flux_LoginError('IP address is banned', Flux_LoginError::IPBANNED);
		}
		
		if (Flux::config('UseLoginCaptcha') && !self::checkSecurityCode($securityCode, Flux::config('EnableReCaptcha'))) {
			throw new Flux_LoginError('Invalid security code', Flux_LoginError::INVALID_SECURITY_CODE);
		}
		
		if (!$loginAthenaGroup->isAuth($username, $password)) {
			throw new Flux_LoginError('Invalid login', Flux_LoginError::INVALID_LOGIN);
		}
		
		$creditsTable  = Flux::config('FluxTables.CreditsTable');
		$creditColumns = 'credits.balance, credits.last_donation_date, credits.last_donation_amount';
		
		$sql  = "SELECT login.*, {$creditColumns} FROM {$loginAthenaGroup->loginDatabase}.login ";
		$sql .= "LEFT OUTER JOIN {$loginAthenaGroup->loginDatabase}.{$creditsTable} AS credits ON login.account_id = credits.account_id ";
		$sql .= "WHERE login.sex != 'S' AND login.group_id >= 0 AND login.userid = ? LIMIT 1";
		$smt  = $loginAthenaGroup->connection->getStatement($sql);
		$res  = $smt->execute(array($username));
		
		if ($res && ($row = $smt->fetch())) {
			if ($row->unban_time > 0) {
				if (time() >= $row->unban_time) {
					$row->unban_time = 0;
					$sql = "UPDATE {$loginAthenaGroup->loginDatabase}.login SET unban_time = 0 WHERE account_id = ?";
					$sth = $loginAthenaGroup->connection->getStatement($sql);
					$sth->execute(array($row->account_id));
				}
				elseif (!Flux::config('AllowTempBanLogin')) {
					throw new Flux_LoginError('Temporarily banned', Flux_LoginError::BANNED);
				}
			}
			if ($row->state == 5) {
				$createTable = Flux::config('FluxTables.AccountCreateTable');
				$sql  = "SELECT id FROM {$loginAthenaGroup->loginDatabase}.$createTable ";
				$sql .= "WHERE account_id = ? AND confirmed = 0";
				$sth  = $loginAthenaGroup->connection->getStatement($sql);
				$sth->execute(array($row->account_id));
				$row2 = $sth->fetch();
				
				if ($row2 && $row2->id) {
					throw new Flux_LoginError('Pending confirmation', Flux_LoginError::PENDING_CONFIRMATION);
				}
			}
			if (!Flux::config('AllowPermBanLogin') && $row->state == 5) {
				throw new Flux_LoginError('Permanently banned', Flux_LoginError::PERMABANNED);
			}
			
			$this->setServerNameData($server);
			$this->setUsernameData($username);
			$this->initialize(false);
		}
		else {
			$message  = "Unexpected error during login.\n";
			$message .= 'PDO error info, if any: '.print_r($smt->errorInfo(), true);
			throw new Flux_LoginError($message, Flux_LoginError::UNEXPECTED);
		}
		
		return true;
	}
	
	/**
	 * Get account object for a particular user name.
	 *
	 * @param Flux_LoginAthenaGroup $loginAthenaGroup
	 * @param string $username
	 * @return mixed
	 * @access private
	 */
	private function getAccount(Flux_LoginAthenaGroup $loginAthenaGroup, $username)
	{
		$creditsTable  = Flux::config('FluxTables.CreditsTable');
		$creditColumns = 'credits.balance, credits.last_donation_date, credits.last_donation_amount';
		
		$sql  = "SELECT login.*, {$creditColumns} FROM {$loginAthenaGroup->loginDatabase}.login ";
		$sql .= "LEFT OUTER JOIN {$loginAthenaGroup->loginDatabase}.{$creditsTable} AS credits ON login.account_id = credits.account_id ";
		$sql .= "WHERE login.sex != 'S' AND login.group_id >= 0 AND login.userid = ? LIMIT 1";
		$smt  = $loginAthenaGroup->connection->getStatement($sql);
		$res  = $smt->execute(array($username));
		
		if ($res && ($row = $smt->fetch())) {
			return $row;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Get available server names.
	 *
	 * @access public
	 */
	public function getAthenaServerNames()
	{
		if ($this->loginAthenaGroup) {
			$names = array();
			foreach ($this->loginAthenaGroup->athenaServers as $server) {
				$names[] = $server->serverName;
			}
			return $names;
		}
		else {
			return array();
		}
	}
	
	/**
	 * Get a Flux_Athena instance by its name based on current server settings.
	 * 
	 * @param string $name
	 * @access public
	 */
	public function getAthenaServer($name = null)
	{
		if (is_null($name) && $this->athenaServerName) {
			return $this->getAthenaServer($this->athenaServerName);
		}
		
		if ($this->loginAthenaGroup && ($server = Flux::getAthenaServerByName($this->serverName, $name))) {
			return $server;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Get flash message.
	 *
	 * @return string
	 * @access public
	 */
	public function getMessage()
	{
		$message = $this->message;
		$this->setMessageData(null);
		return $message;
	}
}
?>
