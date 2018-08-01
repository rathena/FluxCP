<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('AccountEditTitle');

$accountID = $params->get('id');

$creditsTable  = Flux::config('FluxTables.CreditsTable');
$creditColumns = 'credits.balance, credits.last_donation_date, credits.last_donation_amount';

$sql  = "SELECT login.*, {$creditColumns} FROM {$server->loginDatabase}.login ";
$sql .= "LEFT OUTER JOIN {$creditsTable} AS credits ON login.account_id = credits.account_id ";
$sql .= "WHERE login.sex != 'S' AND login.group_id >= 0 AND login.account_id = ? LIMIT 1";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array($accountID));

// Account object.
$account = $sth->fetch();
$isMine  = false;

if ($account) {
	if ($account->group_id > $session->account->group_id && !$auth->allowedToEditHigherPower) {
		$this->deny();
	}
	
	$isMine = $account->account_id == $session->account->account_id;
	
	if ($isMine) {
		$title = Flux::message('AccountEditTitle2');
	}
	else {
		$title = sprintf(Flux::message('AccountEditTitle3'), $account->userid);
	}
	
	if (count($_POST)) {
		$groups     = AccountLevel::getArray();
	
		$email      = trim($params->get('email'));
		$gender     = trim($params->get('gender'));
		$loginCount = (int)$params->get('logincount');
		$birthdate  = $params->get('birthdate_date');
		$lastLogin  = $params->get('lastlogin_date');
		$lastIP     = trim($params->get('last_ip'));
		$group_id   = (int)$params->get('group_id');
		$balance    = (int)$params->get('balance');
		
		if ($isMine && $account->group_id != $group_id) {
			$errorMessage = Flux::message('CannotModifyOwnGroupID');
		}
		elseif ($account->group_id != $group_id && !$auth->allowedToEditAccountGroupID) {
			$errorMessage = Flux::message('CannotModifyAnyGroupID');
		}
		elseif ($group_id > $session->account->group_id) {
			$errorMessage = Flux::message('CannotModifyGroupIDHigh');
		}
		elseif (!isset($groups[$group_id])) {
			$errorMessage = Flux::message('InvalidGroupID');
		}
		elseif (!in_array($gender, array('M', 'F'))) {
			$errorMessage = Flux::message('InvalidGender');
		}
		elseif ($account->balance != $balance && !$auth->allowedToEditAccountBalance) {
			$errorMessage = Flux::message('CannotModifyBalance');
		}
		elseif ($birthdate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate)) {
			$errorMessage = Flux::message('InvalidBirthdate');
		}
		elseif ($lastLogin && !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $lastLogin)) {
			$errorMessage = Flux::message('InvalidLastLoginDate');
		}
		else {
			$bind = array(
				'email'      => $email,
				'sex'        => $gender,
				'logincount' => $loginCount,
				'birthdate'  => $birthdate ? $birthdate : $account->birthdate,
				'lastlogin'  => $lastLogin ? $lastLogin : $account->lastlogin,
				'last_ip'    => $lastIP,
			);
			
			$sql  = "UPDATE {$server->loginDatabase}.login SET email = :email, ";
			$sql .= "sex = :sex, logincount = :logincount, birthdate = :birthdate, lastlogin = :lastlogin, last_ip = :last_ip";
			
			if ($auth->allowedToEditAccountGroupID) {
				$sql .= ", group_id = :group_id";
				$bind['group_id'] = $group_id;
			}
			
			$bind['account_id'] = $account->account_id;
			
			$sql .= " WHERE account_id = :account_id";
			$sth  = $server->connection->getStatement($sql);
			$sth->execute($bind);

			if ($auth->allowedToEditAccountBalance) {
				$deposit = $balance - $account->balance;
				$session->loginServer->depositCredits($account->account_id, $deposit);
			}
			
			$session->setMessageData(Flux::message('AccountModified'));
			$this->redirect($this->url('account', 'view', array('id' => $account->account_id)));
		}
	}
}
?>
