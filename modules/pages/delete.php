<?php
/* CMS Addon
 * Created and maintained by Akkarin
 * Current Version: 1.0.1
 */
 
if (!defined('FLUX_ROOT')) exit;  
$pages 	  = Flux::config('FluxTables.CMSPagesTable');
$id 	  = $params->get('id');
$sql 	  = "SELECT title FROM {$server->loginDatabase}.$pages WHERE id = ?";
$sth 	  = $server->connection->getStatement($sql);
$sth->execute(array($id));
$page	  = $sth->fetch();
$redirect = $auth->actionAllowed('pages', 'index') ? $this->url('pages', 'index') : null;

if ($page) {
    $sth = $server->connection->getStatement("DELETE FROM {$server->loginDatabase}.$pages WHERE id = ?");
    $sth->execute(array($id));
	$session->setMessageData(sprintf(Flux::message('XCMSPageDeleted')));
}
else {
	$session->setMessageData(Flux::message('XCMSPageNotFound'));
}
$this->redirect($redirect);
?>