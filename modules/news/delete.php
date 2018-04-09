<?php
if (!defined('FLUX_ROOT')) exit;  
$news		= Flux::config('FluxTables.CMSNewsTable');
$id			= $params->get('id');
$sql		= "SELECT title FROM {$server->loginDatabase}.$news WHERE id = ?";
$sth		= $server->connection->getStatement($sql);
$sth->execute(array($id));
$new		= $sth->fetch();
$redirect	= $auth->actionAllowed('news', 'index') ? $this->url('news', 'index') : null;

if ($new) {
    $sth = $server->connection->getStatement("DELETE FROM {$server->loginDatabase}.$news WHERE id = ?");
    $sth->execute(array($id));
	$session->setMessageData(sprintf(Flux::message('CMSNewsDeleted'), $new->title));
}
else {
	$session->setMessageData(Flux::message('CMSNewsNotFound'));
}
$this->redirect($redirect);
?>
