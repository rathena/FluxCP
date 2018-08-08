<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('HistoryEmailTitle');
$emailChangeTable = Flux::config('FluxTables.ChangeEmailTable');

$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$emailChangeTable WHERE account_id = ?";
$sth = $server->connection->getStatement($sql);
$sth->execute(array($session->account->account_id));

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('old_email', 'new_email', 'request_date', 'request_ip', 'change_date', 'change_ip', 'change_done'));

$sql = "SELECT old_email, new_email, request_date, request_ip, change_date, change_ip, change_done FROM {$server->loginDatabase}.$emailChangeTable WHERE account_id = ?";
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatement($sql);
$sth->execute(array($session->account->account_id));

$changes = $sth->fetchAll();
?>
