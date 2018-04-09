<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Email Changes';

$changeTable = Flux::config('FluxTables.ChangeEmailTable');
$sqlpartial  = "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = log.account_id ";
$sqlpartial .= 'WHERE 1=1 ';
$bind        = array();

// Email change searching.
$requestAfter  = $params->get('request_after_date');
$requestBefore = $params->get('request_before_date');
$changeAfter   = $params->get('change_after_date');
$changeBefore  = $params->get('change_before_date');
$accountID     = trim($params->get('account_id'));
$username      = trim($params->get('username'));
$oldEmail      = trim($params->get('old_email'));
$newEmail      = trim($params->get('new_email'));
$requestIP     = trim($params->get('request_ip'));
$changeIP      = trim($params->get('change_ip'));

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
if ($oldEmail) {
	$sqlpartial .= 'AND old_email LIKE ? ';
	$bind[]      = "%$oldEmail%";
}
if ($newEmail) {
	$sqlpartial .= 'AND new_email LIKE ? ';
	$bind[]      = "%$newEmail%";
}
if ($requestIP) {
	$sqlpartial .= 'AND request_ip LIKE ? ';
	$bind[]      = "%$requestIP%";
}
if ($changeIP) {
	$sqlpartial .= 'AND change_ip LIKE ? ';
	$bind[]      = "%$changeIP%";
}

$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$changeTable AS log $sqlpartial";
$sth = $server->connection->getStatement($sql);
$sth->execute($bind);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'log.account_id', 'userid',
	'change_date' => 'desc', 'change_ip', 
	'request_date' => 'desc', 'request_ip'
));

$col  = 'id, code, log.account_id, old_email, new_email, userid, ';
$col .= 'request_date, request_ip, change_date, change_ip, change_done';
$sql  = $paginator->getSQL("SELECT $col FROM {$server->loginDatabase}.$changeTable AS log $sqlpartial");
$sth  = $server->connection->getStatement($sql);
$sth->execute($bind);

$changes = $sth->fetchAll();
?>
