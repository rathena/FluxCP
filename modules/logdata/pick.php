<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('PickLogTitle');

$sql = "SELECT COUNT(id) AS total FROM {$server->logsDatabase}.picklog";
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'time' => 'desc', 'char_id', 'type', 'nameid', 'amount',
	'refine', 'card0', 'card1', 'card2', 'card3', 'map'
));

$col = "time, char_id, type, nameid, amount, refine, card0, card1, card2, card3, map";
$sql = $paginator->getSQL("SELECT $col FROM {$server->logsDatabase}.picklog");
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$picks = $sth->fetchAll();

if ($picks) {
	$charIDs   = array();
	$itemIDs   = array();
	$mobIDs    = array();
	$pickTypes = Flux::config('PickTypes');

	foreach ($picks as $pick) {
		$itemIDs[$pick->nameid] = null;

		if ($pick->type == 'M' || $pick->type == 'L') {
			$mobIDs[$pick->char_id] = null;
		}
		else {
			$charIDs[$pick->char_id] = null;
		}

		if ($pick->card0) {
			$itemIDs[$pick->card0] = null;
		}
		if ($pick->card1) {
			$itemIDs[$pick->card1] = null;
		}
		if ($pick->card2) {
			$itemIDs[$pick->card2] = null;
		}
		if ($pick->card3) {
			$itemIDs[$pick->card3] = null;
		}

		$pick->pick_type = $pickTypes->get($pick->type);
	}

	if ($charIDs) {
		$ids = array_keys($charIDs);
		$sql = "SELECT char_id, name FROM {$server->charMapDatabase}.`char` WHERE char_id IN (".implode(',', array_fill(0, count($ids), '?')).")";
		$sth = $server->connection->getStatement($sql);
		$sth->execute($ids);

		$ids = $sth->fetchAll();

		// Map char_id to name.
		foreach ($ids as $id) {
			$charIDs[$id->char_id] = $id->name;
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
		$sql = "SELECT id, name_english FROM {$server->charMapDatabase}.monsters WHERE id IN (".implode(',', array_fill(0, count($ids), '?')).")";
		$sth = $server->connection->getStatement($sql);
		$sth->execute($ids);

		$ids = $sth->fetchAll();

		// Map id to name.
		foreach ($ids as $id) {
			$mobIDs[$id->id] = $id->name_english;
		}
	}

	if ($itemIDs) {
		if($server->isRenewal) {
			$fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
		} else {
			$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
		}
		$tableName = "{$server->charMapDatabase}.items";
		$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);
		$shopTable = Flux::config('FluxTables.ItemShopTable');

		$ids = array_keys($itemIDs);
		$sql = "SELECT id, name_english FROM {$server->charMapDatabase}.items WHERE id IN (".implode(',', array_fill(0, count($ids), '?')).")";
		$sth = $server->connection->getStatement($sql);
		$sth->execute($ids);

		$ids = $sth->fetchAll();

		// Map nameid to name.
		foreach ($ids as $id) {
			$itemIDs[$id->id] = $id->name_english;
		}
	}

	foreach ($picks as $pick) {
		if (($pick->type == 'M' || $pick->type == 'L') && array_key_exists($pick->char_id, $mobIDs)) {
			$pick->char_name = $mobIDs[$pick->char_id];
		}
		elseif (array_key_exists($pick->char_id, $charIDs)) {
			$pick->char_name = $charIDs[$pick->char_id];
		}

		if (array_key_exists($pick->nameid, $itemIDs)) {
			$pick->item_name = $itemIDs[$pick->nameid];
		}
		if (array_key_exists($pick->card0, $itemIDs)) {
			$pick->card0_name = $itemIDs[$pick->card0];
		}
		if (array_key_exists($pick->card1, $itemIDs)) {
			$pick->card1_name = $itemIDs[$pick->card1];
		}
		if (array_key_exists($pick->card2, $itemIDs)) {
			$pick->card2_name = $itemIDs[$pick->card2];
		}
		if (array_key_exists($pick->card3, $itemIDs)) {
			$pick->card3_name = $itemIDs[$pick->card3];
		}
	}
}

?>
