<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('NPCLogTitle');

$sql = "SELECT COUNT(npc_id) AS total FROM {$server->logsDatabase}.npclog";
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('npc_id' => 'npc_date', 'account_id', 'char_id', 'char_name', 'map', 'mes'));

$col = 'npc_id, npc_date, account_id, char_id, char_name, map, mes';
$sql = $paginator->getSQL("SELECT $col FROM {$server->logsDatabase}.npclog");
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$npcs = $sth->fetchAll();
?>
