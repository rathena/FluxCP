<?php
if (!defined('FLUX_ROOT')) exit;

require_once 'Flux/TemporaryTable.php';

$title = 'Buying Items';

if ($server->isRenewal) {
    $fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
} else {
    $fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
}
$itemDB = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $itemDB, $fromTables);

$item = trim($params->get('item'));

$sql_criteria = "";
$sql_params = array();
if ($item) {
    if (count($sql_params))
        $sql_criteria .= ' AND ';
    $sql_criteria .= ' `item_name` LIKE ? OR `nameid`=?';
    $sql_params[] = "%$item%";
    $sql_params[] = $item;
}

$buyingstore_table = 'buyingstores';
$buyingstore_items_table = 'buyingstore_items';

$sql = "SELECT `$buyingstore_table`.id as buyid, `$buyingstore_table`.sex, `$buyingstore_table`.map, `$buyingstore_table`.x, `$buyingstore_table`.y, `$buyingstore_table`.title, autotrade ";
$sql .= ",`$buyingstore_table`.char_id,`char`.name as char_name ";
$sql .= ",`$buyingstore_items_table`.`item_id` as nameid,`$buyingstore_items_table`.price,`$buyingstore_items_table`.amount";
$sql .= ",`items`.`name_japanese` as item_name, `items`.`slots`, `items`.`type` ";
$sql .= "FROM `$buyingstore_table` ";
$sql .= "LEFT JOIN `char` on `$buyingstore_table`.`char_id` = `char`.char_id ";
$sql .= "LEFT JOIN `$buyingstore_items_table` on `$buyingstore_table`.`id`=`$buyingstore_items_table`.`buyingstore_id` ";
$sql .= "LEFT JOIN items on `$buyingstore_items_table`.item_id = items.id ";

if (count($sql_params)) {
    $sql .= 'HAVING '.$sql_criteria;
}

$sortable = array(
    'item_name' => 'ASC', 'nameid', 'price', 'title'
);
$sth = $server->connection->getStatement($sql);
$sth->execute($sql_params);
$paginator = $this->getPaginator($sth->rowCount());
$paginator->setSortableColumns($sortable);

$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatement($sql);
$sth->execute($sql_params);

$items = $sth->fetchAll();
