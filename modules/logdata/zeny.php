<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('ZenyLogTitle');

$sql = "SELECT COUNT(id) AS total FROM {$server->logsDatabase}.zenylog";
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('time' => 'desc', 'char_id', 'src_id', 'type', 'amount', 'map'));

$col = "time, char_id, src_id, type, amount, map";
$sql = $paginator->getSQL("SELECT $col FROM {$server->logsDatabase}.zenylog");
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$logs = $sth->fetchAll();

if ($logs) {
	$charIDs = array();
	$srcIDs  = array();
	$mobIDs  = array();
	$pickTypes = Flux::config('PickTypes');

	foreach ($logs as $log) {
		$charIDs[$log->char_id] = null;

		if ($log->type == 'M') {
			$mobIDs[$log->src_id] = null;
		}
		else {
			$srcIDs[$log->src_id] = null;
		}

		$log->pick_type = $pickTypes->get($log->type);
	}

	if ($charIDs || $srcIDs) {
		$charKeys = array_keys($charIDs);
		$srcKeys = array_keys($srcIDs);

		$sql  = "SELECT char_id, name FROM {$server->charMapDatabase}.`char` ";
		$sql .= "WHERE char_id IN (".implode(',', array_fill(0, count($charKeys) + count($srcKeys), '?')).")";
		$sth  = $server->connection->getStatement($sql);
		$sth->execute(array_merge($charKeys, $srcKeys));

		$ids = $sth->fetchAll();

		// Map char_id to name.
		foreach ($ids as $id) {
			if(array_key_exists($id->char_id, $charIDs)) {
				$charIDs[$id->char_id] = $id->name;
			}
			if(array_key_exists($id->char_id, $srcIDs)) {
				$srcIDs[$id->char_id] = $id->name;
			}
		}
	}

	require_once 'Flux/TemporaryTable.php';

	if ($mobIDs) {
		$mobDB      = "{$server->charMapDatabase}.monsters";
		if($server->isRenewal) {
			$fromTables = array("{$server->charMapDatabase}.mob_db_re", "{$server->charMapDatabase}.mob_db2_re");
		} else {
			$fromTables = array("{$server->charMapDatabase}.mob_db", "{$server->charMapDatabase}.mob_db2");
		}
		$tempMobs   = new Flux_TemporaryTable($server->connection, $mobDB, $fromTables);

		$ids = array_keys($mobIDs);
		$sql = "SELECT id, name_english FROM {$server->charMapDatabase}.monsters WHERE ID IN (".implode(',', array_fill(0, count($ids), '?')).")";
		$sth = $server->connection->getStatement($sql);
		$sth->execute($ids);

		$ids = $sth->fetchAll();

		// Map id to name.
		foreach ($ids as $id) {
			$mobIDs[$id->id] = $id->name_english;
		}
	}

	foreach ($logs as $log) {
		if (array_key_exists($log->char_id, $charIDs)) {
			$log->char_name = $charIDs[$log->char_id];
		}

		if (($log->type == 'M') && array_key_exists($log->src_id, $mobIDs)) {
			$log->src_name = $mobIDs[$log->char_id];
		}
		elseif (array_key_exists($log->char_id, $srcIDs)) {
			$log->src_name = $srcIDs[$log->char_id];
		}
	}
}

?>
