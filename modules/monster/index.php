<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'List Monsters';

require_once 'Flux/TemporaryTable.php';

try {
	$tableName  = "{$server->charMapDatabase}.monsters";
	if($server->isRenewal) {
		$fromTables = array("{$server->charMapDatabase}.mob_db_re", "{$server->charMapDatabase}.mob_db2_re");
  	} else {
		$fromTables = array("{$server->charMapDatabase}.mob_db", "{$server->charMapDatabase}.mob_db2");
	}
	$tempTable  = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);
	
	// Statement parameters, joins and conditions.
	$bind        = array();
	$sqlpartial  = "WHERE 1=1 ";
	$monsterID   = $params->get('monster_id');
	
	if ($monsterID) {
		$sqlpartial .= "AND monsters.ID = ? ";
		$bind[]      = $monsterID;
	}
	else {
		$opMapping      = array('eq' => '=', 'gt' => '>', 'lt' => '<');
		$opValues       = array_keys($opMapping);
		$monsterName    = $params->get('name');
		$size           = $params->get('size');
		$race           = $params->get('race');
		$element        = $params->get('element');
		$mvp            = strtolower($params->get('mvp') ?: '');
		$custom         = $params->get('custom');
		
		if ($monsterName) {
			$sqlpartial .= "AND ((name_english LIKE ? OR name_english = ?) OR (name_japanese LIKE ? OR name_japanese = ?)) ";
			$bind[]      = "%$monsterName%";
			$bind[]      = $monsterName;
			$bind[]      = "%$monsterName%";
			$bind[]      = $monsterName;
		}

		if ($size && $size !== '-1') {
			if(is_numeric($size) && (floatval($size) == intval($size))) {
				$sizes = Flux::config('MonsterSizes')->toArray();
				if (array_key_exists($size, $sizes) && $sizes[$size]) {
					$sqlpartial .= "AND size = ? ";
					$bind[]      = $size;
				}
			} else {
				$sizeName = preg_quote($size, '/');
				$sizes = preg_grep("/.*?$sizeName.*?/i", Flux::config('MonsterSizes')->toArray());
				
				if (count($sizes)) {
					$sizes = array_keys($sizes);
					$sqlpartial .= "AND (";
					$partial     = '';
					
					foreach ($sizes as $id) {
						$partial .= "size = ? OR ";
						$bind[]   = $id;
					}
					
					$partial     = preg_replace('/\s*OR\s*$/', '', $partial);
					$sqlpartial .= "$partial) ";
				}
			}
		}

		if ($race && $race !== '-1') {
			if($race) {
				$races = Flux::config('MonsterRaces')->toArray();
				if (array_key_exists($race, $races) && $races[$race]) {
					$sqlpartial .= "AND race = ? ";
					$bind[]      = $race;
				}
			} else {
				$raceName = preg_quote($race, '/');
				$races = preg_grep("/.*?$raceName.*?/i", Flux::config('MonsterRaces')->toArray());
				
				if (count($races)) {
					$races = array_keys($races);
					$sqlpartial .= "AND (";
					$partial     = '';
					
					foreach ($races as $id) {
						$partial .= "race = ? OR ";
						$bind[]   = $id;
					}
					
					$partial     = preg_replace('/\s*OR\s*$/', '', $partial);
					$sqlpartial .= "$partial) ";
				}
			}
		}

		if ($element && $element !== '-1') {
			if (count($elementSplit = explode('-', $element)) == 2) {
				$element = $elementSplit[0];
				$elementLevel = $elementSplit[1];
			}
			if ($element) {
				$elements = Flux::config('Elements')->toArray();
				if (array_key_exists($element, $elements) && $elements[$element]) {
					$sqlpartial .= "AND element = ? ";
					$bind[]      = $element;
				} else {
					$sqlpartial .= 'AND element IS NULL ';
				}
				
				if (count($elementSplit) == 2 && $elementLevel) {
					$sqlpartial .= "AND element_level = ?" ;
					$bind[]      = $elementLevel;
				}
			}
		}
		
		if ($mvp == 'yes') {
			$sqlpartial .= 'AND mvp_exp > 0 ';
		}
		elseif ($mvp == 'no') {
			$sqlpartial .= 'AND mvp_exp = 0 ';
		}
		
		if ($custom) {
			if ($custom == 'yes') {
				$sqlpartial .= "AND origin_table LIKE '%mob_db2' ";
			}
			elseif ($custom == 'no') {
				$sqlpartial .= "AND origin_table LIKE '%mob_db' ";
			}
		}
	}
	
	// Get total count and feed back to the paginator.
	$sth = $server->connection->getStatement("SELECT COUNT(monsters.ID) AS total FROM $tableName $sqlpartial");
	$sth->execute($bind);
	
	$paginator = $this->getPaginator($sth->fetch()->total);
	$paginator->setSortableColumns(array(
		'monster_id' => 'asc', 'name_english', 'name_japanese', 'level', 'hp', 'size', 'race', 'base_exp', 'job_exp', 'origin_table'
	));
	
	$col  = "origin_table, monsters.ID AS monster_id, name_english, name_japanese, ";
	$col .= "level, hp, size, race, element, element_level, ";
	$col .= "base_exp, job_exp, mvp_exp";
	
	$sql  = $paginator->getSQL("SELECT $col FROM $tableName $sqlpartial");
	$sth  = $server->connection->getStatement($sql);
	
	$sth->execute($bind);
	$monsters = $sth->fetchAll();
	
	$authorized = $auth->actionAllowed('monster', 'view');
	
	if ($monsters && count($monsters) === 1 && $authorized && Flux::config('SingleMatchRedirectMobs')) {
		$this->redirect($this->url('monster', 'view', array('id' => $monsters[0]->monster_id)));
	}
}
catch (Exception $e) {
	if (isset($tempTable) && $tempTable) {
		// Ensure table gets dropped.
		$tempTable->drop();
	}
	
	// Raise the original exception.
	$class = get_class($e);
	throw new $class($e->getMessage());
}
?>
