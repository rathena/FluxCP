<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Viewing Item';

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

$itemID = $params->get('id');

$job_list = array_keys(Flux::config('EquipJobs')->toArray());
$class_list = array_keys(Flux::config('EquipUpper')->toArray());
$equip_list = array_keys(Flux::config('EquipLocations')->toArray());
$trade_list = array_keys(Flux::config('TradeRestriction')->toArray());

if(!$server->isRenewal) {
	array_splice($job_list, 25);
	array_splice($class_list, 4);
}

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

	$mobDB      = "{$server->charMapDatabase}.monsters";
	if($server->isRenewal) {
		$fromTables = array("{$server->charMapDatabase}.mob_db_re", "{$server->charMapDatabase}.mob_db2_re");
	} else {
		$fromTables = array("{$server->charMapDatabase}.mob_db", "{$server->charMapDatabase}.mob_db2");
	}
	$mobTable   = new Flux_TemporaryTable($server->connection, $mobDB, $fromTables);

	$col  = 'id AS monster_id, name_english AS monster_name, level AS monster_level, ';
	$col .= 'race AS monster_race, element AS monster_element, element_level AS monster_ele_lv, mvp_exp, ';

	// Normal drops.
	$col .= 'drop1_item, drop1_rate, drop1_nosteal, drop1_option, drop1_index, ';
	$col .= 'drop2_item, drop2_rate, drop2_nosteal, drop2_option, drop2_index, ';
	$col .= 'drop3_item, drop3_rate, drop3_nosteal, drop3_option, drop3_index, ';
	$col .= 'drop4_item, drop4_rate, drop4_nosteal, drop4_option, drop4_index, ';
	$col .= 'drop5_item, drop5_rate, drop5_nosteal, drop5_option, drop5_index, ';
	$col .= 'drop6_item, drop6_rate, drop6_nosteal, drop6_option, drop6_index, ';
	$col .= 'drop7_item, drop7_rate, drop7_nosteal, drop7_option, drop7_index, ';
	$col .= 'drop8_item, drop8_rate, drop8_nosteal, drop8_option, drop8_index, ';
	$col .= 'drop9_item, drop9_rate, drop9_nosteal, drop9_option, drop9_index, ';
	$col .= 'drop10_item, drop10_rate, drop10_nosteal, drop10_option, drop10_index, ';

	// MVP rewards.
	$col .= 'mvpdrop1_item, mvpdrop1_rate, mvpdrop1_option, mvpdrop1_index, ';
	$col .= 'mvpdrop2_item, mvpdrop2_rate, mvpdrop2_option, mvpdrop2_index, ';
	$col .= 'mvpdrop3_item, mvpdrop3_rate, mvpdrop3_option, mvpdrop3_index ';

	$sql  = "SELECT $col FROM $mobDB WHERE ";

	// Normal drops.
	$sql .= 'drop1_item = ? OR ';
	$sql .= 'drop2_item = ? OR ';
	$sql .= 'drop3_item = ? OR ';
	$sql .= 'drop4_item = ? OR ';
	$sql .= 'drop5_item = ? OR ';
	$sql .= 'drop6_item = ? OR ';
	$sql .= 'drop7_item = ? OR ';
	$sql .= 'drop8_item = ? OR ';
	$sql .= 'drop9_item = ? OR ';
	$sql .= 'drop10_item = ? OR ';

	// MVP rewards.
	$sql .= 'mvpdrop1_item = ? OR ';
	$sql .= 'mvpdrop2_item = ? OR ';
	$sql .= 'mvpdrop3_item = ? ';

	$sth  = $server->connection->getStatement($sql);
	$res = $sth->execute(array_fill(0, 13, $item->identifier));

	$dropResults = $sth->fetchAll();
	$itemDrops   = array();
	$dropNames   = array(
		'drop1', 'drop2', 'drop3', 'drop4', 'drop5', 'drop6', 'drop7', 'drop8', 'drop9', 'drop10',
		'mvpdrop1', 'mvpdrop2', 'mvpdrop3'
	);

	// Sort callback.
	function __tmpSortDrops($arr1, $arr2)
	{
		if ($arr1['drop_rate'] == $arr2['drop_rate']) {
			return strcmp($arr1['monster_name'], $arr2['monster_name']);
		}

		return $arr1['drop_rate'] < $arr2['drop_rate'] ? 1 : -1;
	}

	foreach ($dropResults as $drop) {
		foreach ($dropNames as $dropName) {
			$dropID     = $drop->{$dropName.'_item'};
			$dropChance = $drop->{$dropName.'_rate'};
			$dropSteal  = $drop->{$dropName.'_nosteal'};

			if ($dropID == $item->identifier) {
				$dropArray = array(
					'monster_id'		=> $drop->monster_id,
					'monster_name'		=> $drop->monster_name,
					'monster_level'		=> $drop->monster_level,
					'monster_race'		=> $drop->monster_race,
					'monster_element'	=> $drop->monster_element,
					'monster_ele_lv'	=> $drop->monster_ele_lv,
					'drop_item'			=> $itemID,
					'drop_rate'			=> $dropChance,
					'drop_steal'		=> ($dropSteal ? 'NoLabel' : 'YesLabel')
				);

				if (preg_match('/^mvp/', $dropName)) {
					$adjust = $server->dropRates['MvpItem'];
					$dropArray['type'] = 'mvp';
					$dropArray['drop_steal'] = 'NoLabel';
				}
				elseif (preg_match('/^drop/', $dropName)) {
					switch($item->type) {
						case 'Healing':
							$adjust = ($drop->mvp_exp) ? $server->dropRates['HealBoss'] : $server->dropRates['Heal'];
							break;

						case 'Usable':
						case 'Cash':
							$adjust = ($drop->mvp_exp) ? $server->dropRates['UseableBoss'] : $server->dropRates['Useable'];
							break;

						case 'Weapon':
						case 'Armor':
						case 'Petarmor':
							$adjust = ($drop->mvp_exp) ? $server->dropRates['EquipBoss'] : $server->dropRates['Equip'];
							break;

						case 'Card':
							$adjust = ($drop->mvp_exp) ? $server->dropRates['CardBoss'] : $server->dropRates['Card'];
							break;

						default:
							$adjust = ($drop->mvp_exp) ? $server->dropRates['CommonBoss'] : $server->dropRates['Common'];
							break;
					}

					$dropArray['type'] = 'normal';
				}

				$dropArray['drop_rate'] = $dropArray['drop_rate'] * $adjust / 10000;

				if ($dropArray['drop_rate'] > 100) {
					$dropArray['drop_rate'] = 100;
				}

				$itemDrops[] = $dropArray;
			}
		}
	}

	// Sort so that monsters are ordered by drop chance and name.
	usort($itemDrops, '__tmpSortDrops');
}
?>
