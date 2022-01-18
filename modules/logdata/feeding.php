<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('FeedingLogTitle');

$sql_param_str = '';
$sql_params = array();

$char_id = $params->get('char_id');
$target = $params->get('target');
$item_id = $params->get('item_id');
$map = $params->get('map');
$datefrom = $params->get('from_date');
$dateto = $params->get('to_date');
$type = array();
if ($params->get('type')) {
	$type = $params->get('type')->toArray();
	$type = array_keys($type);
}
if ($char_id) {
	$sql_param_str .= '`char_id`=?';
	$sql_params[] = $char_id;
}
if ($item_id) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`item_id`=?';
	$sql_params[] = $item_id;
}
if ($map) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`map` LIKE ?';
	$sql_params[] = "%$map%";
}
if ($target) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`target_class`=?';
	$sql_params[] = "$target";
}
if (count($type)) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`type` IN ('.implode(',', array_fill(0, count($type), '?')).')';
	$sql_params = array_merge($sql_params, $type);
}
if ($datefrom || $dateto) {
	if ($sql_param_str)
		$sql_param_str .= ' AND ';
	if ($datefrom && $dateto) {
		$sql_param_str .= '(DATE_FORMAT(`time`,\'%Y-%m-%d\') BETWEEN CAST(? AS DATE) AND CAST(? AS DATE))';
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

$sql = "SELECT COUNT(`id`) AS total FROM {$server->logsDatabase}.feedinglog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'time' => 'DESC', 'char_id', 'target_class', 'type', 'item_id', 'map',
));

$sql = "SELECT * ";
$sql .= "FROM {$server->logsDatabase}.feedinglog";
if ($sql_param_str)
	$sql .= " WHERE ".$sql_param_str;
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$feeds = $sth->fetchAll();

if ($feeds) {
	$itemIDs = array();
	$charIDs = array();
	$mobIDs = array();
	$feedTypes = Flux::config('FeedingTypes');

	foreach ($feeds as $log) {
		$itemIDs[$log->item_id] = null;
		$charIDs[$log->char_id] = null;
		if ($log->type == 'P')
			$mobIDs[$log->target_class] = null;
		if ($feedTypes) {
			$log->type_name = $feedTypes->get($log->type);
		}
	}

	if (count($charIDs)) {
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

	if (count($itemIDs)) {
		require_once 'Flux/TemporaryTable.php';
		if($server->isRenewal) {
			$fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
		} else {
			$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
		}
		$tableName = "{$server->charMapDatabase}.items";
		$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);

		$ids = array_keys($itemIDs);
		$sql = "SELECT id, name_english FROM {$server->charMapDatabase}.items WHERE id IN (".implode(',', array_fill(0, count($ids), '?')).")";
		$sth = $server->connection->getStatement($sql);
		$sth->execute($ids);

		$ids = $sth->fetchAll();

		foreach ($ids as $id) {
			$itemIDs[$id->id] = $id->name_english;
		}
	}

	if ($mobIDs) {
		$mobDB      = "{$server->charMapDatabase}.monsters";
        if($server->isRenewal) {
            $fromTables = array("{$server->charMapDatabase}.mob_db_re", "{$server->charMapDatabase}.mob_db2_re");
        } else {
            $fromTables = array("{$server->charMapDatabase}.mob_db", "{$server->charMapDatabase}.mob_db2");
        }
		$tempMobs   = new Flux_TemporaryTable($server->connection, $mobDB, $fromTables);

		$ids = array_keys($mobIDs);
		$sql = "SELECT id, name_english FROM {$server->charMapDatabase}.monsters WHERE id IN (".implode(',', array_fill(0, count($ids), '?')).")";
		$sth = $server->connection->getStatement($sql);
		$sth->execute($ids);

		$ids = $sth->fetchAll();

		foreach ($ids as $id) {
			$mobIDs[$id->id] = $id->name_english;
		}
	}

	$homuns = Flux::config('HomunClasses');
	foreach ($feeds as $log) {
		if ($log->type == 'P' && array_key_exists($log->target_class, $mobIDs))
			$log->target_name = $mobIDs[$log->target_class];
		else if ($homuns && $log->type == 'H')
			$log->target_name = $homuns->get($log->target_class);
	}
}
