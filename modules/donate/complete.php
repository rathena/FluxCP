<?php
if (!defined('FLUX_ROOT')) exit;

//$this->loginRequired();

$ppReturn = $session->ppReturn;
$session->setPpReturnData(null);

//$txnLogTable = Flux::config('FluxTables.TransactionTable');
//
//$sql  = "SELECT id FROM {$server->loginDatabase}.$txnLogTable ";
//$sql .= "WHERE txn_id = :txn_id AND txn_type = :txn_type AND first_name = :first_name AND last_name = :last_name ";
//$sql .= " AND item_name = :item_name LIMIT 1";
//$sth  = $server->connection->getStatement($sql);

//if (!$ppReturn || !$sth->execute($ppReturn) || !$sth->fetch()) {
if (!$ppReturn) {
	$this->deny();
}
?>