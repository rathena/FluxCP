<?php
if (!defined('FLUX_ROOT')) exit;

$news = Flux::config('FluxTables.CMSNewsTable'); 

$sql = "SELECT title, body, link, author, created, modified FROM {$server->loginDatabase}.$news ORDER BY id DESC LIMIT ?";

$sth = $server->connection->getStatement($sql);
$sth->execute(array((int)Flux::config('CMSNewsLimit')));

$news = $sth->fetchAll();
?>
