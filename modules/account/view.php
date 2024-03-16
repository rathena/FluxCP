<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('AccountViewTitle');

require_once 'Flux/TemporaryTable.php';

if($server->isRenewal) {
	$fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
} else {
	$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
}
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);

$creditsTable  = Flux::config('FluxTables.CreditsTable');
$creditColumns = 'credits.balance, credits.last_donation_date, credits.last_donation_amount';
$createTable   = Flux::config('FluxTables.AccountCreateTable');
$createColumns = 'created.confirmed, created.confirm_code, created.reg_date';
$isMine        = false;
$accountID     = $params->get('id');
$account       = false;

if (!$accountID || $accountID == $session->account->account_id) {
	$isMine    = true;
	$accountID = $session->account->account_id;
	$account   = $session->account;
}

if (!$isMine) {
	// Allowed to view other peoples' account information?
	if (!$auth->allowedToViewAccount) {
		$this->deny();
	}

	$sql  = "SELECT login.*, {$creditColumns}, {$createColumns} FROM {$server->loginDatabase}.login ";
	$sql .= "LEFT OUTER JOIN {$server->loginDatabase}.{$creditsTable} AS credits ON login.account_id = credits.account_id ";
	$sql .= "LEFT OUTER JOIN {$server->loginDatabase}.{$createTable} AS created ON login.account_id = created.account_id ";
	$sql .= "WHERE login.sex != 'S' AND login.group_id >= 0 AND login.account_id = ? LIMIT 1";
	$sth  = $server->connection->getStatement($sql);
	$sth->execute(array($accountID));

	// Account object.
	$account = $sth->fetch();

	if ($account) {
		$title = sprintf(Flux::message('AccountViewTitle2'), $account->userid);
	}
}
else {
	$title = Flux::message('AccountViewTitle3');
}

$level       = AccountLevel::getGroupLevel($account->group_id);

$banSuperior = $account && (($level > $session->account->group_level && $auth->allowedToBanHigherPower) || $level <= $session->account->group_level);
$canTempBan  = !$isMine && $banSuperior && $auth->allowedToTempBanAccount;
$canPermBan  = !$isMine && $banSuperior && $auth->allowedToPermBanAccount;
$tempBanned  = $account && $account->unban_time > 0;
$permBanned  = $account && $account->state == 5;
$showTempBan = !$isMine && !$tempBanned && !$permBanned && $auth->allowedToTempBanAccount;
$showPermBan = !$isMine && !$permBanned && $auth->allowedToPermBanAccount;
$showUnban   = !$isMine && ($tempBanned && $auth->allowedToTempUnbanAccount) || ($permBanned && $auth->allowedToPermUnbanAccount);

if($account->vip_time != '0'){
$vipexpiretime = $account->vip_time;
	$dt = new DateTime("@$vipexpiretime");
	$vipexpires = 'Expires '.$dt->format('Y-m-d');
} elseif ($account->vip_time == '0'){
	$vipexpires = 'Standard Account';
} else {$vipexpires = 'Unknown';}

if (count($_POST) && $account) {
	$reason = (string)$params->get('reason');

	if ($params->get('tempban') && ($tempBanDate=$params->get('tempban_date'))) {
		if ($canTempBan) {
			if ($server->loginServer->temporarilyBan($session->account->account_id, $reason, $account->account_id, $tempBanDate)) {
				$formattedDate = $this->formatDateTime($tempBanDate);
				$session->setMessageData("Account has been temporarily banned until $formattedDate.");
				$this->redirect($this->url('account', 'view', array('id' => $account->account_id)));
			}
			else {
				$errorMessage = Flux::message('AccountTempBanFailed');
			}
		}
		else {
			$errorMessage = Flux::message('AccountTempBanUnauth');
		}
	}
	elseif ($params->get('permban')) {
		if ($canPermBan) {
			if ($server->loginServer->permanentlyBan($session->account->account_id, $reason, $account->account_id)) {
				$session->setMessageData("Account has been permanently banned.");
				$this->redirect($this->url('account', 'view', array('id' => $account->account_id)));
			}
			else {
				$errorMessage = Flux::message('AccountPermBanFailed');
			}
		}
		else {
			$errorMessage = Flux::message('AccountPermBanUnauth');
		}
	}
	elseif ($params->get('unban')) {
		$tbl = Flux::config('FluxTables.AccountCreateTable');
		$sql = "SELECT account_id FROM {$server->loginDatabase}.$tbl WHERE confirmed = 0 AND account_id = ?";
		$sth = $server->connection->getStatement($sql);

		$sth->execute(array($account->account_id));
		$confirm = $sth->fetch();

		$sql = "UPDATE {$server->loginDatabase}.$tbl SET confirmed = 1, confirm_expire = NULL WHERE account_id = ?";
		$sth = $server->connection->getStatement($sql);

		if ($tempBanned && $auth->allowedToTempUnbanAccount &&
				$server->loginServer->unban($session->account->account_id, $reason, $account->account_id)) {

			if ($confirm) {
				$sth->execute(array($account->account_id));
			}

			$session->setMessageData(Flux::message('AccountLiftTempBan'));
			$this->redirect($this->url('account', 'view', array('id' => $account->account_id)));
		}
		elseif ($permBanned && $auth->allowedToPermUnbanAccount &&
				$server->loginServer->unban($session->account->account_id, $reason, $account->account_id)) {

			if ($confirm) {
				$sth->execute(array($account->account_id));
			}

			$session->setMessageData(Flux::message('AccountLiftPermBan'));
			$this->redirect($this->url('account', 'view', array('id' => $account->account_id)));
		}
		else {
			$errorMessage = Flux::message('AccountLiftBanUnauth');
		}
	}
}

$banInfo = false;
if ($account) {
	$banInfo = $server->loginServer->getBanInfo($account->account_id);
}

$characters = array();
foreach ($session->getAthenaServerNames() as $serverName) {
	$athena = $session->getAthenaServer($serverName);

	$sql  = "SELECT ch.*, guild.name AS guild_name ";
	$sql .= "FROM {$athena->charMapDatabase}.`char` AS ch ";
	$sql .= "LEFT OUTER JOIN {$athena->charMapDatabase}.guild ON guild.guild_id = ch.guild_id ";
	$sql .= "WHERE ch.account_id = ? ORDER BY ch.char_num ASC";
	$sth  = $server->connection->getStatement($sql);
	$sth->execute(array($accountID));

	$chars = $sth->fetchAll();
	$characters[$athena->serverName] = $chars;
}

$col  = "storage.*, items.name_english, items.type, items.slots, c.char_id, c.name AS char_name";

$sql  = "SELECT $col FROM {$server->charMapDatabase}.storage ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.items ON items.id = storage.nameid ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.`char` AS c ";
$sql .= "ON c.char_id = IF(storage.card0 IN (254, 255), ";
$sql .= "IF(storage.card2 < 0, storage.card2 + 65536, storage.card2) ";
$sql .= "| (storage.card3 << 16), NULL) ";
$sql .= "WHERE storage.account_id = ? ";

if (!$auth->allowedToSeeUnknownItems) {
	$sql .= 'AND storage.identify > 0 ';
}

if ($account) {
	$sql .= "ORDER BY storage.nameid ASC, storage.identify DESC, ";
	$sql .= "storage.attribute DESC, storage.refine ASC";

	$sth  = $server->connection->getStatement($sql);
	$sth->execute(array($account->account_id));

	$items = $sth->fetchAll();
	$cards = array();

	if ($items) {
		$cardIDs = array();

		foreach ($items as $item) {
			$item->cardsOver = -$item->slots;

			if ($item->card0) {
				$cardIDs[] = $item->card0;
				$item->cardsOver++;
			}
			if ($item->card1) {
				$cardIDs[] = $item->card1;
				$item->cardsOver++;
			}
			if ($item->card2) {
				$cardIDs[] = $item->card2;
				$item->cardsOver++;
			}
			if ($item->card3) {
				$cardIDs[] = $item->card3;
				$item->cardsOver++;
			}

			if ($item->card0 == 254 || $item->card0 == 255 || $item->card0 == -256 || $item->cardsOver < 0) {
				$item->cardsOver = 0;
			}

			if($server->isRenewal) {
				$temp = array();
				if ($item->option_id0)	array_push($temp, array($item->option_id0, $item->option_val0));
				if ($item->option_id1) 	array_push($temp, array($item->option_id1, $item->option_val1));
				if ($item->option_id2) 	array_push($temp, array($item->option_id2, $item->option_val2));
				if ($item->option_id3) 	array_push($temp, array($item->option_id3, $item->option_val3));
				if ($item->option_id4) 	array_push($temp, array($item->option_id4, $item->option_val4));
				$item->rndopt = $temp;
			}
		}

		if ($cardIDs) {
			$ids = implode(',', array_fill(0, count($cardIDs), '?'));
			$sql = "SELECT id, name_english FROM {$server->charMapDatabase}.items WHERE id IN ($ids)";
			$sth = $server->connection->getStatement($sql);

			$sth->execute($cardIDs);
			$temp = $sth->fetchAll();
			if ($temp) {
				foreach ($temp as $card) {
					$cards[$card->id] = $card->name_english;
				}
			}
		}
	}

	$itemAttributes = Flux::config('Attributes')->toArray();
	$type_list = Flux::config('ItemTypes')->toArray();
}
?>
