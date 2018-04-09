<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('PasswordChangeTitle');

if (count($_POST)) {
	$currentPassword    = $params->get('currentpass');
	$newPassword        = $params->get('newpass');
	$confirmNewPassword = $params->get('confirmnewpass');
	$useGMPassSecurity  = $session->account->group_level < Flux::config('EnableGMPassSecurity');
	$passwordMinLength  = $useGMPassSecurity ? Flux::config('GMMinPasswordLength') : Flux::config('MinPasswordLength');
	$passwordMinUpper   = $useGMPassSecurity ? Flux::config('GMPasswordMinUpper') : Flux::config('PasswordMinUpper');
	$passwordMinLower   = $useGMPassSecurity ? Flux::config('GMPasswordMinLower') : Flux::config('PasswordMinLower');
	$passwordMinNumber  = $useGMPassSecurity ? Flux::config('GMPasswordMinNumber') : Flux::config('PasswordMinNumber');
	$passwordMinSymbol  = $useGMPassSecurity ? Flux::config('GMPasswordMinSymbol') : Flux::config('PasswordMinSymbol');
	
	if (!$currentPassword) {
		$errorMessage = Flux::message('NeedCurrentPassword');
	}
	elseif (!$newPassword) {
		$errorMessage = Flux::message('NeedNewPassword');
	}
	elseif (!Flux::config('AllowUserInPassword') && stripos($newPassword, $session->account->userid) !== false) {
		$errorMessage = Flux::message('NewPasswordHasUsername');
	}
	elseif (!ctype_graph($newPassword)) {
		$errorMessage = Flux::message('NewPasswordInvalid');
	}
	elseif (strlen($newPassword) < $passwordMinLength) {
		$errorMessage = sprintf(Flux::message('PasswordTooShort'), $passwordMinLength, Flux::config('MaxPasswordLength'));
	}
	elseif (strlen($newPassword) > Flux::config('MaxPasswordLength')) {
		$errorMessage = sprintf(Flux::message('PasswordTooLong'), $passwordMinLength, Flux::config('MaxPasswordLength'));
	}
	elseif (!$confirmNewPassword) {
		$errorMessage = Flux::message('ConfirmNewPassword');
	}
	elseif ($newPassword != $confirmNewPassword) {
		$errorMessage = Flux::message('PasswordsDoNotMatch');
	}
	elseif ($newPassword == $currentPassword) {
		$errorMessage = Flux::message('NewPasswordSameAsOld');
	}
	elseif (Flux::config('PasswordMinUpper') > 0 && preg_match_all('/[A-Z]/', $newPassword, $matches) < $passwordMinUpper) {
		$errorMessage = sprintf(Flux::message('NewPasswordNeedUpper'), $passwordMinUpper);
	}
	elseif (Flux::config('PasswordMinLower') > 0 && preg_match_all('/[a-z]/', $newPassword, $matches) < $passwordMinLower) {
		$errorMessage = sprintf(Flux::message('NewPasswordNeedLower'), $passwordMinLower);
	}
	elseif (Flux::config('PasswordMinNumber') > 0 && preg_match_all('/[0-9]/', $newPassword, $matches) < $passwordMinNumber) {
		$errorMessage = sprintf(Flux::message('NewPasswordNeedNumber'), $passwordMinNumber);
	}
	elseif (Flux::config('PasswordMinSymbol') > 0 && preg_match_all('/[^A-Za-z0-9]/', $newPassword, $matches) < $passwordMinSymbol) {
		$errorMessage = sprintf(Flux::message('NewPasswordNeedSymbol'), $passwordMinSymbol);
	}
	else {
		$sql = "SELECT user_pass AS currentPassword FROM {$server->loginDatabase}.login WHERE account_id = ?";
		$sth = $server->connection->getStatement($sql);
		$sth->execute(array($session->account->account_id));
		
		$account         = $sth->fetch();
		$useMD5          = $session->loginServer->config->getUseMD5();
		$currentPassword = $useMD5 ? Flux::hashPassword($currentPassword) : $currentPassword;
		$newPassword     = $useMD5 ? Flux::hashPassword($newPassword) : $newPassword;
		
		if ($currentPassword != $account->currentPassword) {
			$errorMessage = Flux::message('OldPasswordInvalid');
		}
		else {
			$sql = "UPDATE {$server->loginDatabase}.login SET user_pass = ? WHERE account_id = ?";
			$sth = $server->connection->getStatement($sql);
			
			if ($sth->execute(array($newPassword, $session->account->account_id))) {
				$pwChangeTable = Flux::config('FluxTables.ChangePasswordTable');
				
				$sql  = "INSERT INTO {$server->loginDatabase}.$pwChangeTable ";
				$sql .= "(account_id, old_password, new_password, change_ip, change_date) ";
				$sql .= "VALUES (?, ?, ?, ?, NOW())";
				$sth  = $server->connection->getStatement($sql);
				$sth->execute(array($session->account->account_id, $currentPassword, $newPassword, $_SERVER['REMOTE_ADDR']));
				
				$session->setMessageData(Flux::message('PasswordHasBeenChanged'));
				$session->logout();
				$this->redirect($this->url('account', 'login'));
			}
			else {
				$errorMessage = Flux::message('FailedToChangePassword');
			}
		}
	}
}
?>
