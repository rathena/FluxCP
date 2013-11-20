<?php
/* Tasklist Addon
 * Created and maintained by Akkarin
 * Current Version: 1.00.04
 */

if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$option = trim($params->get('option'));
$staffid = trim($params->get('staffid'));
$tbls = Flux::config('FluxTables.TaskListStaffTable');

if(isset($option) && $option == 'delete'){
	$sth = $server->connection->getStatement("DELETE FROM {$server->loginDatabase}.$tbls WHERE account_id = ?");
	$sth->execute(array($staffid)); 
	$this->redirect($this->url('tasks', 'staffsettings'));
}


if(isset($_POST['account_id'])){
	$ssql = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tbls WHERE account_id = ?");
	$ssql->execute(array($_POST['account_id']));
	$staff = $ssql->fetchAll();
	if($staff){
		$session->setMessageData('Account already exists!');
		$this->redirect($this->url('tasks','staffsettings'));
	} else {
		if(!isset($_POST['emailalerts']) || $_POST['emailalerts']==NULL){
			$_POST['emailalerts'] = '0';
		}
		$sqla = "INSERT INTO {$server->loginDatabase}.$tbls (account_id, account_name, preferred_name, emailalerts) ";
		$sqla.= "VALUES (?, ?, ?, ?)";
		$stha = $server->connection->getStatement($sqla);
		$stha->execute(array(
			$session->account->account_id, 
			$session->account->userid, 
			$_POST['preferred_name'], 
			$_POST['emailalerts'])); 
		$this->redirect($this->url('tasks', 'staffsettings'));
	}
}

$rep = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tbls ORDER BY account_id");
$rep->execute();
$stafflist = $rep->fetchAll();


?>