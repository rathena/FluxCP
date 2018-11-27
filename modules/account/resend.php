<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('ResendTitle');

$serverNames = $this->getServerNames();
$createTable = Flux::config('FluxTables.AccountCreateTable');

if (count($_POST)) {
	$userid    = $params->get('userid');
	$email     = $params->get('email');
	$groupName = $params->get('login');
	
	if (!$userid) {
		$errorMessage = Flux::message('ResendEnterUsername');
	}
	elseif (!$email) {
		$errorMessage = Flux::message('ResendEnterEmail');
	}
	elseif (preg_match('/[^' . Flux::config('UsernameAllowedChars') . ']/', $userid)) {
		$errorMessage = sprintf(Flux::message('AccountInvalidChars'), Flux::config('UsernameAllowedChars'));
	}
	elseif (!preg_match('/^(.+?)@(.+?)$/', $email)) {
		$errorMessage = Flux::message('InvalidEmailAddress');
	}
	else {
		if (!$groupName || !($loginAthenaGroup=Flux::getServerGroupByName($groupName))) {
			$loginAthenaGroup = $session->loginAthenaGroup;
		}

		$sql  = "SELECT confirm_code FROM {$loginAthenaGroup->loginDatabase}.$createTable WHERE ";
		$sql .= "userid = ? AND email = ? AND confirmed = 0 AND confirm_expire > NOW() LIMIT 1";
		$sth  = $loginAthenaGroup->connection->getStatement($sql);
		$sth->execute(array($userid, $email));

		$row  = $sth->fetch();
		if ($row) {
			require_once 'Flux/Mailer.php';
			$code = $row->confirm_code;
			$name = $loginAthenaGroup->serverName;
			$link = $this->url('account', 'confirm', array('_host' => true, 'code' => $code, 'user' => $userid, 'login' => $name));
			$mail = new Flux_Mailer();
			$sent = $mail->send($email, 'Account Confirmation', 'confirm', array('AccountUsername' => $userid, 'ConfirmationLink' => htmlspecialchars($link)));
		}

		if (empty($sent)) {
			$errorMessage = Flux::message('ResendFailed');
		}
		else {
			$session->setMessageData(Flux::message('ResendEmailSent'));
			$this->redirect();
		}
	}
}
?>
