<?php
/* Tasklist Addon
 * Created and maintained by Akkarin
 * Current Version: 1.00.01
 */
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$tasklistsqltable = Flux::config('FluxTables.TaskListTable'); 
$tblsettings = Flux::config('FluxTables.TaskListStaffTable'); 

$sth = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tblsettings WHERE account_id = ?");
$sth->execute(array($session->account->account_id));
$staff = $sth->fetchAll();
if(!$staff){
	$session->setMessageData('!!!Error!!! Account not in Staff Settings table! Please submit your preferred name before using the Task List.'); $this->redirect($this->url('xtasklist','staffsettings'));
} else {
	foreach($staff as $staffsess){}
}

$sth = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tblsettings ORDER BY account_id");
$sth->execute();
$sfetch = $sth->fetchAll();
$staffselect='<option value="0">Unassigned</option>';
foreach($sfetch as $slist){
	$staffselect.='<option value="'. $slist->preferred_name .'">'. $slist->preferred_name .'</option>';
}

if(isset($_POST['title'])){
$title	= $_POST['title'];
$body	= addslashes($_POST['body']);
$link	= $_POST['link'];
if($_POST['assign'] == NULL){$assign=0;} else { $assign=$_POST['assign'];}
$link = $link .',';
$priority	= $_POST['priority'];
$sql = "INSERT INTO {$server->loginDatabase}.$tasklistsqltable (title, body, link, author, priority, assigned, created, status)";
$sql .= "VALUES (?, ?, ?, ?, ?, ?, NOW(), 0)";
$sth = $server->connection->getStatement($sql);
$sth->execute(array($title, $body, $link, $staffsess->preferred_name, $priority, $assign)); 
$this->redirect($this->url('tasks','index'));

}
?>