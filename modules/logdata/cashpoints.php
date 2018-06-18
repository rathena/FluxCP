<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('CashLogTitle');

$sql = "SELECT COUNT(id) AS total FROM {$server->logsDatabase}.cashlog";
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('time' => 'desc', 'char_id', 'type', 'amount', 'map'));

$col = "time, char_id, type, amount, map";
$sql = $paginator->getSQL("SELECT $col FROM {$server->logsDatabase}.cashlog");
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$logs = $sth->fetchAll();

if ($logs) {
	$charIDs = array();
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
