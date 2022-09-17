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

	$mobDB      = "{$server->charMapDatabase}.monsters";
	if($server->isRenewal) {
		$fromTables = array("{$server->charMapDatabase}.mob_db_re", "{$server->charMapDatabase}.mob_db2_re");
	} else {
		$fromTables = array("{$server->charMapDatabase}.mob_db", "{$server->charMapDatabase}.mob_db2");
	}
	$mobTable   = new Flux_TemporaryTable($server->connection, $mobDB, $fromTables);

	$col  = 'id AS monster_id, name_english AS monster_name, level AS monster_level, ';
	$col .= 'race AS monster_race, element AS monster_element, element_level AS monster_ele_lv, mvp_exp, `class` as boss, mode_mvp, ';

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

				$is_boss = false;
				$is_mvp = false;
				if(!$drop->mode_mvp && $drop->boss)
					$is_boss = true;
				if($drop->mode_mvp && $drop->boss)
					$is_mvp = true;

				if (preg_match('/^mvp/', $dropName)) {
					$rate_adjust = $server->dropRates['MvpItem'];
					$ratemin = $server->dropRates['MvpItemMin'];
					$ratemax = $server->dropRates['MvpItemMax'];
					$dropArray['type'] = 'mvp';
					$dropArray['drop_steal'] = 'NoLabel';
				}
				elseif (preg_match('/^drop/', $dropName)) {
					switch($item->type) {
						case 'Healing':
							$rate_adjust = $is_mvp ? $server->dropRates['HealMVP'] : ($is_boss ? $server->dropRates['HealBoss'] : $server->dropRates['Heal']);
							$ratemin = $server->dropRates['HealMin'];
							$ratemax = $server->dropRates['HealMax'];
							break;

						case 'Usable':
						case 'Cash':
							$rate_adjust = $is_mvp ? $server->dropRates['UseableMVP'] : ($is_boss ? $server->dropRates['UseableBoss'] : $server->dropRates['Useable']);
							$ratemin = $server->dropRates['UseableMin'];
							$ratemax = $server->dropRates['UseableMax'];
							break;

						case 'Weapon':
						case 'Armor':
						case 'Petarmor':
							$rate_adjust = $is_mvp ? $server->dropRates['EquipMVP'] : ($is_boss ? $server->dropRates['EquipBoss'] : $server->dropRates['Equip']);
							$ratemin = $server->dropRates['EquipMin'];
							$ratemax = $server->dropRates['EquipMax'];
							break;

						case 'Card':
							$rate_adjust = $is_mvp ? $server->dropRates['CardMVP'] : ($is_boss ? $server->dropRates['CardBoss'] : $server->dropRates['Card']);
							$ratemin = $server->dropRates['CardMin'];
							$ratemax = $server->dropRates['CardMax'];
							break;

						default:
							$rate_adjust = $is_mvp ? $server->dropRates['CommonMVP'] : ($is_boss ? $server->dropRates['CommonBoss'] : $server->dropRates['Common']);
							$ratemin = $server->dropRates['CommonMin'];
							$ratemax = $server->dropRates['CommonMax'];
							break;
					}

					$dropArray['type'] = 'normal';
				}

				$ratemin /= 100;
				$ratemax /= 100;
				$ratecap = $server->dropRates['DropRateCap'] / 100;

				$dropArray['drop_rate'] = $this->cap_value($dropArray['drop_rate'] * $rate_adjust / 10000, $ratemin, $ratemax);

				if($dropArray['drop_rate'] > $ratecap)
					$dropArray['drop_rate'] = $ratecap;
				
				$itemDrops[] = $dropArray;
			}
		}
	}

	// Sort so that monsters are ordered by drop chance and name.
	usort($itemDrops, '__tmpSortDrops');
}
?>
