<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('GenderChangeTitle');

$cost    = +(int)Flux::config('ChargeGenderChange');
$badJobs = Flux::config('GenderLinkedJobClasses')->toArray();

if ($cost && $session->account->balance < $cost && !$auth->allowedToAvoidSexChangeCost) {
	$hasNecessaryFunds = false;
}
else {
	$hasNecessaryFunds = true;
}

if (count($_POST)) {
	if (!$hasNecessaryFunds || !$params->get('changegender')) {
		$this->deny();
	}
	
	$classes = array();
	foreach ($session->loginAthenaGroup->athenaServers as $athenaServer) {
		$sql = "SELECT COUNT(1) AS num FROM {$athenaServer->charMapDatabase}.`char` WHERE account_id = ? AND `class` IN (".implode(',', array_fill(0, count($badJobs), '?')).")";
		$sth = $athenaServer->connection->getStatement($sql);
		$sth->execute(array_merge(array($session->account->account_id), array_keys($badJobs)));
		
		if ($sth->fetch()->num) {
			$errorMessage = sprintf(Flux::message('GenderChangeBadChars'), implode(', ', array_values($badJobs)));
			break;
		}
	}
	
	if (empty($errorMessage)) {
		$sex = $session->account->sex == 'M' ? 'F' : 'M';
		$sql = "UPDATE {$server->loginDatabase}.login SET sex = ? WHERE account_id = ?";
		$sth = $server->connection->getStatement($sql);

		$sth->execute(array($sex, $session->account->account_id));

		$changeTimes = (int)$session->loginServer->getPref($session->account->account_id, 'NumberOfGenderChanges');
		$session->loginServer->setPref($session->account->account_id, 'NumberOfGenderChanges', $changeTimes + 1);

		if ($cost && !$auth->allowedToAvoidSexChangeCost) {
			$session->loginServer->depositCredits($session->account->account_id, -$cost);
			$session->setMessageData(sprintf(Flux::message('GenderChanged'), $cost));
		}
		else {
			$session->setMessageData(Flux::message('GenderChangedForFree'));
		}

		$this->redirect($this->url('account', 'view'));
	}
}
?>
