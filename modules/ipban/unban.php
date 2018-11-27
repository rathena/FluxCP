<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

if (!count($_POST) || !$params->get('unban') ) {
	$this->deny();
}

if (!(($unbanList=$params->get('unban_list')) instanceOf Flux_Config) || !count($unbanList=$unbanList->toArray())) {
	$session->setMessageData(Flux::message('IpbanNothingToUnban'));
}
else {
	$reason = trim($params->get('reason'));
	
	if (!$reason) {
		$session->setMessageData(Flux::message('IpbanEnterUnbanReason'));
	}
	else {
		$didAllSucceed = true;
		$numFailed = 0;
		
		foreach ($unbanList as $unban) {
			if (!$server->loginServer->removeIpBan($session->account->account_id, $reason, $unban)) {
				$didAllSucceed = false;
				$numFailed++;
			}
		}
		
		if ($didAllSucceed) {
			$session->setMessageData(Flux::message('IpbanUnbanned'));
		}
		else {
			$session->setMessageData(sprintf(Flux::message('IpbanUnbanFailed'), $numFailed));
		}
	}
}

$this->redirect($this->url('ipban'));
?>
