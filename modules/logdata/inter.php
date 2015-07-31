<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('InterLogTitle');

$sql = "SELECT COUNT(time) AS total FROM {$server->logsDatabase}.interlog";
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('time' => 'log'));

$col = 'time, log';
$sql = $paginator->getSQL("SELECT $col FROM {$server->logsDatabase}.interlog");
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$inters = $sth->fetchAll();
?>