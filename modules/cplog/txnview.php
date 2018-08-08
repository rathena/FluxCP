<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'Viewing PayPal Transaction';

$txnLogTable = Flux::config('FluxTables.TransactionTable');
$txnID       = $params->get('id');
$txnFileLog  = '';

$sql  = "SELECT * FROM {$server->loginDatabase}.{$txnLogTable} AS txnlog ";
$sql .= "LEFT JOIN {$server->loginDatabase}.login ON txnlog.account_id = login.account_id ";
$sql .= "WHERE id = ? LIMIT 1";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array($txnID));

$txn = $sth->fetch();

if ($txn) {
	$title = "Viewing PayPal Transaction ({$txn->txn_id}, status: {$txn->payment_status})";
	
	$txnLogFile = FLUX_DATA_DIR."/logs/transactions/{$txn->txn_type}/{$txn->payment_status}/{$txn->txn_id}.log.php";
	if (file_exists($txnLogFile)) {
		$txnFileLog = file($txnLogFile);
		
		if (count($txnFileLog) && preg_match('/<\?php.*?\?>/', $txnFileLog[0])) {
			array_shift($txnFileLog);
		}
		
		$txnFileLog = implode('', $txnFileLog);
	}
}
?>
