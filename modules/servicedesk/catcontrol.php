<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$option = trim($params->get('option'));
$catid = trim($params->get('catid'));
$tbl = Flux::config('FluxTables.ServiceDeskCatTable');

if(isset($option) && $option == 'hide'){
	$sth = $server->connection->getStatement("UPDATE {$server->loginDatabase}.$tbl SET display = 0 WHERE cat_id = ?");
	$sth->execute(array($catid)); 
	$this->redirect($this->url('servicedesk','catcontrol'));
}
if(isset($option) && $option == 'show'){
	$sth = $server->connection->getStatement("UPDATE {$server->loginDatabase}.$tbl SET display = 1 WHERE cat_id = ?");
	$sth->execute(array($catid)); 
	$this->redirect($this->url('servicedesk','catcontrol'));
}

if(isset($_POST['name'])){
	$sql = "INSERT INTO {$server->loginDatabase}.$tbl (name, display)";
	$sql .= "VALUES (?, ?)";
	$sth = $server->connection->getStatement($sql);
	$sth->execute(array($_POST['name'],$_POST['display'])); 
	$this->redirect($this->url('servicedesk','catcontrol'));
}

$rep = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tbl ORDER BY cat_id");
$rep->execute();
$catlist = $rep->fetchAll();


?>
