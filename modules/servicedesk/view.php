<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$ticket_id = trim($params->get('ticketid'));
$updateID = trim($params->get('update'));
$tbl = Flux::config('FluxTables.ServiceDeskTable'); 
$tbla = Flux::config('FluxTables.ServiceDeskATable'); 
$tblc = Flux::config('FluxTables.ServiceDeskCatTable'); 

if(isset($_POST['postreply']) && $_POST['postreply'] == 'gogolol'){
	if($_POST['secact']=='2'){
		if($_POST['response']=='Leave as-is to skip text response.' || $_POST['response'] == '' || $_POST['response'] == NULL || !isset($_POST['response'])){
			$session->setMessageData(Flux::message('SDNoBlankResponse'));
			
			$this->redirect($this->url('servicedesk','view', array('ticketid' => $ticket_id)));
		} else {
			$text = addslashes($_POST['response']);
		}
		$sql = "INSERT INTO {$server->loginDatabase}.$tbla (ticket_id, author, text, action, ip, isstaff)";
		$sql .= "VALUES (?, ?, ?, 0, ?, 0)";
		$sth = $server->connection->getStatement($sql);
		$sth->execute(array($ticket_id, $session->account->userid, $text, $_SERVER['REMOTE_ADDR'])); 
		$sth = $server->connection->getStatement("UPDATE {$server->loginDatabase}.$tbl SET lastreply = 'Player' WHERE ticket_id = ?");
		$sth->execute(array($ticket_id)); 
		$this->redirect($this->url('servicedesk','index'));
	
	}elseif($_POST['secact']=='3'){
		$sth = $server->connection->getStatement("UPDATE {$server->loginDatabase}.$tbl SET status = 'Resolved' WHERE ticket_id = ?");
		$sth->execute(array($ticket_id)); 
		
		if($_POST['response']=='Leave as-is to skip text response.' || $_POST['response'] == '' || $_POST['response'] == NULL || !isset($_POST['response'])){
			$text = '0';
		} else {
			$text = addslashes($_POST['response']);
		}
		$action='Player marked ticket as Resolved';
		$sql = "INSERT INTO {$server->loginDatabase}.$tbla (ticket_id, author, text, action, ip, isstaff)";
		$sql .= "VALUES (?, ?, ?, ?, ?, 0)";
		$sth = $server->connection->getStatement($sql);
		$sth->execute(array($ticket_id, $session->account->userid, $text, $action, $_SERVER['REMOTE_ADDR'])); 
		$sth = $server->connection->getStatement("UPDATE {$server->loginDatabase}.$tbl SET lastreply = 'Player' WHERE ticket_id = ?");
		$sth->execute(array($ticket_id)); 
		$this->redirect($this->url('servicedesk','index'));
		
	}elseif($_POST['secact']=='6'){
		$sth = $server->connection->getStatement("UPDATE {$server->loginDatabase}.$tbl SET status = 'Pending' WHERE ticket_id = ?");
		$sth->execute(array($ticket_id)); 
		
		if($_POST['response']=='Leave as-is to skip text response.' || $_POST['response'] == '' || $_POST['response'] == NULL || !isset($_POST['response'])){
			$text = '0';
		} else {
			$text = addslashes($_POST['response']);
		}
		$sql = "INSERT INTO {$server->loginDatabase}.$tbla (ticket_id, author, text, action, ip, isstaff)";
		$sql .= "VALUES (?, ?, ?, ?, ?, 0)";
		$sth = $server->connection->getStatement($sql);
		$sth->execute(array($ticket_id, $session->account->userid, $text, Flux::message('SDReOpenPlayer'), $_SERVER['REMOTE_ADDR'])); 
		$sth = $server->connection->getStatement("UPDATE {$server->loginDatabase}.$tbl SET lastreply = 'Player' WHERE ticket_id = ?");
		$sth->execute(array($ticket_id)); 
		$this->redirect($this->url('servicedesk','index'));
	}
}
$rep = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tbl WHERE ticket_id = ? and account_id = ?");
$rep->execute(array($ticket_id, $session->account->account_id));
$ticketlist = $rep->fetchAll();
if($ticketlist) {
    foreach($ticketlist as $trow) {
		$chid=$trow->char_id;
		$sql = "SELECT * FROM {$server->charMapDatabase}.char WHERE char_id = ? and account_id = ?";
		$ch = $server->connection->getStatement($sql);
		$ch->execute(array($chid, $session->account->account_id));
		$chr = $ch->fetchAll();
		foreach($chr as $char) {
		}
	}
} else {
    $this->redirect($this->url('servicedesk','index'));
}

$repr = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tbla WHERE ticket_id = ?");
$repr->execute(array($ticket_id));
$replylist = $repr->fetchAll();

$tblc = Flux::config('FluxTables.ServiceDeskCatTable'); 
$sth  = $server->connection->getStatement("SELECT name FROM {$server->loginDatabase}.$tblc WHERE cat_id = ?");
$sth->execute(array($trow->category));
$ticketlist = $sth->fetchAll();
if($ticketlist) {
	foreach($ticketlist as $crow) {
		$catname=$crow->name;
	}
}
?>
