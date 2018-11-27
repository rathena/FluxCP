<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$txnTable = Flux::config('FluxTables.TransactionTable');

/** Completed Transactions **/

$sqlpartial  = "WHERE account_id = ? AND hold_until IS NULL AND payment_status = 'Completed' ";
$sqlpartial .= "ORDER BY payment_date DESC";

$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$txnTable $sqlpartial";
$sth = $server->connection->getStatement($sql);

$sth->execute(array($session->account->account_id));
$completedTotal = $sth->fetch()->total;

$col = "*";
$sql = "SELECT $col FROM {$server->loginDatabase}.$txnTable $sqlpartial";
$sth = $server->connection->getStatement($sql);

$sth->execute(array($session->account->account_id));
$completedTxn = $sth->fetchAll();

/** Held Transactions **/

$sqlpartial  = "WHERE account_id = ? AND hold_until IS NOT NULL AND payment_status = 'Completed' ";
$sqlpartial .= "ORDER BY payment_date DESC";

$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$txnTable $sqlpartial";
$sth = $server->connection->getStatement($sql);

$sth->execute(array($session->account->account_id));
$heldTotal = $sth->fetch()->total;

$col = "*";
$sql = "SELECT $col FROM {$server->loginDatabase}.$txnTable $sqlpartial";
$sth = $server->connection->getStatement($sql);

$sth->execute(array($session->account->account_id));
$heldTxn = $sth->fetchAll();

/** Failed Transactions **/

$sqlpartial  = "WHERE account_id = ? AND hold_until IS NULL AND payment_status = 'Completed' ";
$sqlpartial .= "AND credits < 1 ORDER BY payment_date DESC";

$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$txnTable $sqlpartial";
$sth = $server->connection->getStatement($sql);

$sth->execute(array($session->account->account_id));
$failedTotal = $sth->fetch()->total;

$col = "*";
$sql = "SELECT $col FROM {$server->loginDatabase}.$txnTable $sqlpartial";
$sth = $server->connection->getStatement($sql);

$sth->execute(array($session->account->account_id));
$failedTxn = $sth->fetchAll();
?>
