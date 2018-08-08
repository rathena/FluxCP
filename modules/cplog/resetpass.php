<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Password Resets';

$resetTable  = Flux::config('FluxTables.ResetPasswordTable');
$sqlpartial  = "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = log.account_id ";
$sqlpartial .= 'WHERE 1=1 ';
$bind        = array();

// Password reset searching.
$requestAfter  = $params->get('request_after_date');
$requestBefore = $params->get('request_before_date');
$resetAfter    = $params->get('reset_after_date');
$resetBefore   = $params->get('reset_before_date');
$accountID     = trim($params->get('account_id'));
$username      = trim($params->get('username'));
$requestIP     = trim($params->get('request_ip'));
$resetIP       = trim($params->get('reset_ip'));

if ($requestAfter) {
	$sqlpartial .= 'AND request_date >= ? ';
	$bind[]      = $requestAfter;
}
if ($requestBefore) {
	$sqlpartial .= 'AND request_date <= ? ';
	$bind[]      = $requestBefore;
}
if ($accountID) {
	$sqlpartial .= 'AND log.account_id = ? ';
	$bind[]      = $accountID;
}
if ($username) {
	$sqlpartial .= 'AND userid LIKE ? ';
	$bind[]      = "%$username%";
}
if ($requestIP) {
	$sqlpartial .= 'AND request_ip LIKE ? ';
	$bind[]      = "%$requestIP%";
}
if ($resetIP) {
	$sqlpartial .= 'AND reset_ip LIKE ? ';
	$bind[]      = "%$resetIP%";
}

if ($auth->allowedToSearchCpResetPass) {
	$oldPassword = $params->get('old_password');
	$newPassword = $params->get('new_password');
	$useMD5      = $session->loginAthenaGroup->loginServer->config->getUseMD5();
	
	if ($oldPassword) {
		if ($useMD5) {
			$oldPassword = md5($oldPassword);
		}
		$sqlpartial .= 'AND old_password = ? ';
		$bind[]      = $oldPassword;
	}
	if ($newPassword) {
		if ($useMD5) {
			$newPassword = md5($newPassword);
		}
		$sqlpartial .= 'AND new_password = ? ';
		$bind[]      = $newPassword;
	}
}

$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$resetTable AS log $sqlpartial";
$sth = $server->connection->getStatement($sql);
$sth->execute($bind);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'log.account_id', 'userid',
	'reset_date' => 'desc', 'reset_ip', 
	'request_date' => 'desc', 'request_ip'
));

$col  = 'id, code, log.account_id, old_password, new_password, userid, ';
$col .= 'request_date, request_ip, reset_date, reset_ip, reset_done';
$sql  = $paginator->getSQL("SELECT $col FROM {$server->loginDatabase}.$resetTable AS log $sqlpartial");
$sth  = $server->connection->getStatement($sql);
$sth->execute($bind);

$resets = $sth->fetchAll();
?>
