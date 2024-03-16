<?php
if (!defined('FLUX_ROOT')) exit;

require_once 'Flux/TemporaryTable.php';

// Get the current Vendor values.
$sql = "SELECT `char`.name as char_name, `buyingstores`.id, `buyingstores`.sex, `buyingstores`.map, `buyingstores`.x, `buyingstores`.y, `buyingstores`.title, autotrade ";
$sql .= "FROM buyingstores ";
$sql .= "LEFT JOIN `char` on buyingstores.char_id = `char`.char_id where id=?";
$sth = $server->connection->getStatement($sql);
$sth->execute(array($params->get("id")));
$store = $sth->fetch();

if ($store) {
	$title = 'Buyer Items Of [' . $store->char_name . ']';

// Create the itemdb temp table to retrieve names.
	if ($server->isRenewal) {
		$fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
	} else {
		$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
	}
	$itemDB = "{$server->charMapDatabase}.items";
	$tempTable = new Flux_TemporaryTable($server->connection, $itemDB, $fromTables);

// Get the buyer's items.
// Get the current buyer values.
	$sql = "SELECT `buyingstore_items`.`buyingstore_id`, `buyingstore_items`.`index`, `buyingstore_items`.`amount`, `buyingstore_items`.`price`";
	$sql .= ",`buyingstore_items`.`item_id` as nameid";
	$sql .= ",`items`.`name_english` as item_name, `items`.`slots`, `items`.`type` ";
	$sql .= "FROM buyingstore_items ";
	$sql .= "LEFT JOIN items on `buyingstore_items`.item_id = items.id ";
	$sql .= "WHERE `buyingstore_id` = ? ";
	$sql .= "ORDER BY `index` ";
	$sth = $server->connection->getStatement($sql);
	$sth->execute(array($store->id));
	$items = $sth->fetchAll();

	$itemAttributes = Flux::config('Attributes')->toArray();
} else {
	$title = "No Buyer Found.";
}
