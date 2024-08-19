<?php

require 'sendgrid/sendgrid-php.php';

require_once 'Flux/LogFile.php';

class Flux_Mailer_SendGrid {
	protected $pm;
	public static $errLog;
	protected static $log;

	public function __construct()
	{
		if (!self::$errLog) {
			self::$errLog = new Flux_LogFile(FLUX_DATA_DIR.'/logs/errors/mail/'.date('Ymd').'.log');
		}
		if (!self::$log) {
			self::$log = new Flux_LogFile(FLUX_DATA_DIR.'/logs/mail/'.date('Ymd').'.log');
		}

		$this->pm     = $pm = new \SendGrid\Mail\Mail();
		$this->errLog = self::$errLog;
		$this->log    = self::$log;

	}

	public function send($recipient, $subject, $template, array $templateVars = array())
	{
		if (array_key_exists('_ignoreTemplate', $templateVars) && $templateVars['_ignoreTemplate']) {
			$content = $template;
		} else {
			$templatePath = FLUX_DATA_DIR."/templates/$template.php";
			if (!file_exists($templatePath)) {
				return false;
			}

			$find = array();
			$repl = array();

			foreach ($templateVars as $key => $value) {
				$find[] = '{'.$key.'}';
				$repl[] = $value;
			}

			ob_start();
			include $templatePath;
			$content = ob_get_clean();

			if (!empty($find) && !empty($repl)) {
				$content = str_replace($find, $repl, $content);
			}
		}
		$this->pm->setFrom(Flux::config('MailerFromAddress'), Flux::config('MailerFromName'));
		$this->pm->AddTo($recipient, $recipient);
		$this->pm->SetSubject($subject);
		$this->pm->AddContent("text/html", $content);

		$sendgrid = new \SendGrid(Flux::config('SendGridAPIKey'));

		try {
			$response = $sendgrid->send($this->pm);
			self::$log->puts("sent e-mail -- Recipient: $recipient, Subject: $subject");
			return $response->statusCode() . "\n";
			//print_r($response->headers());
			//print $response->body() . "\n";
		} catch (Exception $e) {
			self::$errLog->puts("{$this->pm->ErrorInfo} (while attempting -- Recipient: $recipient, Subject: $subject)");
			return 'Caught exception: '. $e->getMessage() ."\n";
		}
	}
}
?>
