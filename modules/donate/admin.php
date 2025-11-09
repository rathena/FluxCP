<?php
if (!defined('FLUX_ROOT')) exit;

$title    = 'Donate - Admin';
$transactionTable = Flux::config('FluxTables.StripeTransactionTable');

$sql  = "SELECT COUNT(*) AS count FROM {$server->loginDatabase}.$transactionTable WHERE DATE('created_at') = CURDATE()";
$sth  = $server->connection->getStatement($sql);
$sth->execute();
$donates = $sth->fetch();
?>
