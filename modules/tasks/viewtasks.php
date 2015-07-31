<?php
/* Tasklist Addon
 * Created and maintained by Akkarin
 * Current Version: 1.00.04
 */
 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$taskID = trim($params->get('task'));
$updateID = trim($params->get('update'));
$setowner = trim($params->get('setowner'));
$disps = '';
$dispo = '';
$tbl = Flux::config('FluxTables.TaskListTable'); 
$tblsettings = Flux::config('FluxTables.TaskListStaffTable'); 

$sth = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tblsettings WHERE account_id = ?");
$sth->execute(array($session->account->account_id));
$staff = $sth->fetchAll();
if(!$staff){
	$session->setMessageData('!!!Error!!! Account not in Staff Settings table! Please submit your preferred name before using the Task List.'); $this->redirect($this->url('xtasklist','staffsettings'));
} else {
	foreach($staff as $staffsess){}
}


if(isset($setowner) && $setowner == 'takeownership'){
	$tasku3 = $server->connection->getStatement("UPDATE {$server->loginDatabase}.$tbl SET assigned = ? WHERE id = ?");
	if($tasku3->execute(array($staffsess->preferred_name, $taskID))){$session->setMessageData('Task Updated'); $this->redirect($this->url('tasks','viewtasks', array('task' => $taskID)));}
}
if(isset($setowner) && $setowner == 'release'){
	$tasku3 = $server->connection->getStatement("UPDATE {$server->loginDatabase}.$tbl SET assigned = 0 WHERE id = ?");
	if($tasku3->execute(array($taskID))){$session->setMessageData('Task Updated');$this->redirect($this->url('tasks','viewtasks', array('task' => $taskID)));}
}
if(isset($updateID)){
	$tasku = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tbl WHERE id = ?");
	$tasku->execute(array($updateID));
	$tasklistu = $tasku->fetchAll();
		foreach($tasklistu as $trowu) {
			if($trowu->status==5){
				$chu = 0;
			} elseif($trowu->status==2){
				$chu = 5;
			} elseif($trowu->status==1){
				$chu = 2;
			} elseif($trowu->status==0){
				$chu = 1;
			}
			$tasku2 = $server->connection->getStatement("UPDATE {$server->loginDatabase}.$tbl SET status = ? WHERE id = ?");
			if($tasku2->execute(array($chu, $updateID))){
				$session->setMessageData('Task Updated');
				$this->redirect($this->url('tasks','viewtasks', array('task' => $taskID)));
}
		}
}

$task = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tbl WHERE id = ?");
$task->execute(array($taskID));
$tasklist = $task->fetchAll();
if($tasklist) {
    foreach($tasklist as $trow) {
        $title		= $trow->title;
        $body		= stripslashes($trow->body);
		$modified	= $trow->modified;
		
		if($trow->assigned=='0' || $trow->assigned!=$staffsess->preferred_name){
			$assignedlink = '<a href="'. $this->url('tasks', 'viewtasks', array('task' => $taskID, 'setowner' => 'takeownership')) .'">Take Ownership</a>';
		} else {
			$assignedlink = '<a href="'. $this->url('tasks', 'viewtasks', array('task' => $taskID, 'setowner' => 'release')) .'">Release Ownership</a>';
		}

		
		if($trow->link==NULL || $trow->link=='' || $trow->link==','){
			$resources='Link 1: <span class="not-applicable">None</span>';
		} else {
			$resourceslist=explode(',',$trow->link);
			if(isset($resourceslist[0]) && $resourceslist[0]!=''){$resources='Link 1: <a href="'.$resourceslist[0].'">'.$resourceslist[0].'</a><br />';}
			if(isset($resourceslist[1]) && $resourceslist[1]!=''){$resources.='Link 2: <a href="'.$resourceslist[1].'">'.$resourceslist[1].'</a><br />';}else{$resources.='';}
			if(isset($resourceslist[2]) && $resourceslist[2]!=''){$resources.='Link 3: <a href="'.$resourceslist[2].'">'.$resourceslist[2].'</a><br />';}else{$resources.='';}
			if(isset($resourceslist[3]) && $resourceslist[3]!=''){$resources.='Link 4: <a href="'.$resourceslist[3].'">'.$resourceslist[3].'</a><br />';}else{$resources.='';}
			if(isset($resourceslist[4]) && $resourceslist[4]!=''){$resources.='Link 5: <a href="'.$resourceslist[4].'">'.$resourceslist[4].'</a><br />';}else{$resources.='';}
			if(isset($resourceslist[5]) && $resourceslist[5]!=''){$resources.='Link 6: <a href="'.$resourceslist[5].'">'.$resourceslist[5].'</a><br />';}else{$resources.='';}
		}
	}
} else {
    $this->redirect($this->url('tasks','index'));
}
?>