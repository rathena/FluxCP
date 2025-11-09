<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$txnPaypalTable = Flux::config('FluxTables.TransactionTable');
$txnStripeTable = Flux::config('FluxTables.StripeTransactionTable');
$account_id = $session->account->account_id;

/** Completed Transactions **/
$sqlpartial  = "WHERE cp.account_id = ? AND st.account_id = cp.account_id AND cp.hold_until IS NULL AND cp.payment_status = 'Completed' AND st.payment_status = 'paid' ";
$sqlpartial .= "ORDER BY cp.payment_date DESC";

$sql = "SELECT COUNT(*) AS total FROM {$server->loginDatabase}.$txnPaypalTable AS cp, {$server->loginDatabase}.$txnStripeTable AS st $sqlpartial";
$sth = $server->connection->getStatement($sql);

$sth->execute(array($account_id));
$completedTotal = $sth->fetch()->total;

$col = "*";
$sql = "SELECT $col FROM {$server->loginDatabase}.$txnPaypalTable AS cp, {$server->loginDatabase}.$txnStripeTable AS st $sqlpartial";
$sth = $server->connection->getStatement($sql);

$sth->execute(array($account_id));
$completedTxn = $sth->fetchAll();

/** Held Transactions **/

$sqlpartial  = "WHERE account_id = ? AND hold_until IS NOT NULL AND payment_status = 'Completed' ";
$sqlpartial .= "ORDER BY payment_date DESC";

$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$txnPaypalTable $sqlpartial";
$sth = $server->connection->getStatement($sql);

$sth->execute(array($account_id));
$heldTotal = $sth->fetch()->total;

$col = "*";
$sql = "SELECT $col FROM {$server->loginDatabase}.$txnPaypalTable $sqlpartial";
$sth = $server->connection->getStatement($sql);

$sth->execute(array($account_id));
$heldTxn = $sth->fetchAll();

/** Failed Transactions **/

$sqlpartial  = "WHERE cp.account_id = ? AND st.account_id = cp.account_id AND cp.hold_until IS NULL AND cp.payment_status = 'Completed' OR st.payment_status <> 'paid' ";
$sqlpartial .= "AND cp.credits < 1 ORDER BY cp.payment_date DESC";

$sql = "SELECT COUNT(*) AS total FROM {$server->loginDatabase}.$txnPaypalTable AS cp, {$server->loginDatabase}.$txnStripeTable AS st $sqlpartial";
$sth = $server->connection->getStatement($sql);

$sth->execute(array($account_id));
$failedTotal = $sth->fetch()->total;

$col = "*";
$sql = "SELECT $col FROM {$server->loginDatabase}.$txnPaypalTable AS cp, {$server->loginDatabase}.$txnStripeTable AS st $sqlpartial";
$sth = $server->connection->getStatement($sql);

$sth->execute(array($account_id));
$failedTxn = $sth->fetchAll();
?>
