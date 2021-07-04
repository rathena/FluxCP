<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'Pending Redemption';

try {
	// Create item db temp table.
	require_once 'Flux/TemporaryTable.php';
	if($server->isRenewal) {
		$fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
	} else {
		$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
	}
	$tableName = "{$server->charMapDatabase}.items";
	$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);

	$redeemTable = Flux::config('FluxTables.RedemptionTable');

	// JOINs, conditions etc
	$sqlpartial  = "LEFT OUTER JOIN $tableName ON items.id = $redeemTable.nameid WHERE account_id = ? ";
	$sqlpartial .= "AND redeemed < 1 ORDER BY purchase_date DESC";
	
	// Fetch item count.
	$sql = "SELECT COUNT($redeemTable.id) AS total FROM {$server->charMapDatabase}.$redeemTable $sqlpartial";
	$sth = $server->connection->getStatement($sql);
	
	$sth->execute(array($session->account->account_id));
	$total = $sth->fetch()->total;

	// Fetch items.
	$col = "nameid, quantity, purchase_date, cost, credits_before, credits_after, items.name_english AS item_name";
	$sql = "SELECT $col FROM {$server->charMapDatabase}.$redeemTable $sqlpartial";
	$sth = $server->connection->getStatement($sql);
	
	$sth->execute(array($session->account->account_id));
	$items = $sth->fetchAll();
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
