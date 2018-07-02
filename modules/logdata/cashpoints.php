<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('CashLogTitle');

$sql_param_str = '';
$sql_params = array();
$type = array();
if ($params->get('type')) {
	$type = $params->get('type')->toArray();
	$type = array_keys($type);
}
$cash_type = array();
if ($params->get('cash_type')) {
	$cash_type = $params->get('cash_type')->toArray();
	$cash_type = array_keys($cash_type);
}
$char_id = $params->get('char_id');
$cash_min = $params->get('cash_min');
$cash_max = $params->get('cash_max');
$map = $params->get('map');
$datefrom = $params->get('from_date');
$dateto = $params->get('to_date');
if ($char_id) {
	$sql_param_str .= '`char_id`=?';
	$sql_params[] = $char_id;
}
if ($map) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`map` LIKE ?';
	$sql_params[] = "%$map%";
}
if (count($type)) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`type` IN ('.implode(',', array_fill(0, count($type), '?')).')';
	$sql_params = array_merge($sql_params, $type);
}
if (count($cash_type)) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`cash_type` IN ('.implode(',', array_fill(0, count($cash_type), '?')).')';
	$sql_params = array_merge($sql_params, $cash_type);
}
if ($cash_min || $cash_max) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	if ($cash_min && $cash_max) {
		$sql_param_str .= '`amount` BETWEEN ? AND ?';
		$sql_params[] = $cash_min;
		$sql_params[] = $cash_max;
	}
	else if ($cash_min && !$cash_max) {
		$sql_param_str .= '`amount` >= ?';
		$sql_params[] = $cash_min;
	}
	else {
		$sql_param_str .= '`amount` <= ?';
		$sql_params[] = $cash_max;
	}
}
if ($datefrom || $dateto) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	if ($datefrom && $dateto) {
		$sql_param_str .= '`time` BETWEEN ? AND ?';
		$sql_params[] = $datefrom;
		$sql_params[] = $dateto;
	}
	else if ($datefrom && !$dateto) {
		$sql_param_str .= '`time` >= ?';
		$sql_params[] = $datefrom;
	}
	else {
		$sql_param_str .= '`time` <= ?';
		$sql_params[] = $dateto;
	}
}

$sql = "SELECT COUNT(id) AS total FROM {$server->logsDatabase}.cashlog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('time' => 'DESC', 'char_id', 'type', 'cash_type', 'amount', 'map'));

$sql = "SELECT `time`, `char_id`, `type`, `cash_type`, `amount`, `map` FROM {$server->logsDatabase}.cashlog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$logs = $sth->fetchAll();

if ($logs) {
	$charIDs = array();
	$pickTypes = Flux::config('PickTypes');
	$cashTypes = Flux::config('CashTypes');

	foreach ($logs as $log) {
		$charIDs[$log->char_id] = null;

		if ($log->type == 'M') {
			$mobIDs[$log->src_id] = null;
		}
		else {
			$srcIDs[$log->src_id] = null;
		}

		$log->pick_type = $pickTypes->get($log->type);
		$log->cash_type_name = $cashTypes->get($log->cash_type);
	}

	if ($charIDs || $srcIDs) {
		$charKeys = array_keys($charIDs);

		$search = implode(',', array_fill(0, count($charKeys), '?'));
		$sql  = "SELECT char_id, name FROM {$server->charMapDatabase}.`char` ";
		$sql .= "WHERE char_id IN (".$search.")";
		$sth  = $server->connection->getStatement($sql);
		$sth->execute($charKeys);

		$ids = $sth->fetchAll();

		// Map char_id to name.
		foreach ($ids as $id) {
			if(array_key_exists($id->char_id, $charIDs)) {
				$charIDs[$id->char_id] = $id->name;
			}
		}
	}

	foreach ($logs as $log) {
		if (array_key_exists($log->char_id, $charIDs)) {
			$log->char_name = $charIDs[$log->char_id];
		}
	}
}

?>
