<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'Trusted PayPal E-mails';

$trustedTable = Flux::config('FluxTables.DonationTrustTable');

$sql  = "SELECT DISTINCT email, create_date FROM {$server->loginDatabase}.$trustedTable ";
$sql .= "WHERE account_id = ? ORDER BY create_date DESC";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array($session->account->account_id));

$emails = $sth->fetchAll();
?>
