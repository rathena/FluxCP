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
    $sql_criteria .= '(`name_japanese` LIKE ? OR item_id = ?)';
    $sql_params[] = "%$item%";
    $sql_params[] = $item;
}

$buyingstore_table = 'buyingstores';
$buyingstore_items_table = 'buyingstore_items';

$col = "`ch`.`char_id`,`ch`.`name` as char_name ";
$col .= ",`$buyingstore_table`.id as buyid, `$buyingstore_table`.sex, `$buyingstore_table`.map, `$buyingstore_table`.x, `$buyingstore_table`.y, `$buyingstore_table`.title, autotrade ";
$col .= ",`$buyingstore_items_table`.`item_id` as nameid,`$buyingstore_items_table`.price,`$buyingstore_items_table`.amount";
$col .= ",items.`name_japanese` as item_name, items.`slots`, items.`type` ";

$sql = "FROM `$buyingstore_table` ";
$sql .= "LEFT JOIN `char` ch on `$buyingstore_table`.`char_id` = `ch`.char_id ";
$sql .= "LEFT JOIN `$buyingstore_items_table` on `$buyingstore_table`.`id`=`$buyingstore_items_table`.`buyingstore_id` ";
$sql .= "LEFT JOIN items on `$buyingstore_items_table`.item_id = items.`id` ";

if (count($sql_params)) {
    $sql .= 'WHERE '.$sql_criteria;
}

$sortable = array(
    'item_name' => 'ASC', 'nameid', 'price', 'title'
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
