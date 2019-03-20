<?php
if (!defined('FLUX_ROOT')) exit;

$itemID = $params->get('id');
if (!$itemID) {
	$this->deny();
}

$title = 'Duplicate Item';

require_once 'Flux/TemporaryTable.php';

if($server->isRenewal) {
    $fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
    $customTable = 'item_db2_re';
} else {
    $fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
    $customTable = 'item_db2';
}
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);

$col  = "name_english, name_japanese, type, price_buy, price_sell, ";
$col .= "weight, defence, `range`, slots, equip_jobs, equip_upper, ";
$col .= "equip_genders, equip_locations, weapon_level, equip_level, refineable, ";
$col .= "view, script, equip_script, unequip_script, ";
$col .= ($server->isRenewal) ? "`atk:matk` AS attack" : "attack";

$sql = "SELECT $col FROM $tableName WHERE id = ? LIMIT 1";
$sth = $server->connection->getStatement($sql);
$sth->execute(array($itemID));

$item = $sth->fetch();

if ($item) {
	$title = "Duplicate Item ({$item->name_japanese}: #$itemID)";
}

if ($item && count($_POST) && $params->get('copyitem')) {
	$isCustom = preg_match('/item_db2$/', $item->origin_table) ? true : false;
	$copyID   = trim($params->get('new_item_id'));

	if (!$copyID) {
		$errorMessage = 'You must specify a duplicate item ID.';
	}
	elseif (!ctype_digit($copyID)) {
		$errorMessage = 'Duplicate item ID must be a number.';
	}
	else {
		$sql = "SELECT COUNT(id) AS itemExists FROM {$server->charMapDatabase}.{$customTable} WHERE id = ?";
		$sth = $server->connection->getStatement($sql);
		$res = $sth->execute(array($copyID));

		if ($res && $sth->fetch()->itemExists) {
			$errorMessage = 'An item with that ID already exists in '.$customTable.'.';
		}
		else {
			$col  = "id, name_english, name_japanese, type, price_buy, price_sell, ";
			$col .= "weight, defence, `range`, slots, equip_jobs, equip_upper, ";
			$col .= "equip_genders, equip_locations, weapon_level, equip_level, refineable, ";
			$col .= "view, script, equip_script, unequip_script, ";
			$col .= ($server->isRenewal) ? "`atk:matk`" : "attack";
			$neweng = $item->name_english.$copyID;
			$newjap = $item->name_japanese.$copyID;

			$bind = array(
				$copyID, $neweng, $newjap, $item->type, $item->price_buy, $item->price_sell,
				$item->weight, $item->defence, $item->range, $item->slots, $item->equip_jobs, $item->equip_upper,
				$item->equip_genders, $item->equip_locations, $item->weapon_level, $item->equip_level, $item->refineable,
				$item->view, $item->script, $item->equip_script, $item->unequip_script, $item->attack
			);

			$sql  = "INSERT INTO {$server->charMapDatabase}.{$customTable} ($col) VALUES (".implode(',', array_fill(0, count($bind), '?')).")";
			$sth  = $server->connection->getStatement($sql);
			$res  = $sth->execute($bind);

			if ($res) {
				$session->setMessageData("Item has been duplicated as #$copyID!");

				if ($auth->actionAllowed('item', 'view')) {
					$this->redirect($this->url('item', 'view', array('id' => $copyID)));
				}
				else {
					$this->redirect();
				}
			}
			else {
				$errorMessage = 'Failed to duplicate item.';
			}
		}
	}
}
?>
