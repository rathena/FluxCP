<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('BranchLogTitle');

$sql = "SELECT COUNT(branch_id) AS total FROM {$server->logsDatabase}.branchlog";
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('branch_date' => 'account_id', 'char_id', 'char_name', 'map'));

$col = 'branch_id, branch_date, account_id, char_id, char_name, map';
$sql = $paginator->getSQL("SELECT $col FROM {$server->logsDatabase}.branchlog");
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$branchs = $sth->fetchAll();
?>
