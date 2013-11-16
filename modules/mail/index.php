<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('MailerTitle');
$preview = '';

if (count($_POST)) {
	$prev    = (bool)$params->get('_preview');
	$to      = trim($params->get('to'));
	$subject = trim($params->get('subject'));
	$body    = trim($params->get('body'));
	
	if (!$to) {
		$errorMessage = Flux::message('MailerEnterToAddress');
	}
	elseif (!$subject) {
		$errorMessage = Flux::message('MailerEnterSubject');
	}
	elseif (!$body) {
		$errorMessage = Flux::message('MailerEnterBodyText');
	}
	
	if (empty($errorMessage)) {
		if ($prev) {
			require_once 'markdown/markdown.php';
			$preview = Markdown($body);
		}
		else {
			require_once 'Flux/Mailer.php';
			
			$mail = new Flux_Mailer();
			$opts = array('_ignoreTemplate' => true, '_useMarkdown' => true);
			
			if ($mail->send($to, $subject, $body, $opts)) {
				$session->setMessageData(sprintf(Flux::message('MailerEmailHasBeenSent'), $to));
				$this->redirect();
			}
			else {
				$errorMessage = Flux::message('MailerFailedToSend');
			}
		}
	}
}
?>