<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Password Changes';

$changeTable  = Flux::config('FluxTables.ChangePasswordTable');
$sqlpartial  = "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = log.account_id ";
$sqlpartial .= 'WHERE 1=1 ';
$bind        = array();

// Password change searching.
$changeAfter   = $params->get('change_after_date');
$changeBefore  = $params->get('change_before_date');
$accountID     = trim($params->get('account_id'));
$username      = trim($params->get('username'));
$changeIP      = trim($params->get('change_ip'));

if ($changeAfter) {
	$sqlpartial .= 'AND change_date >= ? ';
	$bind[]      = $changeAfter;
}
if ($changeBefore) {
	$sqlpartial .= 'AND change_date <= ? ';
	$bind[]      = $changeBefore;
}
if ($accountID) {
	$sqlpartial .= 'AND log.account_id = ? ';
	$bind[]      = $accountID;
}
if ($username) {
	$sqlpartial .= 'AND userid LIKE ? ';
	$bind[]      = "%$username%";
}
if ($changeIP) {
	$sqlpartial .= 'AND change_ip LIKE ? ';
	$bind[]      = "%$changeIP%";
}

if ($auth->allowedToSearchCpChangePass) {
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

$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$changeTable AS log $sqlpartial";
$sth = $server->connection->getStatement($sql);
$sth->execute($bind);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'log.account_id', 'userid',
	'change_date' => 'desc', 'change_ip'
));

$col  = 'id, log.account_id, old_password, new_password, userid, change_date, change_ip';
$sql  = $paginator->getSQL("SELECT $col FROM {$server->loginDatabase}.$changeTable AS log $sqlpartial");
$sth  = $server->connection->getStatement($sql);
$sth->execute($bind);

$changes = $sth->fetchAll();
?>
