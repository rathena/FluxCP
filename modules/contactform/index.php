<?php
/* Contact Form Addon
 * Created and maintained by Akkarin
 * Current Version: 1.00.01
 */
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();

if(isset($_POST['body'])){
	require_once 'Flux/Mailer.php';
	$mail = new Flux_Mailer();
	$sent = $mail->send(Flux::config('ContactFormEmail'), $_POST['subject'], 'contactform', array(
		'AccountID'		=> $session->account->account_id,
		'Name'			=> $session->account->userid,
		'Email'			=> $session->account->email,
		'Subject'		=> $_POST['subject'],
		'Body'			=> $_POST['body'],
		'IP'			=> $_POST['ip']
	));
	if ($sent) {
			$session->setMessageData('Your message has been sent.');	
	}
	else {
		$errorMessage = 'Form submission failed.';
	}
}	

?>