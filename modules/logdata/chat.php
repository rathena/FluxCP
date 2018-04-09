<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'List Chat Messages';

$sql = "SELECT COUNT(id) AS total FROM {$server->logsDatabase}.chatlog";
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'time' => 'desc', 'type', 'type_id', 'src_charid', 'src_accountid',
	'src_map', 'src_map_x', 'src_map_y', 'dst_charname', 'message'
));

$col  = 'time, type, type_id, src_charid, src_accountid, src_map, src_map_x, src_map_y, dst_charname, ';
$col .= "REPLACE(message, '|00', '') AS message";
$sql  = $paginator->getSQL("SELECT $col FROM {$server->logsDatabase}.chatlog");
$sth  = $server->connection->getStatementForLogs($sql);
$sth->execute();

$messages = $sth->fetchAll();
?>
