<?php
if (!defined('FLUX_ROOT')) exit;

require_once 'Flux/Item.php';
$itemLib = new Flux_Item($server);

$title = Flux::message('PickLogTitle');

$sql_param_str = '';
$sql_params = array();

$char_id = $params->get('char_id');
$nameid = $params->get('nameid');
$map = $params->get('map');
$card = $params->get('card');
$datefrom = $params->get('datefrom');
$dateto = $params->get('dateto');

if ($char_id) {
	$sql_param_str = '`char_id`=?';
	$sql_params[] = $char_id;
}
if ($nameid) {
	if (count($sql_params))
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`nameid`=?';
	$sql_params[] = $nameid;
}
if ($map) {
	if (count($sql_params))
		$sql_param_str .= ' AND ';
	$sql_param_str .= '`map` LIKE ?';
	$sql_params[] = "%$map%";
}
if ($card) {
	if (count($sql_params))
		$sql_param_str .= ' AND ';
	$sql_param_str .= '(`card0`=? OR `card1`=? OR `card2`=? OR `card3`=?)';
	$sql_params[] = $card;
	$sql_params[] = $card;
	$sql_params[] = $card;
	$sql_params[] = $card;
}
if ($datefrom || $dateto) {
	if (count($sql_params))
		$sql_param_str .= ' AND ';
	if ($datefrom && $dateto) {
		$sql_param_str .= '`time` BETWEEN ? AND ?';
		$sql_params[] = $datefrom;
		$sql_params[] = $dateto;
	}
	else if ($datefrom && !$dateto) {
		$sql_param_str .= '`time` > ?';
		$sql_params[] = $datefrom;
	}
	else {
		$sql_param_str .= '`time` < ?';
		$sql_params[] = $dateto;
	}
}

$sql = "SELECT COUNT(`id`) AS total FROM {$server->logsDatabase}.picklog";
if (count($sql_params))
	$sql .= " WHERE ".$sql_param_str;
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'time' => 'desc', 'char_id', 'type', 'nameid', 'amount',
	'refine', 'card0', 'card1', 'card2', 'card3', 'map'
));

$sql = "SELECT `time`, `char_id`, `type`, `nameid`, `amount`, `refine`, `card0`, `card1`, `card2`, `card3`,`map`";
$sql .= ",`bound`,`unique_id` ".$itemLib->random_options_select;
$sql .= "FROM {$server->logsDatabase}.picklog";
if (count($sql_params))
	$sql .= " WHERE ".$sql_param_str;
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);

$picks = $sth->fetchAll();

if ($picks) {
	$charIDs   = array();
	$itemIDs   = array();
	$mobIDs    = array();
	$creatorIDs = array();
	$pickTypes = Flux::config('PickTypes');
	
	foreach ($picks as $pick) {
		$itemIDs[$pick->nameid] = null;
		
		if ($pick->type == 'M' || $pick->type == 'L') {
			$mobIDs[$pick->char_id] = null;
		}
		else {
			$charIDs[$pick->char_id] = null;
		}
		
		if (!$itemLib->itemIsSpecial($pick->card0)) {
			$pick->cardsOver = $itemLib->getCardsOver($pick);
			if ($pick->cardsOver < 0) {
				$pick->cardsOver = 0;
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
			$pick->special = 0;
		}
		else {
			$pick->cardsOver = 0;
			$pick->creator_char_id = (($pick->card3<<16)|$pick->card2);
			$creatorIDs[$pick->creator_char_id] = null;
			$pick = $itemLib->getItemSpecialValues($pick);
			$pick->special = 1;
		}

		$pick->options = ($itemLib->random_options_enabled ? $itemLib->itemHasOptions($pick) : 0);
		$pick->pick_type = $pickTypes->get($pick->type);
	}

	if ($creatorIDs) {
		$ids = array_keys($creatorIDs);
		$sql = "SELECT `char_id`, `name` FROM {$server->charMapDatabase}.`char` WHERE `char_id` IN (".implode(',', array_fill(0, count($creatorIDs), '?')).")";
		$sth = $server->connection->getStatement($sql);
		$sth->execute($ids);

		$ids = $sth->fetchAll();

		// Map char_id to name.
		foreach ($ids as $id) {
			$creatorIDs[$id->char_id] = $id->name;
		}
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
		$fromTables = array("{$server->charMapDatabase}.mob_db", "{$server->charMapDatabase}.mob_db2");
		$tempMobs   = new Flux_TemporaryTable($server->connection, $mobDB, $fromTables);

		$ids = array_keys($mobIDs);
		$sql = "SELECT ID, iName FROM {$server->charMapDatabase}.monsters WHERE ID IN (".implode(',', array_fill(0, count($ids), '?')).")";
		$sth = $server->connection->getStatement($sql);
		$sth->execute($ids);

		$ids = $sth->fetchAll();

		// Map id to name.
		foreach ($ids as $id) {
			$mobIDs[$id->ID] = $id->iName;
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
		$sql = "SELECT id, name_japanese FROM {$server->charMapDatabase}.items WHERE id IN (".implode(',', array_fill(0, count($ids), '?')).")";
		$sth = $server->connection->getStatement($sql);
		$sth->execute($ids);

		$ids = $sth->fetchAll();

		// Map nameid to name.
		foreach ($ids as $id) {
			$itemIDs[$id->id] = $id->name_japanese;
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
