<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('InterLogTitle');

$sql_param_str = '';
$sql_params = array();

$logmsg = $params->get('logmsg');
$datefrom = $params->get('from_date');
$dateto = $params->get('to_date');
if ($logmsg) {
	$sql_param_str = '`log` LIKE ?';
	$sql_params[] = "%$logmsg%";
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

$sql = "SELECT COUNT(`time`) as total FROM {$server->charMapDatabase}.interlog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sth = $server->connection->getStatement($sql);
$sth->execute($sql_params);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('time' => 'DESC','log'));

$sql = "SELECT `time`, `log` FROM {$server->charMapDatabase}.interlog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatement($sql);
$sth->execute($sql_params);

$inters = $sth->fetchAll();
?>
