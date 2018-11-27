<?php
if (!defined('FLUX_ROOT')) exit;
$title	= Flux::message('CMSPageHeader');
$pages	= Flux::config('FluxTables.CMSPagesTable');

$sql = "SELECT id, title, path, modified FROM {$server->loginDatabase}.$pages ORDER BY id";
$sth = $server->connection->getStatement($sql);
$sth->execute();

$pages = $sth->fetchAll();
?>
