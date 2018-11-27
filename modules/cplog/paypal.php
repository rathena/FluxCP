<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'List PayPal Transactions';

$txnLogTable = Flux::config('FluxTables.TransactionTable');
$sqlpartial  = "{$server->loginDatabase}.{$txnLogTable} AS p ";
$sqlpartial .= "LEFT OUTER JOIN {$server->loginDatabase}.login AS l ON p.account_id = l.account_id ";
$sqlpartial .= "LEFT OUTER JOIN {$server->loginDatabase}.$txnLogTable AS pp ON pp.txn_id = p.parent_txn_id ";
$sqlpartial .= "WHERE (p.server_name = ? OR p.server_name IS NULL OR p.server_name = '') ";

$bind            = array($session->loginAthenaGroup->serverName);
$opMapping       = array('eq' => '=', 'gt' => '>', 'lt' => '<');
$opValues        = array_keys($opMapping);
$txnID           = trim($params->get('txn_id'));
$parentTxnID     = trim($params->get('parent_txn_id'));
$paymentStatus   = trim($params->get('status'));
$emailAddress    = trim($params->get('email'));
$amount          = $params->get('amount');
$amountOp        = $params->get('amount_op');
$credits         = $params->get('credits');
$creditsOp       = $params->get('credits_op');
$account         = $params->get('account');
$processedAfter  = $params->get('processed_after_date');
$processedBefore = $params->get('processed_before_date');
$receivedAfter   = $params->get('received_after_date');
$receivedBefore  = $params->get('received_before_date');

if ($txnID) {
	$sqlpartial .= 'AND p.txn_id = ? ';
	$bind[]      = $txnID;
}

if ($parentTxnID) {
	$sqlpartial .= 'AND p.parent_txn_id = ? ';
	$bind[]      = $parentTxnID;
}

if ($paymentStatus) {
	$sqlpartial .= 'AND p.payment_status LIKE ?';
	$bind[]      = "%$paymentStatus%";
}

if ($emailAddress) {
	$sqlpartial .= 'AND p.payer_email LIKE ?';
	$bind[]      = "%$emailAddress%";
}

if ($account) {
	$sqlpartial .= 'AND l.userid LIKE ?';
	$bind[]      = "%$account%";
}

if (in_array($amountOp, $opValues) && trim($amount) != '') {
	$op          = $opMapping[$amountOp];
	$sqlpartial .= "AND CAST(p.mc_gross AS SIGNED) $op ? ";
	$bind[]      = $amount;
}

if (in_array($creditsOp, $opValues) && trim($credits) != '') {
	$op          = $opMapping[$creditsOp];
	$sqlpartial .= "AND CAST(p.credits AS UNSIGNED) $op ? ";
	$bind[]      = $credits;
}

if ($processedAfter) {
	$sqlpartial .= 'AND p.process_date >= ? ';
	$bind[]      = $processedAfter;
}

if ($processedBefore) {
	$sqlpartial .= 'AND p.process_date <= ? ';
	$bind[]      = $processedBefore;
}

if ($receivedAfter) {
	$sqlpartial .= 'AND p.payment_date >= ? ';
	$bind[]      = $receivedAfter;
}

if ($receivedBefore) {
	$sqlpartial .= 'AND p.payment_date <= ? ';
	$bind[]      = $receivedBefore;
}

$sth = $server->connection->getStatement("SELECT COUNT(p.id) AS total FROM $sqlpartial");
$sth->execute($bind);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(
	array(
		'p.txn_id',
		'p.parent_txn_id',
		'p.process_date' => 'DESC',
		'p.payment_date',
		'p.payment_status',
		'p.payer_email',
		'p.mc_gross',
		'p.credits',
		'p.server_name',
		'l.userid'
	)
);

$col  = "p.id, p.txn_id, p.parent_txn_id, p.process_date, p.payment_date, p.payment_status, p.mc_currency, ";
$col .=  "p.payer_email, p.mc_gross, p.credits, p.server_name, pp.id AS parent_id, p.account_id, l.userid";

$sql  = "SELECT $col FROM $sqlpartial";
$sql  = $paginator->getSQL($sql);
$sth  = $server->connection->getStatement($sql);

$sth->execute($bind);
$transactions = $sth->fetchAll();
?>
