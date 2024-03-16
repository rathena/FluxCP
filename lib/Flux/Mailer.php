<?php
require 'phpmailer/PHPMailerAutoload.php';
require_once 'Flux/LogFile.php';

class Flux_Mailer {
	protected $pm;
	protected static $errLog;
	protected static $log;
	
	public function __construct()
	{
		if (!self::$errLog) {
			self::$errLog = new Flux_LogFile(FLUX_DATA_DIR.'/logs/errors/mail/'.date('Ymd').'.log');
		}
		if (!self::$log) {
			self::$log = new Flux_LogFile(FLUX_DATA_DIR.'/logs/mail/'.date('Ymd').'.log');
		}
		
		$this->pm     = $pm = new PHPMailer();
		$this->errLog = self::$errLog;
		$this->log    = self::$log;
		
		if (Flux::config('MailerUseSMTP')) {
			$pm->IsSMTP();
			
			if (is_array($hosts=Flux::config('MailerSMTPHosts'))) {
				$hosts = implode(';', $hosts);
			}
			
			$pm->Host = $hosts;
			
			if ($user=Flux::config('MailerSMTPUsername')) {
				$pm->SMTPAuth = true;
				
				if (Flux::config('MailerSMTPUseTLS')) {
					$pm->SMTPSecure = 'tls';
				}
				if (Flux::config('MailerSMTPUseSSL')) {
					$pm->SMTPSecure = 'ssl';
				}
				if ($port=Flux::config('MailerSMTPPort')) {
					$pm->Port = (int)$port;
				}
				
				$pm->Username = $user;
				
				if ($pass=Flux::config('MailerSMTPPassword')) {
					$pm->Password = $pass;
				}
			}
		}
		
		// From address.
        $pm->setFrom(Flux::config('MailerFromAddress'), Flux::config('MailerFromName'));
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
		$this->pm->isHTML(true);
		$this->pm->CharSet = 'UTF-8';
		$this->pm->AddAddress($recipient);
		$this->pm->Subject = $subject;
		$this->pm->msgHTML($content);
		
		if ($sent=$this->pm->Send()) {
			self::$log->puts("sent e-mail -- Recipient: $recipient, Subject: $subject");
		}
		else {
			self::$errLog->puts("{$this->pm->ErrorInfo} (while attempting -- Recipient: $recipient, Subject: $subject)");
		}
		return $sent;
	}
}
?>
