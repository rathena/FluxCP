<?php
if (!defined('FLUX_ROOT')) exit;

//$this->loginRequired();

$title = 'List Monsters';

require_once 'Flux/TemporaryTable.php';

try {
	$tableName  = "{$server->charMapDatabase}.monsters";
	$fromTables = array("{$server->charMapDatabase}.mob_db", "{$server->charMapDatabase}.mob_db2");
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
		$cardID         = $params->get('card_id');
		$mvp            = strtolower($params->get('mvp'));
		$custom         = $params->get('custom');
		
		if ($monsterName) {
			$sqlpartial .= "AND ((kName LIKE ? OR kName = ?) OR (iName LIKE ? OR iName = ?)) ";
			$bind[]      = "%$monsterName%";
			$bind[]      = $monsterName;
			$bind[]      = "%$monsterName%";
			$bind[]      = $monsterName;
		}

		if ($size !== false && $size !== '-1') {
			if(is_numeric($size) && (floatval($size) == intval($size))) {
				$sizes = Flux::config('MonsterSizes')->toArray();
				if (array_key_exists($size, $sizes) && $sizes[$size]) {
					$sqlpartial .= "AND Scale = ? ";
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
						$partial .= "Scale = ? OR ";
						$bind[]   = $id;
					}
					
					$partial     = preg_replace('/\s*OR\s*$/', '', $partial);
					$sqlpartial .= "$partial) ";
				}
			}
		}

		if ($race !== false && $race !== '-1') {
			if(is_numeric($race) && (floatval($race) == intval($race))) {
				$races = Flux::config('MonsterRaces')->toArray();
				if (array_key_exists($race, $races) && $races[$race]) {
					$sqlpartial .= "AND Race = ? ";
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
						$partial .= "Race = ? OR ";
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
			if (is_numeric($element) && (floatval($element) == intval($element))) {
				$elements = Flux::config('Elements')->toArray();
				if (array_key_exists($element, $elements) && $elements[$element]) {
					$sqlpartial .= "AND Element%10 = ? ";
					$bind[]      = $element;
				} else {
					$sqlpartial .= 'AND Element IS NULL ';
				}
				
				if (count($elementSplit) == 2 && is_numeric($elementLevel) && (floatval($elementLevel) == intval($elementLevel))) {
					$sqlpartial .= "AND CAST(Element/20 AS UNSIGNED) = ? ";
					$bind[]      = $elementLevel;
				}
			} else {
				$elementName = preg_quote($element, '/');
				$elements    = preg_grep("/.*?$elementName.*?/i", Flux::config('Elements')->toArray());
				
				if (count($elements)) {
					$elements    = array_keys($elements);
					$sqlpartial .= "AND (";
					$partial     = '';
					
					foreach ($elements as $id) {
						$partial .= "Element%10 = ? OR ";
						$bind[]   = $id;
					}
					
					$partial     = preg_replace('/\s*OR\s*$/', '', $partial);
					$sqlpartial .= "$partial) ";
				} else {
					$sqlpartial .= 'AND Element IS NULL ';
				}
			}
		}
		
		if ($cardID) {
			$sqlpartial .= "AND DropCardid = ? ";
			$bind[]      = $cardID;
		}
		
		if ($mvp == 'yes') {
			$sqlpartial .= 'AND MEXP > 0 ';
		}
		elseif ($mvp == 'no') {
			$sqlpartial .= 'AND MEXP = 0 ';
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
		'monster_id' => 'asc', 'kro_name', 'iro_name', 'level', 'hp', 'size', 'race', 'exp', 'jexp', 'dropcard_id', 'origin_table'
	));
	
	$col  = "origin_table, monsters.ID AS monster_id, kName AS kro_name, iName AS iro_name, ";
	$col .= "LV AS level, HP AS hp, Scale as size, Race AS race, (Element%10) AS element_type, (Element/20) AS element_level, ";
	$col .= "EXP AS exp, JEXP AS jexp, DropCardid AS dropcard_id, mexp AS mvp_exp";
	
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
