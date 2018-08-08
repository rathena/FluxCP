<?php
if (!defined('FLUX_ROOT')) exit;
$title	= Flux::message('XCMSNewsPage');
$news	= Flux::config('FluxTables.CMSNewsTable'); 

$sql = "SELECT id, title, author, created, modified FROM {$server->loginDatabase}.$news ORDER BY id DESC";
$sth = $server->connection->getStatement($sql);
$sth->execute();

$news = $sth->fetchAll();
?>
