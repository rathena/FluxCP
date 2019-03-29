<?php

use rAthena\FluxCp\Flux;
use rAthena\FluxCp\LoginError;

if (!defined('FLUX_ROOT')) exit;

if (Flux::config('UseLoginCaptcha') && Flux::config('EnableReCaptcha')) {
	$recaptcha = Flux::config('ReCaptchaPublicKey');
	$theme = Flux::config('ReCaptchaTheme');
}

$title = Flux::message('LoginTitle');
$loginLogTable = Flux::config('FluxTables.LoginLogTable');

if (count($_POST)) {
	$serverGroupName = $params->get('server');
	$username = $params->get('username');
	$password = $params->get('password');
	$code     = $params->get('security_code');
	
	try {
		$session->login($serverGroupName, $username, $password, $code);
		$returnURL = $params->get('return_url');
		
		if ($session->loginAthenaGroup->loginServer->config->getUseMD5()) {
			$password = Flux::hashPassword($password);
		}
		
		$sql  = "INSERT INTO {$session->loginAthenaGroup->loginDatabase}.$loginLogTable ";
		$sql .= "(account_id, username, password, ip, error_code, login_date) ";
		$sql .= "VALUES (?, ?, ?, ?, ?, NOW())";
		$sth  = $session->loginAthenaGroup->connection->getStatement($sql);
		$sth->execute(array($session->account->account_id, $username, $password, $_SERVER['REMOTE_ADDR'], null));
		
		if ($returnURL) {
			$this->redirect($returnURL);
		}
		else {
			$this->redirect();
		}
	}
	catch (LoginError $e) {
		if ($username && $password && $e->getCode() != LoginError::INVALID_SERVER) {
			$loginAthenaGroup = Flux::getServerGroupByName($serverGroupName);

			$sql = "SELECT account_id FROM {$loginAthenaGroup->loginDatabase}.login WHERE ";
			
			if (!$loginAthenaGroup->loginServer->config->getNoCase()) {
				$sql .= "CAST(userid AS BINARY) ";
			} else {
				$sql .= "userid ";
			}
			
			$sql .= "= ? LIMIT 1";
			$sth = $loginAthenaGroup->connection->getStatement($sql);
			$sth->execute(array($username));
			$row = $sth->fetch();

			if ($row) {
				$accountID = $row->account_id;
				
				if ($loginAthenaGroup->loginServer->config->getUseMD5()) {
					$password = Flux::hashPassword($password);
				}

				$sql  = "INSERT INTO {$loginAthenaGroup->loginDatabase}.$loginLogTable ";
				$sql .= "(account_id, username, password, ip, error_code, login_date) ";
				$sql .= "VALUES (?, ?, ?, ?, ?, NOW())";
				$sth  = $loginAthenaGroup->connection->getStatement($sql);
				$sth->execute(array($accountID, $username, $password, $_SERVER['REMOTE_ADDR'], $e->getCode()));
			}
		}
		
		switch ($e->getCode()) {
			case LoginError::UNEXPECTED:
				$errorMessage = Flux::message('UnexpectedLoginError');
				break;
			case LoginError::INVALID_SERVER:
				$errorMessage = Flux::message('InvalidLoginServer');
				break;
			case LoginError::INVALID_LOGIN:
				$errorMessage = Flux::message('InvalidLoginCredentials');
				break;
			case LoginError::BANNED:
				$errorMessage = Flux::message('TemporarilyBanned');
				break;
			case LoginError::PERMABANNED:
				$errorMessage = Flux::message('PermanentlyBanned');
				break;
			case LoginError::IPBANNED:
				$errorMessage = Flux::message('IpBanned');
				break;
			case LoginError::INVALID_SECURITY_CODE:
				$errorMessage = Flux::message('InvalidSecurityCode');
				break;
			case LoginError::PENDING_CONFIRMATION:
				$errorMessage = Flux::message('PendingConfirmation');
				break;
			default:
				$errorMessage = Flux::message('CriticalLoginError');
				break;
		}
	}
}

$serverNames = $this->getServerNames();
?>
