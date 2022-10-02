<?php
if (!defined('FLUX_ROOT')) exit;

$type = (int)$params->get('type');
$index = $params->get('index');
$storageTable = Flux::config('StorageTables.'.$type);

$title = 'Viewing '.htmlspecialchars(Flux::message('StorageGroup.'.$type)).' Item';

require_once 'Flux/TemporaryTable.php';

if($server->isRenewal) {
	$fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
} else {
	$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
}
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);
$shopTable = Flux::config('FluxTables.ItemShopTable');
$itemDescTable = Flux::config('FluxTables.ItemDescTable');

// Get item id from storage
$col  = "nameid, refine, attribute, bound, unique_id, enchantgrade, ";
$col .= "card0, card1, card2, card3, ";
$col .= "option_id0, option_val0, option_id1, option_val1, option_id2, option_val2, option_id3, option_val3, option_id4, option_val4";
$sql  = "SELECT $col FROM {$server->charMapDatabase}.$storageTable WHERE id = ?";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array($index));
$itemData = $sth->fetch();

if($server->isRenewal) {
	$temp = array();
	if ($itemData->option_id0)	array_push($temp, array($itemData->option_id0, $itemData->option_val0));
	if ($itemData->option_id1) 	array_push($temp, array($itemData->option_id1, $itemData->option_val1));
	if ($itemData->option_id2) 	array_push($temp, array($itemData->option_id2, $itemData->option_val2));
	if ($itemData->option_id3) 	array_push($temp, array($itemData->option_id3, $itemData->option_val3));
	if ($itemData->option_id4) 	array_push($temp, array($itemData->option_id4, $itemData->option_val4));
	$itemData->rndopt = $temp;
}

$itemID = $itemData->nameid;

$job_list = array_keys($this->GetJobsList($server->isRenewal));
$class_list = array_keys($this->GetClassList($server->isRenewal));
$equip_list = array_keys(Flux::config('EquipLocations')->toArray());
$trade_list = array_keys(Flux::config('TradeRestriction')->toArray());

$col  = 'items.id AS item_id, name_aegis AS identifier, ';
$col .= 'name_english AS name, type, subtype, ';
$col .= 'price_buy, price_sell, weight/10 AS weight, attack, defense, `range`, slots, gender, ';
$col .= 'weapon_level, equip_level_min, equip_level_max, refineable, view, alias_name, ';
$col .= 'script, equip_script, unequip_script, origin_table, ';
$col .= implode(', ', $job_list).', ';		// Job list
$col .= implode(', ', $class_list).', ';	// Class list
$col .= implode(', ', $equip_list).', ';
$col .= implode(', ', $trade_list).', ';	// Trade restriction list

$col .= "$shopTable.cost, $shopTable.id AS shop_item_id, ";
if(Flux::config('ShowItemDesc')){
    $col .= 'itemdesc, ';
}
if($server->isRenewal)	$col .= 'magic_attack, ';
$col .= 'origin_table';

$sql  = "SELECT $col FROM {$server->charMapDatabase}.items ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.$shopTable ON $shopTable.nameid = items.id ";
if(Flux::config('ShowItemDesc')){
    $sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.$itemDescTable ON $itemDescTable.itemid = items.id ";
}
$sql .= "WHERE items.id = ? LIMIT 1";

$sth  = $server->connection->getStatement($sql);
$sth->execute(array($itemID));

$item = $sth->fetch();
$isCustom = null;

if ($item) {
	$title = "Viewing Item ($item->name)";
	$isCustom = (bool)preg_match('/item_db2$/', $item->origin_table);

	// Jobs
	$jobs = array();
	foreach($job_list as $job) if($item->$job) $jobs[] = $job;
	// Classes
	$upper = array();
	foreach($class_list as $class) if($item->$class) $upper[] = $class;
	// Equip location
	$equip_locs = array();
	foreach($equip_list as $eq_loc) if($item->$eq_loc) $equip_locs[] = $eq_loc;
	// Trade restrictions
	$restrictions = array();
	foreach($trade_list as $trade) if($item->$trade) $restrictions[] = $trade;
	
	$cardIDs = array();
	$item_cards = array();

	$itemData->cardsOver = -$item->slots;
	
	if ($itemData->card0) {
		$cardIDs[] = $itemData->card0;
		$itemData->cardsOver++;
	}
	if ($itemData->card1) {
		$cardIDs[] = $itemData->card1;
		$itemData->cardsOver++;
	}
	if ($itemData->card2) {
		$cardIDs[] = $itemData->card2;
		$itemData->cardsOver++;
	}
	if ($itemData->card3) {
		$cardIDs[] = $itemData->card3;
		$itemData->cardsOver++;
	}
	
	if ($itemData->card0 == 254 || $itemData->card0 == 255 || $itemData->card0 == -256 || $itemData->cardsOver < 0) {
		$itemData->cardsOver = 0;
	}

	if ($cardIDs) {
		$ids = implode(',', array_fill(0, count($cardIDs), '?'));
		$sql = "SELECT id, name_english FROM {$server->charMapDatabase}.items WHERE id IN ($ids)";
		$sth = $server->connection->getStatement($sql);

		$sth->execute($cardIDs);
		$temp = $sth->fetchAll();

		if ($temp) {
			foreach ($temp as $card) {
				$item_cards[$card->id] = $card->name_english;
			}
		}
	}
}
?>
