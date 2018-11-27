<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('CommandLogTitle');

$sql = "SELECT COUNT(atcommand_id) AS total FROM {$server->logsDatabase}.atcommandlog";
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('atcommand_date' => 'desc', 'account_id', 'char_id', 'char_name', 'map', 'command'));

$col = 'atcommand_id, atcommand_date, account_id, char_id, char_name, map, command';
$sql = $paginator->getSQL("SELECT $col FROM {$server->logsDatabase}.atcommandlog");
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$commands = $sth->fetchAll();
?>
