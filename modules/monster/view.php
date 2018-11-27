<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Viewing Monster';
$mobID = $params->get('id');

require_once 'Flux/TemporaryTable.php';

// Monsters table.
$mobDB      = "{$server->charMapDatabase}.monsters";
//here needs the same check if the server is renewal or not, I'm just lazy to do it by myself
if($server->isRenewal) {
	$fromTables = array("{$server->charMapDatabase}.mob_db_re", "{$server->charMapDatabase}.mob_db2_re");
} else {
 	$fromTables = array("{$server->charMapDatabase}.mob_db", "{$server->charMapDatabase}.mob_db2");
}
$tempMobs   = new Flux_TemporaryTable($server->connection, $mobDB, $fromTables);

// Monster Skills table.
$skillDB    = "{$server->charMapDatabase}.mobskills";
if($server->isRenewal) {
	$fromTables = array("{$server->charMapDatabase}.mob_skill_db_re", "{$server->charMapDatabase}.mob_skill_db2_re");
} else {
 	$fromTables = array("{$server->charMapDatabase}.mob_skill_db", "{$server->charMapDatabase}.mob_skill_db2");
}

$tempSkills = new Flux_TemporaryTable($server->connection, $skillDB, $fromTables);

// Items table.
if($server->isRenewal) {
	$fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
} else {
	$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
}
$itemDB    = "{$server->charMapDatabase}.items";
$tempItems = new Flux_TemporaryTable($server->connection, $itemDB, $fromTables);

$col  = 'origin_table, ID as monster_id, Sprite AS sprite, kName AS kro_name, iName AS iro_name, LV AS level, HP AS hp, ';
$col .= 'EXP AS base_exp, JEXP as job_exp, Range1 AS range1, Range2 AS range2, Range3 AS range3, ';
$col .= 'DEF AS defense, MDEF AS magic_defense, ATK1 AS attack1, ATK2 AS attack2, DEF AS defense, MDEF AS magic_defense, ';
$col .= 'STR AS strength, AGI AS agility, VIT AS vitality, `INT` AS intelligence, DEX AS dexterity, LUK AS luck, ';
$col .= 'Scale AS size, Race AS race, (Element%10) AS element_type, (Element/20) AS element_level, Mode AS mode, ';
$col .= 'Speed AS speed, aDelay AS attack_delay, aMotion AS attack_motion, dMotion AS delay_motion, ';
$col .= 'MEXP AS mvp_exp, ';

// Item drops.
$col .= 'Drop1id AS drop1_id, Drop1per AS drop1_chance, ';
$col .= 'Drop2id AS drop2_id, Drop2per AS drop2_chance, ';
$col .= 'Drop3id AS drop3_id, Drop3per AS drop3_chance, ';
$col .= 'Drop4id AS drop4_id, Drop4per AS drop4_chance, ';
$col .= 'Drop5id AS drop5_id, Drop5per AS drop5_chance, ';
$col .= 'Drop6id AS drop6_id, Drop6per AS drop6_chance, ';
$col .= 'Drop7id AS drop7_id, Drop7per AS drop7_chance, ';
$col .= 'Drop8id AS drop8_id, Drop8per AS drop8_chance, ';
$col .= 'Drop9id AS drop9_id, Drop9per AS drop9_chance, ';
$col .= 'DropCardid AS dropcard_id, DropCardper AS dropcard_chance, ';

// MVP drops.
$col .= 'MVP1id AS mvpdrop1_id, MVP1per AS mvpdrop1_chance, ';
$col .= 'MVP2id AS mvpdrop2_id, MVP2per AS mvpdrop2_chance, ';
$col .= 'MVP3id AS mvpdrop3_id, MVP3per AS mvpdrop3_chance ';

$sql  = "SELECT $col FROM $mobDB WHERE ID = ? LIMIT 1";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array($mobID));
$monster = $sth->fetch();

if ($monster) {
	$title   = "Viewing Monster ({$monster->kro_name})";
	
	$monster->boss = $monster->mvp_exp;
	
	$monster->base_exp = $monster->base_exp * $server->expRates['Base'] / 100;
	$monster->job_exp  = $monster->job_exp * $server->expRates['Job'] / 100;
	$monster->mvp_exp  = $monster->mvp_exp * $server->expRates['Mvp'] / 100;
	
	$dropIDs = array(
		'drop1'    => $monster->drop1_id,
		'drop2'    => $monster->drop2_id,
		'drop3'    => $monster->drop3_id,
		'drop4'    => $monster->drop4_id,
		'drop5'    => $monster->drop5_id,
		'drop6'    => $monster->drop6_id,
		'drop7'    => $monster->drop7_id,
		'drop8'    => $monster->drop8_id,
		'drop9'    => $monster->drop9_id,
		'dropcard' => $monster->dropcard_id,
		'mvpdrop1' => $monster->mvpdrop1_id,
		'mvpdrop2' => $monster->mvpdrop2_id,
		'mvpdrop3' => $monster->mvpdrop3_id
	);
	
	$sql = "SELECT id, name_japanese, type FROM $itemDB WHERE id IN (".implode(', ', array_fill(0, count($dropIDs), '?')).")";
	$sth = $server->connection->getStatement($sql);
	$sth->execute(array_values($dropIDs));
	$items = $sth->fetchAll();
	
	$needToSet = array();
	if ($items) {
		foreach ($dropIDs AS $dropField => $dropID) {
			$needToSet[$dropField] = true;
		}
		
		foreach ($items as $item) {
			foreach ($dropIDs AS $dropField => $dropID) {
				if ($needToSet[$dropField] && $dropID == $item->id) {
					$needToSet[$dropField] = false;
					$monster->{$dropField.'_name'} = $item->name_japanese;
					$monster->{$dropField.'_type'} = $item->type;
				}
			}
		}
	}
	
	$itemDrops = array();
	foreach ($needToSet as $dropField => $isset) {
		if ($isset === false) {
			$itemDrops[$dropField] = array(
				'id'     => $monster->{$dropField.'_id'},
				'name'   => $monster->{$dropField.'_name'},
				'chance' => $monster->{$dropField.'_chance'}
			);
			
			if (preg_match('/^dropcard/', $dropField)) {
				$adjust = ($monster->boss) ? $server->dropRates['CardBoss'] : $server->dropRates['Card'];
				$itemDrops[$dropField]['type'] = 'card';
			}
			elseif (preg_match('/^mvpdrop/', $dropField)) {
				$adjust = $server->dropRates['MvpItem'];
				$itemDrops[$dropField]['type'] = 'mvp';
			}
			elseif (preg_match('/^drop/', $dropField)) {
				switch($monster->{$dropField.'_type'}) {
					case 0: // Healing
						$adjust = ($monster->boss) ? $server->dropRates['HealBoss'] : $server->dropRates['Heal'];
						break;
					
					case 2: // Useable
					case 18: // Cash Useable
						$adjust = ($monster->boss) ? $server->dropRates['UseableBoss'] : $server->dropRates['Useable'];
						break;
					
					case 4: // Weapon
					case 5: // Armor
					case 8: // Pet Armor
						$adjust = ($monster->boss) ? $server->dropRates['EquipBoss'] : $server->dropRates['Equip'];
						break;
					
					default: // Common
						$adjust = ($monster->boss) ? $server->dropRates['CommonBoss'] : $server->dropRates['Common'];
						break;
				}
				
				$itemDrops[$dropField]['type'] = 'normal';
			}
			
			$itemDrops[$dropField]['chance'] = $itemDrops[$dropField]['chance'] * $adjust / 10000;

			if ($itemDrops[$dropField]['chance'] > 100) {
				$itemDrops[$dropField]['chance'] = 100;
			}
		}
	}
	
	$sql = "SELECT * FROM $skillDB WHERE mob_id = ?";
	$sth = $server->connection->getStatement($sql);
	$sth->execute(array($mobID));
	$mobSkills = $sth->fetchAll();
}
?>
