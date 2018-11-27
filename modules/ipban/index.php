<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('IpbanListTitle');

$sqlpartial = "WHERE rtime > NOW() ";

$sql = "SELECT COUNT(list) AS total FROM {$server->loginDatabase}.ipbanlist $sqlpartial";
$sth = $server->connection->getStatement($sql);
$sth->execute();

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('list', 'btime' => 'desc', 'rtime', 'reason'));

$sql = $paginator->getSQL("SELECT list, btime, rtime, reason FROM {$server->loginDatabase}.ipbanlist $sqlpartial");
$sth = $server->connection->getStatement($sql);
$sth->execute();

$banlist = $sth->fetchAll();
?>
