<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'List Chat Messages';

$sql_param_str = '';
$sql_params = array();
$chat_type = array();
if ($params->get('chat_type')) {
	$chat_type = $params->get('chat_type')->toArray();
	$chat_type = array_keys($chat_type);
}
$char_id = $params->get('char_id');
$account_id = $params->get('account_id');
$dst_name = $params->get('dst_name');
$map = $params->get('map');
$datefrom = $params->get('from_date');
$dateto = $params->get('to_date');
if ($char_id) {
	$sql_param_str .= '`src_charid`=?';
	$sql_params[] = $char_id;
}
if ($account_id) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`src_accountid`=?';
	$sql_params[] = $account_id;
}
if ($map) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`src_map` LIKE ?';
	$sql_params[] = "%$map%";
}
if ($dst_name) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`dst_charname` LIKE ?';
	$sql_params[] = "%$dst_name%";
}
if (count($chat_type)) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`type` IN ('.implode(',', array_fill(0, count($chat_type), '?')).')';
	$sql_params = array_merge($sql_params, $chat_type);
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

$sql = "SELECT COUNT(id) AS total FROM {$server->logsDatabase}.chatlog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'time' => 'desc', 'type', 'type_id', 'src_charid', 'src_accountid',
	'src_map', 'src_map_x', 'src_map_y', 'dst_charname', 'message'
));

$sql  = "SELECT time, type, type_id, src_charid, src_accountid, src_map, src_map_x, src_map_y, dst_charname, ";
$sql .= "REPLACE(message, '|00', '') AS message FROM {$server->logsDatabase}.chatlog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sql  = $paginator->getSQL($sql);
$sth  = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$messages = $sth->fetchAll();

$chattypes = Flux::config('ChatTypes');

if ($chattypes) {
	foreach ($messages as $msg) {
		$msg->type_str = $chattypes->get($msg->type);
	}
}
?>
