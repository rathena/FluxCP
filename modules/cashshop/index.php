<?php
if (!defined('FLUX_ROOT')) exit; 

$this->loginRequired();

if (!$auth->allowedToManageCashShop) {
	$this->deny();
}
$title = 'CashShop';

require_once 'Flux/TemporaryTable.php';

$tabs = Flux::config('CashShopCategories')->toArray();

if($server->isRenewal) {
	$fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
} else {
	$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
}
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);

$col = "cash.tab AS tab, cash.item_id AS item_id, cash.price AS price, items.name_english AS item_name";
$sql = "SELECT $col FROM {$server->charMapDatabase}.`item_cash_db` AS cash ";
$sql.= "LEFT OUTER JOIN {$server->charMapDatabase}.items ON items.id = cash.item_id ORDER BY tab";
$sth = $server->connection->getStatement($sql);

$sth->execute();
$items = $sth->fetchAll();

?>
