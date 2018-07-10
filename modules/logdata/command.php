<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('CommandLogTitle');

$sql_param_str = '';
$sql_params = array();
$cmd = $params->get('cmd');
$char_id = $params->get('char_id');
$account_id = $params->get('account_id');
$char_name = $params->get('char_name');
$map = $params->get('map');
$datefrom = $params->get('from_date');
$dateto = $params->get('to_date');
if ($char_id) {
	$sql_param_str .= '`char_id`=?';
	$sql_params[] = $char_id;
}
if ($account_id) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`account_id`=?';
	$sql_params[] = $account_id;
}
if ($map) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`map` LIKE ?';
	$sql_params[] = "%$map%";
}
if ($cmd) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`command` LIKE ?';
	$sql_params[] = "%$cmd%";
}
if ($char_name) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`char_name` LIKE ?';
	$sql_params[] = "%$char_name%";
}
if ($datefrom) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`time` >= ?';
	$sql_params[] = $datefrom;
}
if ($dateto) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`time` <= ?';
	$sql_params[] = $dateto;
}

$sql = "SELECT COUNT(atcommand_id) AS total FROM {$server->logsDatabase}.atcommandlog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('atcommand_date' => 'desc', 'account_id', 'char_id', 'char_name', 'map', 'command'));

$sql = "SELECT atcommand_id, atcommand_date, account_id, char_id, char_name, map, command FROM {$server->logsDatabase}.atcommandlog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$commands = $sth->fetchAll();
?>
