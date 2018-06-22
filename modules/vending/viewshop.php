<?php

if (!defined('FLUX_ROOT'))
    exit;

require_once 'Flux/Item.php';
require_once 'Flux/TemporaryTable.php';


// Get the current Vendor values.
$sql = "SELECT `char`.`name` as char_name, `vendings`.`id`, `vendings`.`account_id`, `vendings`.`char_id`, `vendings`.`sex`, `vendings`.`map`, `vendings`.`x`, `vendings`.`y`, `vendings`.`title`, `autotrade` ";
$sql .= "FROM vendings ";
$sql .= "LEFT JOIN `char` on vendings.char_id = `char`.char_id where id=?";
$sth = $server->connection->getStatement($sql);
$sth->execute(array($params->get("id")));
$vending = $sth->fetch();

if ($vending) {
    $isMine = false;
    $title = 'Vending Items Of [' . $vending->char_name . ']';

    if ($vending->account_id == $session->account->account_id) {
        $isMine = true;
    }

// Create the itemdb temp table to retrieve names.
    if ($server->isRenewal) {
        $fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
    } else {
        $fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
    }
    $itemDB = "{$server->charMapDatabase}.items";
	$itemLib = new Flux_Item($server, 'ci', 'nameid');
    $tempTable = new Flux_TemporaryTable($server->connection, $itemDB, $fromTables);

// Get the vendor's items.
// Get the current Vendor values.
    $sql = "SELECT (".$vending->account_id.") AS account_id, (".$vending->char_id.") AS char_id, `vending_items`.`cartinventory_id`, `vending_items`.`amount`, `vending_items`.`price`, ";
    $sql .= "`ci`.`nameid`, `ci`.`refine`, `ci`.`card0`, `ci`.`card1`, `ci`.`card2`, `ci`.`card3`, c.`name` as char_name ";
    $sql .= $itemLib->select_string;
    $sql .= "FROM vending_items ";
    $sql .= "LEFT JOIN `cart_inventory` ci on `vending_items`.cartinventory_id = `ci`.id ";

    $sql .= $itemLib->join_string;
	$sql .= $itemLib->named_item_string;

    
    $sql .= "where vending_id = ? ";
    $sth = $server->connection->getStatement($sql);
    $sth->execute(array($vending->id));
    $items = $sth->fetchAll();

    //Set the cards
    $cards = array();
    if ($items) {
		$this->cardIDs = array();
		$items = $itemLib->prettyPrint($items, $this);

		if ($this->cardIDs) {
			$ids = implode(',', array_fill(0, count($this->cardIDs), '?'));
			$sql = "SELECT id, name_japanese FROM {$server->charMapDatabase}.items WHERE id IN ($ids)";
			$sth = $server->connection->getStatement($sql);

			$sth->execute($this->cardIDs);
			$temp = $sth->fetchAll();
			if ($temp) {
				foreach ($temp as $card) {
					$cards[$card->id] = $card->name_japanese;
				}
			}
		}
	}
} else {
    $title = "No Vendor Found.";
}


?>
