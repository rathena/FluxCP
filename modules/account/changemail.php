<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('EmailChangeTitle');

$emailChangeTable = Flux::config('FluxTables.ChangeEmailTable');

if (count($_POST)) {
	$email = trim($params->get('email'));
	
	if (!$email) {
		$errorMessage = Flux::message('EnterEmailAddress');
	}
	elseif ($email == $session->account->email) {
		$errorMessage = Flux::message('EmailCannotBeSame');
	}
	elseif (!preg_match('/^(.+?)@(.+?)$/', $email)) {
		$errorMessage = Flux::message('EmailInvalid');
	}
	elseif (!Flux::config('AllowDuplicateEmails')) {
		$sql = "SELECT email FROM {$server->loginDatabase}.login WHERE email = ? LIMIT 1";
		$sth = $server->connection->getStatement($sql);
		$sth->execute(array($email));
		
		$row = $sth->fetch();
		if ($row && $row->email) {
			$errorMessage = Flux::message('EmailAlreadyRegistered');
		}
	}
	
	if (empty($errorMessage)) {
		$code = md5(rand() + $session->account->account_id);
		$ip   = $_SERVER['REMOTE_ADDR'];
		$fail = false;
		
		if (Flux::config('RequireChangeConfirm')) {
			$sql  = "INSERT INTO {$server->loginDatabase}.$emailChangeTable ";
			$sql .= "(code, account_id, old_email, new_email, request_date, request_ip, change_done) ";
			$sql .= "VALUES (?, ?, ?, ?, NOW(), ?, 0)";
			$sth  = $server->connection->getStatement($sql);
			$res  = $sth->execute(array($code, $session->account->account_id, $session->account->email, $email, $ip));
			
			if ($res) {
				require_once 'Flux/Mailer.php';
				$name = $session->loginAthenaGroup->serverName;
				$link = $this->url('account', 'confirmemail', array('_host' => true, 'code' => $code, 'account' => $session->account->account_id, 'login' => $name));
				$mail = new Flux_Mailer();
				$sent = $mail->send($email, 'Change E-mail', 'changemail', array(
					'AccountUsername' => $session->account->userid,
					'OldEmail'        => $session->account->email,
					'NewEmail'        => $email,
					'ChangeLink'      => htmlspecialchars($link)
				));

				if ($sent) {
					$session->setMessageData(Flux::message('EmailChangeSent'));
					$this->redirect();
				}
				else {
					$fail = true;
				}
			}
			else {
				$fail = true;
			}
			
		}
		else {
			$old  = $session->account->email;
			$sql  = "UPDATE {$server->loginDatabase}.login SET email = ? WHERE account_id = ?";
			$sth  = $server->connection->getStatement($sql);
			
			if ($sth->execute(array($email, $session->account->account_id))) {
				$sql  = "INSERT INTO {$server->loginDatabase}.$emailChangeTable ";
				$sql .= "(code, account_id, old_email, new_email, request_date, request_ip, change_date, change_ip, change_done) ";
				$sql .= "VALUES (?, ?, ?, ?, NOW(), ?, NOW(), ?, 1)";
				$sth  = $server->connection->getStatement($sql);
				$res  = $sth->execute(array($code, $session->account->account_id, $old, $email, $ip, $ip));
				
				if ($res) {
					$session->setMessageData(Flux::message('EmailAddressChanged'));
					$this->redirect();
				}
				else {
					$fail = true;
				}
			}
			else {
				$fail = true;
			}
		}
	}
	
	if (!empty($fail)) {
		$errorMessage = Flux::message('EmailChangeFailed');
	}
}
?>
