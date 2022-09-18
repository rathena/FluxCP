<?php
if (version_compare(PHP_VERSION, '5.2.1', '<')) {
	echo '<h2>Error</h2>';
	echo '<p>PHP 5.2.1 or higher is required to use Flux Control Panel.</p>';
	echo '<p>You are running '.PHP_VERSION.'</p>';
	exit;
}

// Time started.
define('__START__', microtime(true));

define('FLUX_ROOT',			str_replace('\\', '/', dirname(__FILE__)));
define('FLUX_DATA_DIR',		'data');
define('FLUX_CONFIG_DIR',	'config');
define('FLUX_LIB_DIR',		'lib');
define('FLUX_MODULE_DIR',	'modules');
define('FLUX_THEME_DIR',	'themes');
define('FLUX_ADDON_DIR',	'addons');
define('FLUX_LANG_DIR',		'lang');

// Clean GPC arrays in the event magic_quotes_gpc is enabled.
if (ini_get('magic_quotes_gpc')) {
	$gpc = array(&$_GET, &$_POST, &$_REQUEST, &$_COOKIE);
	foreach ($gpc as &$arr) {
		foreach ($arr as $key => $value) {
			if (is_string($value)) {
				$arr[$key] = stripslashes($value);
			}
		}
	}
}

set_include_path(FLUX_LIB_DIR.PATH_SEPARATOR.get_include_path());

// Default account group IDs.
require_once FLUX_CONFIG_DIR.'/groups.php';

// Some necessary Flux core libraries.
require_once 'Flux.php';
require_once 'Flux/Dispatcher.php';
require_once 'Flux/SessionData.php';
require_once 'Flux/DataObject.php';
require_once 'Flux/Authorization.php';
require_once 'Flux/Installer.php';
require_once 'Flux/PermissionError.php';

// Vendor libraries.

try {
	if (!extension_loaded('pdo')) {
		throw new Flux_Error('The PDO extension is required to use Flux, please make sure it is installed along with the PDO_MYSQL driver.');
	}
	elseif (!extension_loaded('pdo_mysql')) {
		throw new Flux_Error('The PDO_MYSQL driver for the PDO extension must be installed to use Flux.  Please consult the PHP manual for installation instructions.');
	}

	// Initialize Flux.
	Flux::initialize(array(
		'appConfigFile'      => FLUX_CONFIG_DIR.'/application.php',
		'serversConfigFile'  => FLUX_CONFIG_DIR.'/servers.php',
	));

	// Set time limit.
	set_time_limit((int)Flux::config('ScriptTimeLimit'));

	// Set default timezone for entire app.
	$timezone = Flux::config('DateDefaultTimezone');
	if ($timezone && !@date_default_timezone_set($timezone)) {
		throw new Flux_Error("'$timezone' is not a valid timezone.  Consult http://php.net/timezones for a list of valid timezones.");
	}

	// Create some basic directories.
	$directories = array(
		FLUX_DATA_DIR.'/logs/schemas',
		FLUX_DATA_DIR.'/logs/schemas/logindb',
		FLUX_DATA_DIR.'/logs/schemas/charmapdb',
		FLUX_DATA_DIR.'/logs/transactions',
		FLUX_DATA_DIR.'/logs/mail',
		FLUX_DATA_DIR.'/logs/mysql',
		FLUX_DATA_DIR.'/logs/mysql/errors',
		FLUX_DATA_DIR.'/logs/errors',
		FLUX_DATA_DIR.'/logs/errors/exceptions',
		FLUX_DATA_DIR.'/logs/errors/mail',
	);

	// Schema log directories.
	foreach (Flux::$loginAthenaGroupRegistry as $serverName => $loginAthenaGroup) {
		$directories[] = FLUX_DATA_DIR."/logs/schemas/logindb/$serverName";
		$directories[] = FLUX_DATA_DIR."/logs/schemas/charmapdb/$serverName";

		foreach ($loginAthenaGroup->athenaServers as $athenaServer)
			$directories[] = FLUX_DATA_DIR."/logs/schemas/charmapdb/$serverName/{$athenaServer->serverName}";
	}

	foreach ($directories as $directory) {
		if (is_writable(dirname($directory)) && !is_dir($directory)) {
			if (Flux::config('RequireOwnership'))
				mkdir($directory, 0700);
			else
				mkdir($directory, 0777);
		}
	}

	if (Flux::config('RequireOwnership') && function_exists('posix_getuid'))
		$uid = posix_getuid();

	$directories = array(
		FLUX_DATA_DIR.'/logs'		=> 'log storage',
		FLUX_DATA_DIR.'/itemshop'	=> 'item shop image',
		FLUX_DATA_DIR.'/tmp'		=> 'temporary'
	);

	foreach ($directories as $directory => $directoryFunction) {
		$directory = realpath($directory);
		if (!is_writable($directory))
			throw new Flux_PermissionError("The $directoryFunction directory '$directory' is not writable.  Remedy with `chmod 0600 $directory`");
		if (Flux::config('RequireOwnership') && function_exists('posix_getuid') && fileowner($directory) != $uid)
			throw new Flux_PermissionError("The $directoryFunction directory '$directory' is not owned by the executing user.  Remedy with `chown -R ".posix_geteuid().":".posix_geteuid()." $directory`");
	}

	if (ini_get('session.use_trans_sid'))
		throw new Flux_Error("The 'session.use_trans_sid' php.ini configuration must be turned off for Flux to work.");

	// Installer library.
	$installer = Flux_Installer::getInstance();
	if ($hasUpdates=$installer->updateNeeded())
		Flux::config('ThemeName', array('installer'));

	$sessionKey = Flux::config('SessionKey');
	$sessionExpireDuration = Flux::config('SessionCookieExpire') * 60 * 60;
	session_set_cookie_params($sessionExpireDuration, Flux::config('BaseURI'));
	ini_set('session.gc_maxlifetime', $sessionExpireDuration);
	ini_set('session.name', $sessionKey);
	@session_start();

	if (empty($_SESSION[$sessionKey]) || !is_array($_SESSION[$sessionKey])) {
		$_SESSION[$sessionKey] = array();
	}

	// Initialize session data.
	Flux::$sessionData = new Flux_SessionData($_SESSION[$sessionKey], $hasUpdates);

	// Initialize authorization component.
	$accessConfig = Flux::parseConfigFile(FLUX_CONFIG_DIR.'/access.php');

	// Merge with add-on configs.
	foreach (Flux::$addons as $addon) {
		$accessConfig->merge($addon->accessConfig);
	}

	$accessConfig->set('unauthorized.index', AccountLevel::ANYONE);
	$authComponent = Flux_Authorization::getInstance($accessConfig, Flux::$sessionData);

	if (Flux::config('DebugMode')) {
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
	}

	// Dispatch requests->modules->actions->views.
	$dispatcher = Flux_Dispatcher::getInstance();
	$dispatcher->setDefaultModule(Flux::config('DefaultModule'));
	$dispatcher->dispatch(array(
		'basePath'					=> Flux::config('BaseURI'),
		'useCleanUrls'				=> Flux::config('UseCleanUrls'),
		'modulePath'				=> FLUX_MODULE_DIR,
		'themePath'					=> FLUX_THEME_DIR,
		'themeName'                 => Flux::$sessionData->theme,
		'missingActionModuleAction'	=> Flux::config('DebugMode') ? array('errors', 'missing_action') : array('main', 'page_not_found'),
		'missingViewModuleAction'	=> Flux::config('DebugMode') ? array('errors', 'missing_view')   : array('main', 'page_not_found')
	));
}
catch (Exception $e) {
	$exceptionDir = FLUX_DATA_DIR.'/logs/errors/exceptions';
	if (is_writable($exceptionDir)) {
		require_once 'Flux/LogFile.php';
		$today = date('Ymd');
		$eLog  = new Flux_LogFile("$exceptionDir/$today.log");

		// Log exception.
		$eLog->puts('(%s) Exception %s: %s', get_class($e), get_class($e), $e->getMessage());
		foreach (explode("\n", $e->getTraceAsString()) as $traceLine) {
			$eLog->puts('(%s) **TRACE** %s', get_class($e), $traceLine);
		}
	}

	require_once FLUX_CONFIG_DIR.'/error.php';
	define('__ERROR__', 1);
	include $errorFile;
}
?>
