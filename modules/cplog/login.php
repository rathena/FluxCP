<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'List Logins';

$loginLogTable = Flux::config('FluxTables.LoginLogTable');
$sqlpartial    = "WHERE 1=1 ";
$bind          = array();

$password    = $params->get('password');
$accountID   = (int)$params->get('account_id');
$username    = trim($params->get('username') ?: '');
$ipAddress   = trim($params->get('ip') ?: '');
$loginAfter  = $params->get('login_after_date');
$loginBefore = $params->get('login_before_date');
$errorCode   = $params->get('error_code');

if ($password && $auth->allowedToSearchCpLoginLogPw) {
	$sqlpartial .= 'AND password = ? ';
	$bind[]      = $session->loginAthenaGroup->loginServer->config->getUseMD5() ? md5($password) : $password;
}

if ($accountID) {
	$sqlpartial .= 'AND account_id = ? ';
	$bind[]      = $accountID;
}

if ($username) {
	$sqlpartial .= 'AND username LIKE ? ';
	$bind[]      = "%$username%";
}

if ($ipAddress) {
	$sqlpartial .= 'AND ip LIKE ? ';
	$bind[]      = "%$ipAddress%";
}

if ($loginAfter) {
	$sqlpartial .= 'AND login_date >= ? ';
	$bind[]      = $loginAfter;
}

if ($loginBefore) {
	$sqlpartial .= 'AND login_date <= ? ';
	$bind[]      = $loginBefore;
}

if (!is_null($errorCode) && strtolower($errorCode) != 'all') {
	if (strtolower($errorCode) == 'none') {
		$sqlpartial .= 'AND error_code IS NULL ';
	}
	else {
		$sqlpartial .= 'AND error_code = ? ';
		$bind[]      = $errorCode;
	}
}

$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$loginLogTable $sqlpartial";
$sth = $server->connection->getStatement($sql);
$sth->execute($bind);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'account_id', 'username', 'password', 'ip',
	'login_date' => 'desc', 'error_code'
));

$sql = "SELECT account_id, username, password, ip, login_date, error_code FROM {$server->loginDatabase}.$loginLogTable $sqlpartial";
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatement($sql);
$sth->execute($bind);

$logins = $sth->fetchAll();
$loginErrors = Flux::config('LoginErrors');

if ($logins) {
	foreach ($logins as $_tmplogin) {
		$_tmplogin->error_type = $loginErrors->get($_tmplogin->error_code);
		if (is_null($_tmplogin->error_type)) {
			$_tmplogin->error_type = $_tmplogin->error_code;
		}
	}
}
?>
