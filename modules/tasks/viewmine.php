<?php
/* Tasklist Addon
 * Created and maintained by Akkarin
 * Current Version: 1.00.04
 */
 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$tblsettings = Flux::config('FluxTables.TaskListStaffTable'); 
$sth = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tblsettings WHERE account_id = ?");
$sth->execute(array($session->account->account_id));
$staff = $sth->fetchAll();
if(!$staff){
	$session->setMessageData('!!!Error!!! Account not in Staff Settings table! Please submit your preferred name before using the Task List.'); $this->redirect($this->url('xtasklist','staffsettings'));
} else {
	foreach($staff as $staffsess){}
}
$tbl = Flux::config('FluxTables.TaskListTable'); 
$sql = "SELECT * FROM {$server->loginDatabase}.$tbl WHERE status != 5 AND assigned = ? ORDER BY id";
$task = $server->connection->getStatement($sql);
$task->execute(array($staffsess->preferred_name));
$tasklist = $task->fetchAll();
$tblsettings = Flux::config('FluxTables.TaskListStaffTable'); 


?>