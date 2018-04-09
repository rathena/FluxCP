<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('MVPLogTitle');

$sql = "SELECT COUNT(mvp_id) AS total FROM {$server->logsDatabase}.mvplog";
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('mvp_date' => 'kill_char_id', 'monster_id', 'prize', 'mvpexp', 'map'));

$col = 'mvp_id, mvp_date, kill_char_id, monster_id, prize, mvpexp, map';
$sql = $paginator->getSQL("SELECT $col FROM {$server->logsDatabase}.mvplog");
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$mvps = $sth->fetchAll();
?>
