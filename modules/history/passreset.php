<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('HistoryPassResetTitle');
$passResetTable = Flux::config('FluxTables.ResetPasswordTable');

$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$passResetTable WHERE account_id = ?";
$sth = $server->connection->getStatement($sql);
$sth->execute(array($session->account->account_id));

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('request_date', 'request_ip', 'reset_date', 'reset_ip', 'reset_done'));

$sql = "SELECT request_date, request_ip, reset_date, reset_ip, reset_done FROM {$server->loginDatabase}.$passResetTable WHERE account_id = ?";
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatement($sql);
$sth->execute(array($session->account->account_id));

$resets = $sth->fetchAll();
?>
