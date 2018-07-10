<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('NPCLogTitle');

$sql_param_str = '';
$sql_params = array();

$map = $params->get('map');
$mes = $params->get('mes');
$datefrom = $params->get('from_date');
$dateto = $params->get('to_date');
if ($map) {
	$sql_param_str .= '`map` LIKE ?';
	$sql_params[] = "%$map%";
}
if ($mes) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`mes` LIKE ?';
	$sql_params[] = "%$mes%";
}
if ($datefrom) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`npc_date` >= ?';
	$sql_params[] = $datefrom;
}
if ($dateto) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`npc_date` <= ?';
	$sql_params[] = $dateto;
}

$sql = "SELECT COUNT(npc_id) AS total FROM {$server->logsDatabase}.npclog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('npc_date' => 'DESC', 'npc_id', 'account_id', 'char_id', 'char_name', 'map', 'mes'));

$sql = "SELECT npc_id, npc_date, account_id, char_id, char_name, map, mes FROM {$server->logsDatabase}.npclog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$npcs = $sth->fetchAll();
?>
