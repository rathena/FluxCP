<?php
if (!defined('FLUX_ROOT')) exit;

require_once 'Flux/Item.php';
require_once 'Flux/TemporaryTable.php';

$title = 'Vending Items';

if ($server->isRenewal) {
    $fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
} else {
    $fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
}
$itemDB = "{$server->charMapDatabase}.items";
$itemLib = new Flux_Item($server);
$tempTable = new Flux_TemporaryTable($server->connection, $itemDB, $fromTables);

$item = trim($params->get('item'));
$refine = trim($params->get('refine'));
$card = trim($params->get('card'));

$sql_criteria = "";
$sql_params = array();
if ($refine) {
    $sql_criteria .= '`ci`.`refine`=?';
    $sql_params[] = $refine;
}
if ($card) {
    if (count($sql_params))
        $sql_criteria .= ' AND ';
    $sql_criteria .= '(`ci`.`card0`=? OR `ci`.`card1`=? OR `ci`.`card2`=? OR `ci`.`card3`=?) ';
    $sql_params[] = $card;
    $sql_params[] = $card;
    $sql_params[] = $card;
    $sql_params[] = $card;
}
if ($item) {
    if (count($sql_params))
        $sql_criteria .= ' AND ';
    $sql_criteria .= ' `name_japanese` LIKE ? OR `nameid`=?';
    $sql_params[] = "%$item%";
    $sql_params[] = $item;
}

$vending_table = 'vendings';
$vending_items_table = 'vending_items';

$col = "`ch`.char_id, `ch`.name as char_name, `$vending_table`.id as vending_id, `$vending_table`.account_id, `$vending_table`.sex, `$vending_table`.map, `$vending_table`.x, `$vending_table`.y, `$vending_table`.title, autotrade ";
$col .= ",`$vending_items_table`.`cartinventory_id`,`$vending_items_table`.`amount`,`$vending_items_table`.`price`";
$col .= ",ci.`nameid`,ci.`refine`,ci.`card0`,ci.`card1`,ci.`card2`,ci.`card3`";
$col .= $itemLib->select_string;

$sql = "FROM `$vending_items_table` ";
$sql .= "LEFT JOIN `$vending_table` ON `$vending_items_table`.vending_id = `$vending_table`.id ";
$sql .= "LEFT JOIN `cart_inventory` ci ON `$vending_items_table`.`cartinventory_id` = `ci`.id ";
$sql .= "LEFT JOIN `char` ch ON `$vending_table`.char_id = `ch`.char_id ";
$sql .= $itemLib->getJoinString('ci','nameid');
$sql .= $itemLib->getNamedItemString('ci');

if (count($sql_params)) {
    $sql .= 'WHERE '.$sql_criteria;
}

$sortable = array(
    'name_japanese' => 'ASC', 'nameid', 'price', 'amount'
);

$sth = $server->connection->getStatement("SELECT COUNT(`ch`.`char_id`) as total ".$sql);
$sth->execute($sql_params);
$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns($sortable);

$sql = "SELECT ".$col." ".$sql;
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatement($sql);
$sth->execute($sql_params);

$items = $sth->fetchAll();

if ($items) {
    //Set the cards
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
