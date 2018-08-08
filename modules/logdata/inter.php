<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('InterLogTitle');

$sql = "SELECT COUNT(time) AS total FROM {$server->charMapDatabase}.interlog";
$sth = $server->connection->getStatement($sql);
$sth->execute();

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('time' => 'DESC','log'));

$col = 'time, log';
$sql = $paginator->getSQL("SELECT $col FROM {$server->charMapDatabase}.interlog");
$sth = $server->connection->getStatement($sql);
$sth->execute();

$inters = $sth->fetchAll();
?>
