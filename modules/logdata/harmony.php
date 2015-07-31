<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Harmony Logs';
$harmonysql = 'harmony_log';

$sqlpartial = '';
$bind       = array();

$dateAfter  = $params->get('log_after_date');
$dateBefore = $params->get('log_before_date');
$ipAddress  = trim($params->get('ip'));
$char_name = trim($params->get('char_name'));
$account_id = trim($params->get('account_id'));

if ($dateAfter) {
	$sqlpartial .= 'AND date >= ? ';
	$bind[]      = $dateAfter;
}

if ($dateBefore) {
	$sqlpartial .= 'AND date <= ? ';
	$bind[]      = $dateBefore;
}

if ($ipAddress) {
	$sqlpartial .= 'AND ip LIKE ? ';
	$bind[]      = "%$ipAddress%";
}

if ($char_name) {
	$sqlpartial .= 'AND char_name LIKE ? ';
	$bind[]      = "%$char_name%";
}
if ($account_id) {
	$sqlpartial .= 'AND account_id = ? ';
	$bind[]      = $account_id;
}

$sth = $server->connection->getStatementForLogs("SELECT COUNT(log_id) AS total FROM {$server->logsDatabase}.$harmonysql WHERE 1=1 $sqlpartial");
$sth->execute($bind);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'date' => 'data', 'account_id', 'char_name', 'ip'
));

$sql = "SELECT date, ip, account_id, char_name, data FROM {$server->logsDatabase}.$harmonysql WHERE 1=1 $sqlpartial";
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($bind);

$harmonydata = $sth->fetchAll();

?>