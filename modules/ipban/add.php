<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('IpbanAddTitle');

if (count($_POST)) {
	if (!$params->get('addipban')) {
		$this->deny();
	}
	
	$list   = trim($params->get('list'));
	$reason = trim($params->get('reason'));
	$rtime  = trim($params->get('rtime_date'));
	
	if (!$list) {
		$errorMessage = Flux::message('IpbanEnterIpPattern');
	}
	elseif (!preg_match('/^([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]|\*)\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]|\*)\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]|\*)$/', $list, $m)) {
		$errorMessage = Flux::message('IpbanInvalidPattern');
	}
	elseif (preg_match('/' . Flux::config('IpWhitelistPattern') . '/', $list)) {
		$errorMessage = Flux::message('IpbanWhitelistedPattern');
	}
	elseif (!$reason) {
		$errorMessage = Flux::message('IpbanEnterReason');
	}
	elseif (!$rtime) {
		$errorMessage = Flux::message('IpbanSelectUnbanDate');
	}
	elseif (strtotime($rtime) <= time()) {
		$errorMessage = Flux::message('IpbanFutureDate');
	}
	else {
		$listArr   = array();
		$listArr[] = sprintf('%u.*.*.*', $m[1]);
		$listArr[] = sprintf('%u.%u.*.*', $m[1], $m[2]);
		$listArr[] = sprintf('%u.%u.%u.*', $m[1], $m[2], $m[3]);
		$listArr[] = sprintf('%u.%u.%u.%u', $m[1], $m[2], $m[3], $m[4]);
		
		$sql  = "SELECT list FROM {$server->loginDatabase}.ipbanlist WHERE rtime > NOW() AND ";
		$sql .= "(list = ? OR list = ? OR list = ? OR list = ?) LIMIT 1";
		$sth  = $server->connection->getStatement($sql);
		
		$sth->execute($listArr);
		$ipban = $sth->fetch();
		
		if ($ipban && $ipban->list) {
			$errorMessage = sprintf(Flux::message('IpbanAlreadyBanned'), $ipban->list);
		}
		else if ($server->loginServer->addIpBan($session->account->account_id, $reason, $rtime, $list)) {
			$session->setMessageData(sprintf(Flux::message('IpbanPatternBanned'), $list));
			$this->redirect($this->url('ipban'));
		}
		else {
			$errorMessage = Flux::message('IpbanAddFailed');
		}
	}
}
?>
