<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Account Registrations';

$loginLogTable = Flux::config('FluxTables.AccountCreateTable');
$sqlpartial    = "WHERE 1=1 ";
$bind          = array();

$password    = $params->get('password');
$accountID   = (int)$params->get('account_id');
$username    = trim($params->get('username'));
$ipAddress   = trim($params->get('ip'));
$loginAfter  = $params->get('login_after_date');
$loginBefore = $params->get('login_before_date');

if ($password && $auth->allowedToSearchCpLoginLogPw) {
	$sqlpartial .= 'AND user_pass = ? ';
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

$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$loginLogTable $sqlpartial";
$sth = $server->connection->getStatement($sql);
$sth->execute($bind);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'account_id', 'user_id', 'user_pass', 'reg_ip',
	'reg_date' => 'desc'
));

$sql = "SELECT account_id, userid, user_pass, reg_ip, reg_date FROM {$server->loginDatabase}.$loginLogTable $sqlpartial";
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatement($sql);
$sth->execute($bind);

$logins = $sth->fetchAll();
if ($logins) {
}
?>
