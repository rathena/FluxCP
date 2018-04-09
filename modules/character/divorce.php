<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('DivorceTitle');

$this->loginRequired();

$charID = $params->get('id');
if (!$charID) {
	$this->deny();
}

$char = $server->getCharacter($charID);
if ($char->account_id != $session->account->account_id && !$auth->allowedToDivorceCharacter) {
	$this->deny();
}

if (!$char->partner_id) {
	$session->setMessageData(sprintf(Flux::message('DivorceNotMarried'), $char->name));
	$this->redirect($this->referer);
}

$partner = $server->getCharacter($char->partner_id);
if (!$partner) {
	$session->setMessageData(Flux::message('DivorceInvalidPartner'));
	$this->redirect($this->referer);
}

$child = false;
if ($char->child && !($child=$server->getCharacter($char->child))) {
	$session->setMessageData(Flux::message('DivorceInvalidChild'));
	$this->redirect($this->referer);
}

if ($char->online || $partner->online || (!Flux::config('DivorceKeepChild') && $child && $child->online)) {
	$session->setMessageData(sprintf(Flux::message(Flux::config('DivorceKeepChild') ? 'DivorceMustBeOffline' : 'DivorceMustBeOffline2'), $char->name));
	$this->redirect($this->referer);
}

if (count($_POST) && $params->get('divorce')) {
	$sql = "UPDATE {$server->charMapDatabase}.`char` SET partner_id = 0 ";
	if (!Flux::config('DivorceKeepChild')) {
		$sql .= ", child = 0 ";
	}
	$sql .= "WHERE char_id IN (?, ?)";
	$sth  = $server->connection->getStatement($sql);
	$sth->execute(array($charID, $char->partner_id));
	
	if (!Flux::config('DivorceKeepChild') && $child) {
		$sql = "UPDATE {$server->charMapDatabase}.`char` SET father = 0, mother = 0 WHERE char_id = ?";
		$sth = $server->connection->getStatement($sql);
		$sth->execute(array($char->child));
	}
	
	if (!Flux::config('DivorceKeepRings')) {
		$sql  = "DELETE FROM {$server->charMapDatabase}.inventory ";
		$sql .= "WHERE char_id IN (?, ?) AND nameid IN (2634, 2635) AND card0 = 255 AND (";
		$sql .= "(card2 = IF(? & 0xFFFF > 32767, (? & 0xFFFF) - 65536, ? & 0xFFFF) AND card3 = (? & 0xFFFF0000) >> 16) ";
		$sql .= "OR (card2 = IF(? & 0xFFFF > 32767, (? & 0xFFFF) - 65536, ? & 0xFFFF) AND card3 = (? & 0xFFFF0000) >> 16))";
		$sth  = $server->connection->getStatement($sql);
		$sth->execute(array($charID, $char->partner_id, $charID, $charID, $charID, $charID, $char->partner_id, $char->partner_id, $char->partner_id, $char->partner_id));
	}
	
	$session->setMessageData(sprintf(Flux::message('DivorceSuccessful'), $char->name));
	$this->redirect();
}
?>
