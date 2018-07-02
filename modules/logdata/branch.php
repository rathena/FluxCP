<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('BranchLogTitle');

$sql_param_str = '';
$sql_params = array();
$datefrom = $params->get('from_date');
$dateto = $params->get('to_date');
$account_id = $params->get('account_id');
$char_id = $params->get('char_id');
$char_name = $params->get('char_name');
$map = $params->get('map');
if ($account_id) {
	$sql_param_str .= '`account_id`=?';
	$sql_params[] = $account_id;
}
if ($char_id) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`char_id`=?';
	$sql_params[] = $char_id;
}
if ($char_name) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`char_name` LIKE ?';
	$sql_params[] = "%$char_name%";
}
if ($map) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`map` LIKE ?';
	$sql_params[] = "%$map%";
}
if ($datefrom || $dateto) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	if ($datefrom && $dateto) {
		$sql_param_str .= '`branch_date` BETWEEN ? AND ?';
		$sql_params[] = $datefrom;
		$sql_params[] = $dateto;
	}
	else if ($datefrom && !$dateto) {
		$sql_param_str .= '`branch_date` >= ?';
		$sql_params[] = $datefrom;
	}
	else {
		$sql_param_str .= '`branch_date` <= ?';
		$sql_params[] = $dateto;
	}
}

$sql = "SELECT COUNT(branch_id) AS total FROM {$server->logsDatabase}.branchlog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('branch_date' => 'DESC','account_id', 'char_id', 'char_name', 'map'));

$sql = "SELECT branch_id, branch_date, account_id, char_id, char_name, map FROM {$server->logsDatabase}.branchlog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$branchs = $sth->fetchAll();
?>
