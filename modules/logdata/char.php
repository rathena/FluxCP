<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('CharLogTitle');

$sql_param_str = '';
$sql_params = array();
$char_actions = array();
$account_id = $params->get('account_id');
$char_name = $params->get('char_name');
$datefrom = $params->get('from_date');
$dateto = $params->get('to_date');
$other_action = $params->get('other_action');
if ($params->get('char_action')) {
	$char_actions = $params->get('char_action')->toArray();
	$char_actions = array_keys($char_actions);
}
if ($account_id) {
	$sql_param_str .= '`account_id`=?';
	$sql_params[] = $account_id;
}
if ($char_name) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`name` LIKE ?';
	$sql_params[] = "%$char_name%";
}
if ($other_action) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`char_msg` LIKE ?';
	$sql_params[] = "%$other_action%";
}
if (count($char_actions)) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`char_msg` IN('.implode(',', array_fill(0, count($char_actions), '?')).')';
	$arr = array();
	if (in_array(1,$char_actions)) $arr[] = 'char select';
	if (in_array(2,$char_actions)) $arr[] = 'change char name';
	if (in_array(3,$char_actions)) $arr[] = 'make new char';
	$sql_params = array_merge($sql_params, $arr);
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

$sql = "SELECT COUNT(time) AS total FROM {$server->charMapDatabase}.charlog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('time' => 'DESC', 'char_msg', 'account_id', 'char_num', 'name'));

$sql = "SELECT time, char_msg, account_id, char_num, name FROM {$server->charMapDatabase}.charlog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$chars1 = $sth->fetchAll();
?>
