<?php 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();


$tbl = Flux::config('FluxTables.WebCommandsTable'); 

$pageTitle = Flux::message('WCTitleLabel');


if(isset($_POST['command'])){
	$sql = "INSERT INTO {$server->charMapDatabase}.$tbl (command, issuer, account_id)";
	$sql .= "VALUES (?, ?, ?)";
	$sth = $server->connection->getStatement($sql);
	$sth->execute(array($_POST['command'], $session->account->userid, $session->account->account_id));
	if(Flux::config('DiscordUseWebhook')) {
		if(Flux::config('DiscordSendOnWebCommand')) {
			sendtodiscord(Flux::config('DiscordWebhookURL'), 'Web Command Submitted: '. $_POST['command']);
		}
	}

}


// Last ran commands

$sql1 = "SELECT * FROM {$server->charMapDatabase}.$tbl WHERE `done` = '1' ORDER BY `timestamp` DESC LIMIT 5";
$sth1 = $server->connection->getStatement($sql1);
$sth1->execute();
$output = '';
$comms = $sth1->fetchAll();
	if($comms){
		foreach($comms as $command){
			$output.='<tr><td>'.$command->command.'</td><td>'.$command->issuer.'</td><td>'.$command->timestamp.'</td></tr>';
		}
	}



?>
