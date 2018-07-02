<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('MVPLogTitle');

$sql_param_str = '';
$sql_params = array();

$char_id = $params->get('char_id');
$mobid = $params->get('mobid');
$item = $params->get('item');
$exp_min = $params->get('exp_min');
$exp_max = $params->get('exp_max');
$map = $params->get('map');
$datefrom = $params->get('from_date');
$dateto = $params->get('to_date');

if ($char_id) {
	$sql_param_str .= '`kill_char_id`=?';
	$sql_params[] = $char_id;
}
if ($mobid) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`monster_id`=?';
	$sql_params[] = $mobid;
}
if ($item) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`prize`=?';
	$sql_params[] = $item;
}
if ($map) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`map` LIKE ?';
	$sql_params[] = "%$map%";
}
if ($exp_min || $exp_max) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	if ($exp_min && $exp_max) {
		$sql_param_str .= '`mvpexp` BETWEEN ? AND ?';
		$sql_params[] = $exp_min;
		$sql_params[] = $exp_max;
	}
	else if ($exp_min && !$exp_max) {
		$sql_param_str .= '`mvpexp` >= ?';
		$sql_params[] = $datefrom;
	}
	else {
		$sql_param_str .= '`mvpexp` <= ?';
		$sql_params[] = $exp_max;
	}
}
if ($datefrom || $dateto) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	if ($datefrom && $dateto) {
		$sql_param_str .= '`mvp_date` BETWEEN ? AND ?';
		$sql_params[] = $datefrom;
		$sql_params[] = $dateto;
	}
	else if ($datefrom && !$dateto) {
		$sql_param_str .= '`mvp_date` >= ?';
		$sql_params[] = $datefrom;
	}
	else {
		$sql_param_str .= '`mvp_date` <= ?';
		$sql_params[] = $dateto;
	}
}

$sql = "SELECT COUNT(mvp_id) AS total FROM {$server->logsDatabase}.mvplog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('mvp_date' => 'DESC', 'kill_char_id', 'monster_id', 'prize', 'mvpexp', 'map'));

$sql = "SELECT mvp_id, mvp_date, kill_char_id, monster_id, prize, mvpexp, map FROM {$server->logsDatabase}.mvplog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$mvps = $sth->fetchAll();
?>
