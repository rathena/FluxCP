<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$option = trim($params->get('option'));
$cur = trim($params->get('cur'));
$staffid = trim($params->get('staffid'));
$tbl = Flux::config('FluxTables.ServiceDeskSettingsTable');
$sth = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tbl WHERE account_id = ?");
$sth->execute(array($session->account->account_id));
$staff = $sth->fetchAll();
if($staff){
	foreach($staff as $staffsess){}
}

if(isset($option) && $option == 'delete'){
	$sth = $server->connection->getStatement("DELETE FROM {$server->loginDatabase}.$tbl WHERE account_id = $staffid");
	$sth->execute(); 
	$this->redirect($this->url('servicedesk','staffsettings'));
}

if(isset($option) && $option == 'alerttoggle'){
	if($cur=='1'){
		$sth = $server->connection->getStatement("UPDATE {$server->loginDatabase}.$tbl SET emailalerts = 0 WHERE account_id = $staffid");
	} else {
		$sth = $server->connection->getStatement("UPDATE {$server->loginDatabase}.$tbl SET emailalerts = 1 WHERE account_id = $staffid");
	}

	$sth->execute(); 
	$this->redirect($this->url('servicedesk','staffsettings'));
}

if(isset($_POST['account_id'])){
	$sth = $server->connection->getStatement("SELECT account_id FROM {$server->loginDatabase}.$tbl WHERE account_id = ?");
	$sth->execute(array($_POST['account_id']));
	$fetch = $sth->fetch();
	if($fetch){	$session->setMessageData('Account already exists!'); } else {
	if(!$_POST['emailalerts']){$_POST['emailalerts'] = 0;}
	$sql = "INSERT INTO {$server->loginDatabase}.$tbl (account_id, account_name, prefered_name, team, emailalerts)";
	$sql .= "VALUES (?, ?, ?, ?, ?)";
	$sth = $server->connection->getStatement($sql);
	$sth->execute(array($_POST['account_id'],$_POST['account_name'],$_POST['prefered_name'],$_POST['team'], $_POST['emailalerts'])); 
	$this->redirect($this->url('servicedesk','staffsettings'));
}
}

$rep = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tbl ORDER BY account_id");
$rep->execute();
$stafflist = $rep->fetchAll();
?>
