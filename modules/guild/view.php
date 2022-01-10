<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'Viewing Guild';

require_once 'Flux/TemporaryTable.php';

if($server->isRenewal) {
	$fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
} else {
	$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
}
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);

$guildID = $params->get('id');

$col  = "guild.guild_id, guild.name, guild.char_id, guild.master, guild.guild_lv, guild.connect_member, guild.max_member, ";
$col .= "guild.average_lv, guild.exp, guild.next_exp, guild.skill_point, REPLACE(guild.mes1, '|00', '') AS mes1, REPLACE(guild.mes2, '|00', '') AS mes2, ";
$col .= "guild.emblem_id, guild.emblem_data, `char`.name AS guild_master, ";
if(Flux::config('EmblemUseWebservice'))
	$col .= "guild_emblems.file_data as emblem_len ";
else
	$col .= "guild.emblem_len ";

$sql  = "SELECT $col FROM {$server->charMapDatabase}.guild ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.`char` ON `char`.char_id = guild.char_id ";
if(Flux::config('EmblemUseWebservice'))
	$sql .= "LEFT JOIN {$server->charMapDatabase}.`guild_emblems` ON `guild_emblems`.guild_id = `char`.guild_id ";	
$sql .= "WHERE guild.guild_id = ?";

$sth  = $server->connection->getStatement($sql);
$sth->execute(array($guildID));

$guild = $sth->fetch();

$col  = "guild_alliance.alliance_id, guild.name";

$sql  = "SELECT $col FROM {$server->charMapDatabase}.guild_alliance LEFT JOIN {$server->charMapDatabase}.guild ON guild_alliance.alliance_id = guild.guild_id ";
$sql .= "WHERE guild_alliance.guild_id = ? AND guild_alliance.opposition = 0 ORDER BY guild_alliance.alliance_id ASC";

$sth  = $server->connection->getStatement($sql);
$sth->execute(array($guildID));

$alliances = $sth->fetchAll();

$col  = "guild_alliance.alliance_id, guild.name";

$sql  = "SELECT $col FROM {$server->charMapDatabase}.guild_alliance LEFT JOIN {$server->charMapDatabase}.guild ON guild_alliance.alliance_id = guild.guild_id ";
$sql .= "WHERE guild_alliance.guild_id = ? AND guild_alliance.opposition = 1 ORDER BY guild_alliance.alliance_id ASC";

$sth  = $server->connection->getStatement($sql);
$sth->execute(array($guildID));

$oppositions = $sth->fetchAll();

if ($guild) {
	$title = "Viewing Guild ({$guild->name})";
}

$col  = "ch.account_id, ch.char_id, ch.name, ch.class, ch.base_level, ch.job_level, ";
$col .= "IF(ch.online = 1, 'Online Now!', ";
$col .= "CASE DATE_FORMAT(acc.lastlogin, '%Y-%m-%d') ";
$col .= "WHEN DATE_FORMAT(NOW(), '%Y-%m-%d') THEN 'Today' ";
$col .= "WHEN DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 DAY), '%Y-%m-%d') THEN 'Yesterday' ";
$col .= "ELSE CONCAT(DATEDIFF(NOW(), acc.lastlogin), ' Days Ago') ";
$col .= "END) AS lastlogin, ";
$col .= "IFNULL(roster.exp, 0) AS devotion, roster.position, ";
$col .= "pos.name AS position_name, pos.mode, IFNULL(pos.exp_mode, 0) AS guild_tax";

$sql  = "SELECT $col FROM {$server->charMapDatabase}.`char` AS ch ";
$sql .= "LEFT JOIN {$server->loginDatabase}.login AS acc ON acc.account_id = ch.account_id ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.guild_member AS roster ON (roster.guild_id = ch.guild_id AND roster.char_id = ch.char_id) ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.guild_position AS pos ON (pos.guild_id = ch.guild_id AND pos.position = roster.position) ";
$sql .= "WHERE ch.guild_id = ? ORDER BY roster.position ASC, acc.lastlogin DESC";

$sth  = $server->connection->getStatement($sql);
$sth->execute(array($guildID));

$members = $sth->fetchAll();

$isMine  = false;
$amOwner = false;
foreach ($members as $member) {
	if ($guild && $member->account_id == $session->account->account_id) {
		$isMine = true;
		if ($member->position == 0) {
			$amOwner = true;
		}
	}
}

if (!$isMine && !$auth->allowedToViewGuild) {
	$this->deny();
}

$col  = "account_id, name, REPLACE(mes, '|00', '') AS mes";

$sql  = "SELECT $col FROM {$server->charMapDatabase}.guild_expulsion ";
$sql .= "WHERE guild_id = ? ORDER BY name ASC";

$sth  = $server->connection->getStatement($sql);
$sth->execute(array($guildID));

$expulsions = $sth->fetchAll();

if (!Flux::config('GStorageLeaderOnly') || $amOwner || $auth->allowedToViewGuild) {
	$col  = "guild_storage.*, items.name_english, items.type, items.slots, c.char_id, c.name AS char_name";

	$sql  = "SELECT $col FROM {$server->charMapDatabase}.guild_storage ";
	$sql .= "LEFT JOIN {$server->charMapDatabase}.items ON items.id = guild_storage.nameid ";
	$sql .= "LEFT JOIN {$server->charMapDatabase}.`char` AS c ";
	$sql .= "ON c.char_id = IF(guild_storage.card0 IN (254, 255), ";
	$sql .= "IF(guild_storage.card2 < 0, guild_storage.card2 + 65536, guild_storage.card2) ";
	$sql .= "| (guild_storage.card3 << 16), NULL) ";
	$sql .= "WHERE guild_storage.guild_id = ? ";

	if (!$auth->allowedToSeeUnknownItems) {
		$sql .= 'AND guild_storage.identify > 0 ';
	}

	$sql .= "ORDER BY guild_storage.nameid ASC, guild_storage.identify DESC, ";
	$sql .= "guild_storage.attribute ASC, guild_storage.refine ASC";

	$sth  = $server->connection->getStatement($sql);
	$sth->execute(array($guildID));

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
