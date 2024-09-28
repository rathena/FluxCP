<?php
if (!defined('FLUX_ROOT')) {
    exit;
}

require_once 'Flux/TemporaryTable.php';

$title = "Items for Sale";

try {
    if ($server->isRenewal) {
        $fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
    } else {
        $fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
    }
    $itemDB = "{$server->charMapDatabase}.items";
    $tempTable = new Flux_TemporaryTable($server->connection, $itemDB, $fromTables);
    
    $bind = array();
    $sqlpartial = "WHERE 1=1 ";
    $itemName = $params->get('name');
    
    if ($itemName) {
        $sqlpartial .= "AND (i.name_japanese LIKE ? OR i.name_japanese = ?) ";
        $bind[]      = "%$itemName%";
        $bind[]      = $itemName;
    }

    $sql = "SELECT COUNT(vending_id) AS total FROM vending_items AS vi "
         . "LEFT JOIN cart_inventory AS ci on ci.id = vi.cartinventory_id "
         . "LEFT JOIN items i ON i.id = ci.nameid ";
    $sth = $server->connection->getStatement("$sql $sqlpartial");
    $sth->execute($bind);
    $paginator = $this->getPaginator($sth->fetch()->total);

    $sortable = array('title' => 'asc', 'merchant', 'item_name');
    $paginator->setSortableColumns($sortable);
    
    $cols = "i.name_japanese AS item_name, i.slots, i.type"
          . ", vi.cartinventory_id, vi.amount, vi.price"
          . ", v.title, v.map, v.x, v.y, v.id AS shop_id, char.name AS merchant"
          . ", ci.nameid AS item_id, ci.refine, ci.card0, ci.card1, ci.card2, ci.card3";
    $sql = "SELECT $cols FROM  vending_items AS vi "
         . "LEFT JOIN cart_inventory AS ci on vi.cartinventory_id = ci.id "
         . "LEFT JOIN vendings AS v ON v.id = vi.vending_id "
         . "LEFT JOIN `char` ON `char`.char_id = v.char_id "
         . "LEFT JOIN items AS i ON i.id = ci.nameid ";
    $sql = $paginator->getSQL("$sql $sqlpartial");
    $sth = $server->connection->getStatement($sql);
    $sth->execute($bind);
    $items = $sth->fetchAll();
    
    $cards = array();
    $itemAttributes = Flux::config('Attributes')->toArray();
    if ($items) {
        $cardIDs = array();
    
        foreach ($items as $item) {
            $item->cardsOver = -$item->slots;
    
            for ($n = 0; $n <= 3; $n++) {
                $idx = "card$n";
                if ($item->$idx) {
                    if (!in_array($item->$idx, $cardIDs)) {
                        $cardIDs[] = $item->$idx;
                    }
                    $item->cardsOver++;
                }
            }
            
            if ($item->card0 == 254 || $item->card0 == 255 || $item->card0 == -256 || $item->cardsOver < 0) {
                $item->cardsOver = 0;
            }
        }
        
        if ($cardIDs) {
            $ids = implode(',', array_fill(0, count($cardIDs), '?'));
            $sql = "SELECT id, name_japanese FROM {$server->charMapDatabase}.items WHERE id IN ($ids)";
            $sth = $server->connection->getStatement($sql);
    
            $sth->execute($cardIDs);
            $temp = $sth->fetchAll();
            if ($temp) {
                foreach ($temp as $card) {
                    $cards[$card->id] = $card->name_japanese;
                }
            }
        }
    }
} catch (Exception $e) {
    $items = array();
}
