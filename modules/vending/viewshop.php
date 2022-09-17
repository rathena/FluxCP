<?php

if (!defined('FLUX_ROOT'))
    exit;

require_once 'Flux/TemporaryTable.php';


// Get the current Vendor values.
$sql = "SELECT `char`.name as char_name, `vendings`.id, `vendings`.account_id, `vendings`.sex, `vendings`.map, `vendings`.x, `vendings`.y, `vendings`.title, autotrade ";
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
    $tempTable = new Flux_TemporaryTable($server->connection, $itemDB, $fromTables);

// Get the vendor's items.
// Get the current Vendor values.
    $sql = "SELECT `vending_items`.cartinventory_id, `vending_items`.amount, `vending_items`.price, ";
    $sql .= "`cart_inventory`.nameid, `cart_inventory`.refine, `cart_inventory`.card0, `cart_inventory`.card1, `cart_inventory`.card2, c.name as char_name, ";
    $sql .= "`cart_inventory`.option_id0, `cart_inventory`.option_val0, ";
    $sql .= "`cart_inventory`.option_id1, `cart_inventory`.option_val1, ";
    $sql .= "`cart_inventory`.option_id2, `cart_inventory`.option_val2, ";
    $sql .= "`cart_inventory`.option_id3, `cart_inventory`.option_val3, ";
    $sql .= "`cart_inventory`.option_id4, `cart_inventory`.option_val4, ";
    $sql .= "items.name_english as item_name, items.slots, items.type ";
    $sql .= "FROM vending_items ";
    $sql .= "LEFT JOIN `cart_inventory` on `vending_items`.cartinventory_id = `cart_inventory`.id ";

    $sql .= "LEFT JOIN items on `cart_inventory`.nameid = items.id ";
    
    $sql .= "LEFT JOIN {$server->charMapDatabase}.`char` AS c ";
	$sql .= "ON c.char_id = IF(cart_inventory.card0 IN (254, 255), ";
	$sql .= "IF(cart_inventory.card2 < 0, cart_inventory.card2 + 65536, cart_inventory.card2) ";
	$sql .= "| (cart_inventory.card3 << 16), NULL) ";

    
    $sql .= "where vending_id = ? ";
    $sth = $server->connection->getStatement($sql);
    $sth->execute(array($vending->id));
    $vending_items = $sth->fetchAll();
    $items=$vending_items;
    

    //Set the cards
    $cards = array();
    if ($items) {
		$cardIDs = array();

		foreach ($items as $item) {
			$item->cardsOver = -$item->slots;
			
			if ($item->card0) {
				$cardIDs[] = $item->card0;
				$item->cardsOver++;
			}
			if ($item->card1) {
				$cardIDs[] = $item->card1;
				$item->cardsOver++;
			}
			if ($item->card2) {
				$cardIDs[] = $item->card2;
				$item->cardsOver++;
			}
			if ($item->card3) {
				$cardIDs[] = $item->card3;
				$item->cardsOver++;
			}
			
			if ($item->card0 == 254 || $item->card0 == 255 || $item->card0 == -256 || $item->cardsOver < 0) {
				$item->cardsOver = 0;
			}

			if($server->isRenewal) {
				$temp = array();
				if ($item->option_id0)	array_push($temp, array($item->option_id0, $item->option_val0));
				if ($item->option_id1) 	array_push($temp, array($item->option_id1, $item->option_val1));
				if ($item->option_id2) 	array_push($temp, array($item->option_id2, $item->option_val2));
				if ($item->option_id3) 	array_push($temp, array($item->option_id3, $item->option_val3));
				if ($item->option_id4) 	array_push($temp, array($item->option_id4, $item->option_val4));
				$item->rndopt = $temp;
			}
		}
		
		if ($cardIDs) {
			$ids = implode(',', array_fill(0, count($cardIDs), '?'));
			$sql = "SELECT id, name_english FROM {$server->charMapDatabase}.items WHERE id IN ($ids)";
			$sth = $server->connection->getStatement($sql);

			$sth->execute($cardIDs);
			$temp = $sth->fetchAll();
			if ($temp) {
				foreach ($temp as $card) {
					$cards[$card->id] = $card->name_english;
				}
			}
		}
	}
    
    $itemAttributes = Flux::config('Attributes')->toArray();
	$type_list = Flux::config('ItemTypes')->toArray();

    
} else {
    $title = "No Vendor Found.";
}


?>
