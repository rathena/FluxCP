<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('MailerTitle');
$whoto = trim($params->get('whoto'));
$template = trim($params->get('template'));
$subject = trim($params->get('subject'));
$selectedtemplate = $template.'.php';


// Select Template
$template_dir = FLUX_DATA_DIR."/templates/";
$myDirectory = opendir($template_dir);
while($entryName = readdir($myDirectory)) {$dirArray[] = $entryName;}
closedir($myDirectory);
$indexCount	= count($dirArray);
sort($dirArray);

if (count($_POST)) {
	//<input type="radio" name="whoto" id="whoto" value="1" checked="checked"> No one<br />
	//<input type="radio" name="whoto" id="whoto" value="2"> Admins Only<br />
	//<input type="radio" name="whoto" id="whoto" value="3"> Staff Only<br />
	//<input type="radio" name="whoto" id="whoto" value="4"> Everyone<br />
	//<input type="radio" name="whoto" id="whoto" value="5"> VIPs<br />

	if($whoto == '1'){
		// please leave blank
	}elseif($whoto == '2'){
		$sth = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.login WHERE `group_id` = '99'");
	}elseif($whoto == '3'){
		$sth = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.login WHERE (group_id=2 OR group_id=99)");
	}elseif($whoto == '4'){
		$sth = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.login");
	}elseif($whoto == '5'){
	}

	$sth->execute();
	$list = $sth->fetchAll();

	foreach($list as $lrow){
		$email = $lrow->email;
		require_once 'Flux/Mailer.php';
		$mail = new Flux_Mailer();
		$sent = $mail->send($email, $subject, $template, array(
			'emailtitle'		=> $subject,
			'username'		=> $lrow->userid,
			'email'		=> $lrow->email,
		));
	}
	
	$session->setMessageData(Flux::message('MailerEmailHasBeenSent'));
	
	if(Flux::config('DiscordUseWebhook')) {
		if(Flux::config('DiscordSendOnMarketing')) {
			sendtodiscord(Flux::config('DiscordWebhookURL'), 'Mass Email Sent: '. $subject);
		}
	}

}
?>
