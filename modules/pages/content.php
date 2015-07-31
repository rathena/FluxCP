<?php
if (!defined('FLUX_ROOT')) exit;
$pages = Flux::config('FluxTables.CMSPagesTable');
$path = trim($params->get('path'));

$sql = "SELECT title, body, modified FROM {$server->loginDatabase}.$pages WHERE path = ?";
$sth = $server->connection->getStatement($sql);
$sth->execute(array($path));

$pages = $sth->fetchAll();

if($pages) {
    foreach($pages as $prow) {
        $title		= $prow->title;
        $body		= $prow->body;
		$modified	= $prow->modified;
    }   
}
else {
    $this->redirect($this->url('main','index'));
}
?>
