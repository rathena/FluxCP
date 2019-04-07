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

$col  = 'items.id AS item_id, name_english AS identifier, ';
$col .= 'name_japanese AS name, type, ';
$col .= 'price_buy, price_sell, weight/10 AS weight, defence, `range`, slots, ';
$col .= 'equip_jobs, equip_upper, equip_genders, equip_locations, ';
$col .= 'weapon_level, equip_level AS equip_level_min, refineable, view, script, ';
$col .= 'equip_script, unequip_script, origin_table, ';
$col .= "$shopTable.cost, $shopTable.id AS shop_item_id, ";
if(Flux::config('ShowItemDesc')){
    $col .= 'itemdesc, ';
}
$col .= $server->isRenewal ? '`atk:matk` AS attack' : 'attack';

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

	if($server->isRenewal) {
		$item = $this->itemFieldExplode($item, 'attack', ':', array('attack','matk'));
		$item = $this->itemFieldExplode($item, 'equip_level_min', ':', array('equip_level_min','equip_level_max'));
	}

	$mobDB      = "{$server->charMapDatabase}.monsters";
	if($server->isRenewal) {
		$fromTables = array("{$server->charMapDatabase}.mob_db_re", "{$server->charMapDatabase}.mob_db2_re");
	} else {
		$fromTables = array("{$server->charMapDatabase}.mob_db", "{$server->charMapDatabase}.mob_db2");
	}
	$mobTable   = new Flux_TemporaryTable($server->connection, $mobDB, $fromTables);

	$col  = 'ID AS monster_id, iName AS monster_name, LV AS monster_level, ';
	$col .= 'Race AS monster_race, (Element%10) AS monster_element, (Element/20) AS monster_ele_lv, MEXP AS mvp_exp, ';

	// Normal drops.
	$col .= 'Drop1id AS drop1_id, Drop1per AS drop1_chance, ';
	$col .= 'Drop2id AS drop2_id, Drop2per AS drop2_chance, ';
	$col .= 'Drop3id AS drop3_id, Drop3per AS drop3_chance, ';
	$col .= 'Drop4id AS drop4_id, Drop4per AS drop4_chance, ';
	$col .= 'Drop5id AS drop5_id, Drop5per AS drop5_chance, ';
	$col .= 'Drop6id AS drop6_id, Drop6per AS drop6_chance, ';
	$col .= 'Drop7id AS drop7_id, Drop7per AS drop7_chance, ';
	$col .= 'Drop8id AS drop8_id, Drop8per AS drop8_chance, ';
	$col .= 'Drop9id AS drop9_id, Drop9per AS drop9_chance, ';

	// Card drops.
	$col .= 'DropCardid AS dropcard_id, DropCardper AS dropcard_chance, ';

	// MVP rewards.
	$col .= 'MVP1id AS mvpdrop1_id, MVP1per AS mvpdrop1_chance, ';
	$col .= 'MVP2id AS mvpdrop2_id, MVP2per AS mvpdrop2_chance, ';
	$col .= 'MVP3id AS mvpdrop3_id, MVP3per AS mvpdrop3_chance';

	$sql  = "SELECT $col FROM $mobDB WHERE ";

	// Normal drops.
	$sql .= 'Drop1id = ? OR ';
	$sql .= 'Drop2id = ? OR ';
	$sql .= 'Drop3id = ? OR ';
	$sql .= 'Drop4id = ? OR ';
	$sql .= 'Drop5id = ? OR ';
	$sql .= 'Drop6id = ? OR ';
	$sql .= 'Drop7id = ? OR ';
	$sql .= 'Drop8id = ? OR ';
	$sql .= 'Drop9id = ? OR ';

	// Card drops.
	$sql .= 'DropCardid = ? OR ';

	// MVP rewards.
	$sql .= 'MVP1id = ? OR ';
	$sql .= 'MVP2id = ? OR ';
	$sql .= 'MVP3id = ? ';

	$sth  = $server->connection->getStatement($sql);
	$res = $sth->execute(array_fill(0, 13, $itemID));

	$dropResults = $sth->fetchAll();
	$itemDrops   = array();
	$dropNames   = array(
		'drop1', 'drop2', 'drop3', 'drop4', 'drop5', 'drop6', 'drop7', 'drop8', 'drop9',
		'dropcard', 'mvpdrop1', 'mvpdrop2', 'mvpdrop3'
	);

	// Sort callback.
	function __tmpSortDrops($arr1, $arr2)
	{
		if ($arr1['drop_chance'] == $arr2['drop_chance']) {
			return strcmp($arr1['monster_name'], $arr2['monster_name']);
		}

		return $arr1['drop_chance'] < $arr2['drop_chance'] ? 1 : -1;
	}

	foreach ($dropResults as $drop) {
		foreach ($dropNames as $dropName) {
			$dropID     = $drop->{$dropName.'_id'};
			$dropChance = $drop->{$dropName.'_chance'};

			if ($dropID == $itemID) {
				$dropArray = array(
					'monster_id'      => $drop->monster_id,
					'monster_name'    => $drop->monster_name,
					'monster_level'   => $drop->monster_level,
					'monster_race'    => $drop->monster_race,
					'monster_element' => $drop->monster_element,
					'monster_ele_lv'  => $drop->monster_ele_lv,
					'drop_id'         => $itemID,
					'drop_chance'     => $dropChance
				);

				if (preg_match('/^dropcard/', $dropName)) {
					$adjust = ($drop->mvp_exp) ? $server->dropRates['CardBoss'] : $server->dropRates['Card'];
					$dropArray['type'] = 'card';
				}
				elseif (preg_match('/^mvp/', $dropName)) {
					$adjust = $server->dropRates['MvpItem'];
					$dropArray['type'] = 'mvp';
				}
				elseif (preg_match('/^drop/', $dropName)) {
					switch($item->type) {
						case 0: // Healing
							$adjust = ($drop->mvp_exp) ? $server->dropRates['HealBoss'] : $server->dropRates['Heal'];
							break;

						case 2: // Useable
						case 18: // Cash Useable
							$adjust = ($drop->mvp_exp) ? $server->dropRates['UseableBoss'] : $server->dropRates['Useable'];
							break;

						case 4: // Weapon
						case 5: // Armor
						case 8: // Pet Armor
							$adjust = ($drop->mvp_exp) ? $server->dropRates['EquipBoss'] : $server->dropRates['Equip'];
							break;

						default: // Common
							$adjust = ($drop->mvp_exp) ? $server->dropRates['CommonBoss'] : $server->dropRates['Common'];
							break;
					}

					$dropArray['type'] = 'normal';
				}

				$dropArray['drop_chance'] = $dropArray['drop_chance'] * $adjust / 10000;

				if ($dropArray['drop_chance'] > 100) {
					$dropArray['drop_chance'] = 100;
				}

				$itemDrops[] = $dropArray;
			}
		}
	}

	// Sort so that monsters are ordered by drop chance and name.
	usort($itemDrops, '__tmpSortDrops');
}
?>
