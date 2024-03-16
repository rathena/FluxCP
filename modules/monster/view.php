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

$mode_list = array('mode_aggressive', 'mode_angry', 'mode_assist', 'mode_canattack', 'mode_canmove', 'mode_castsensorchase', 'mode_castsensoridle', 'mode_changechase', 'mode_changetargetchase', 'mode_changetargetmelee', 'mode_detector', 'mode_fixeditemdrop', 'mode_ignoremagic', 'mode_ignoremelee', 'mode_ignoremisc', 'mode_ignoreranged', 'mode_knockbackimmune', 'mode_looter', 'mode_mvp', 'mode_norandomwalk', 'mode_randomtarget', 'mode_skillimmune', 'mode_statusimmune', 'mode_targetweak', 'mode_teleportblock');

$col  = 'origin_table, ID as monster_id, name_aegis AS sprite, name_english, name_japanese, level, HP AS hp, ';
$col .= 'base_exp, job_exp, attack_range, skill_range, chase_range, ';
$col .= 'defense, magic_defense, attack, attack2, ';
$col .= 'STR AS strength, AGI AS agility, VIT AS vitality, `INT` AS intelligence, DEX AS dexterity, LUK AS luck, ';
$col .= 'size, race, element, element_level, mode_canmove AS mode, `class` as boss, ';
$col .= 'walk_speed, attack_delay, attack_motion, damage_motion, ';
$col .= 'mvp_exp, ai, ';
$col .= implode(', ', $mode_list).', ';		// Mode list

// Item drops.
$col .= 'drop1_item, drop1_rate, drop1_nosteal, ';
$col .= 'drop2_item, drop2_rate, drop2_nosteal, ';
$col .= 'drop3_item, drop3_rate, drop3_nosteal, ';
$col .= 'drop4_item, drop4_rate, drop4_nosteal, ';
$col .= 'drop5_item, drop5_rate, drop5_nosteal, ';
$col .= 'drop6_item, drop6_rate, drop6_nosteal, ';
$col .= 'drop7_item, drop7_rate, drop7_nosteal, ';
$col .= 'drop8_item, drop8_rate, drop8_nosteal, ';
$col .= 'drop9_item, drop9_rate, drop9_nosteal, ';
$col .= 'drop10_item, drop10_rate, drop10_nosteal, ';

// MVP drops.
$col .= 'mvpdrop1_item, mvpdrop1_rate, ';
$col .= 'mvpdrop2_item, mvpdrop2_rate, ';
$col .= 'mvpdrop3_item, mvpdrop3_rate ';

$sql  = "SELECT $col FROM $mobDB WHERE ID = ? LIMIT 1";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array($mobID));
$monster = $sth->fetch();


if ($monster) {
	$title   = "Viewing Monster ({$monster->name_english})";

	// Mode
	$modes = array();
	foreach($mode_list as $mode) if($monster->$mode) $modes[] = $mode;
	
	$monster->base_exp = $monster->base_exp * $server->expRates['Base'] / 100;
	$monster->job_exp  = $monster->job_exp * $server->expRates['Job'] / 100;
	$monster->mvp_exp  = $monster->mvp_exp * $server->expRates['Mvp'] / 100;
	
	$dropIDs = array(
		'drop1'    => $monster->drop1_item,
		'drop2'    => $monster->drop2_item,
		'drop3'    => $monster->drop3_item,
		'drop4'    => $monster->drop4_item,
		'drop5'    => $monster->drop5_item,
		'drop6'    => $monster->drop6_item,
		'drop7'    => $monster->drop7_item,
		'drop8'    => $monster->drop8_item,
		'drop9'    => $monster->drop9_item,
		'drop10'   => $monster->drop10_item,
		'mvpdrop1' => $monster->mvpdrop1_item,
		'mvpdrop2' => $monster->mvpdrop2_item,
		'mvpdrop3' => $monster->mvpdrop3_item
	);
	
	$sql = "SELECT id, name_aegis, name_english, type FROM $itemDB WHERE name_aegis IN (".implode(', ', array_fill(0, count($dropIDs), '?')).")";
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
				if ($needToSet[$dropField] && $dropID == $item->name_aegis) {
					$needToSet[$dropField] = false;
					$monster->{$dropField.'_id'} = $item->id;
					$monster->{$dropField.'_name'} = $item->name_english;
					$monster->{$dropField.'_type'} = $item->type;
				}
			}
		}
	}
	
	$is_boss = false;
	$is_mvp = false;
	if(!$monster->mode_mvp && $monster->boss)
		$is_boss = true;
	if($monster->mode_mvp && $monster->boss)
		$is_mvp = true;

	$itemDrops = array();
	foreach ($needToSet as $dropField => $isset) {
		if ($isset === false) {
			$itemDrops[$dropField] = array(
				'id'     => $monster->{$dropField.'_id'},
				'name'   => $monster->{$dropField.'_name'},
				'type'   => $monster->{$dropField.'_type'},
				'chance' => $monster->{$dropField.'_rate'},
				'nosteal' => ($monster->{$dropField.'_nosteal'} ? 'NoLabel' : 'YesLabel')
			);

			if (preg_match('/^mvpdrop/', $dropField)) {
				$rate_adjust = $server->dropRates['MvpItem'];
				$ratemin = $server->dropRates['MvpItemMin'];
				$ratemax = $server->dropRates['MvpItemMax'];
				$itemDrops[$dropField]['type'] = 'mvp';
				$itemDrops[$dropField]['nosteal'] = 'NoLabel';
			}
			elseif (preg_match('/^drop/', $dropField)) {
				switch($monster->{$dropField.'_type'}) {
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
					
					default: // Common
						$rate_adjust = $is_mvp ? $server->dropRates['CommonMVP'] : ($is_boss ? $server->dropRates['CommonBoss'] : $server->dropRates['Common']);
						$ratemin = $server->dropRates['CommonMin'];
						$ratemax = $server->dropRates['CommonMax'];
						break;
				}
				
				$itemDrops[$dropField]['type'] = 'normal';
			}

			$ratemin /= 100;
			$ratemax /= 100;
			$ratecap = $server->dropRates['DropRateCap'] / 100;
			
			$itemDrops[$dropField]['chance'] = $this->cap_value($itemDrops[$dropField]['chance'] * $rate_adjust / 10000, $ratemin, $ratemax);

			if($itemDrops[$dropField]['chance'] > $ratecap)
				$itemDrops[$dropField]['chance'] = $ratecap;
		}
	}
	
	$sql = "SELECT * FROM $skillDB WHERE mob_id = ?";
	$sth = $server->connection->getStatement($sql);
	$sth->execute(array($mobID));
	$mobSkills = $sth->fetchAll();
}
?>
