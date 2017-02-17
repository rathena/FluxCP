<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('CharLogTitle');

$sql = "SELECT COUNT(time) AS total FROM {$server->logsDatabase}.charlog";
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('time' => 'char_msg', 'account_id', 'char_num', 'name'));

$col = 'time, char_msg, account_id, char_num, name';
$sql = $paginator->getSQL("SELECT $col FROM {$server->logsDatabase}.charlog ORDER BY `time` DESC");
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$chars1 = $sth->fetchAll();
?>