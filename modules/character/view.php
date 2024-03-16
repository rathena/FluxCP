<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'Viewing Character';

require_once 'Flux/TemporaryTable.php';

if($server->isRenewal) {
	$fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
	$mobdb = array("mob_db_re","mob_db2_re");
} else {
	$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
	$mobdb = array("mob_db","mob_db2");
}
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);

$charID = $params->get('id');

$col  = "ch.char_id, ch.account_id, ch.char_num, ch.name AS char_name, ch.class AS char_class, ch.base_level AS char_base_level, ";
$col .= "ch.job_level AS char_job_level, ch.base_exp AS char_base_exp, ch.job_exp AS char_job_exp, ch.zeny AS char_zeny, ";
$col .= "ch.str AS char_str, ch.agi AS char_agi, ch.vit AS char_vit, ";
$col .= "ch.int AS char_int, ch.dex AS char_dex, ch.luk AS char_luk, ch.max_hp AS char_max_hp, ch.hp AS char_hp, ";
$col .= "ch.max_sp AS char_max_sp, ch.sp AS char_sp, ch.status_point AS char_status_point, ";
$col .= "ch.skill_point AS char_skill_point, ch.online AS char_online, ch.party_id AS char_party_id, ";

$col .= "login.userid, login.account_id AS char_account_id, login.sex AS gender, ";
$col .= "partner.name AS partner_name, partner.char_id AS partner_id, ";
$col .= "mother.name AS mother_name, mother.char_id AS mother_id, ";
$col .= "father.name AS father_name, father.char_id AS father_id, ";
$col .= "child.name AS child_name, child.char_id AS child_id, ";
$col .= "guild.guild_id, guild.name AS guild_name, guild.emblem_id AS emblem, ";
$col .= "guild_position.name AS guild_position, IFNULL(guild_position.exp_mode, 0) AS guild_tax, ";
$col .= "party.name AS party_name, party.leader_char AS party_leader_id, party_leader.name AS party_leader_name, ";

$col .= "homun.name AS homun_name, homun.class AS homun_class, homun.level AS homun_level, homun.exp AS homun_exp, ";
$col .= "homun.intimacy AS homun_intimacy, homun.hunger AS homun_hunger, homun.str AS homun_str, homun.agi As homun_agi, ";
$col .= "homun.vit AS homun_vit, homun.int AS homun_int, homun.dex AS homun_dex, homun.luk AS homun_luk, ";
$col .= "homun.hp AS homun_hp, homun.max_hp As homun_max_hp, homun.sp AS homun_sp, homun.max_sp AS homun_max_sp, ";
$col .= "homun.skill_point AS homun_skill_point, homun.alive AS homun_alive, ";

$col .= "pet.class AS pet_class, pet.name AS pet_name, pet.level AS pet_level, pet.intimate AS pet_intimacy, ";
$col .= "pet.hungry AS pet_hungry, pet_mob.name_english AS pet_mob_name, pet_mob2.name_english AS pet_mob_name2, ";

$col .= "IFNULL(reg.value, 0) AS death_count";

$sql  = "SELECT $col FROM {$server->charMapDatabase}.`char` AS ch ";
$sql .= "LEFT OUTER JOIN {$server->loginDatabase}.login ON login.account_id = ch.account_id ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS partner ON partner.char_id = ch.partner_id ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS mother ON mother.char_id = ch.mother ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS father ON father.char_id = ch.father ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS child ON child.char_id = ch.child ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`guild_member` ON guild_member.char_id = ch.char_id ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`guild` ON guild.guild_id = guild_member.guild_id ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`guild_position` ON ";
$sql .= "(guild_member.position = guild_position.position AND guild_member.guild_id = guild_position.guild_id) ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`party` ON ch.party_id = party.party_id ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS party_leader ON party.leader_char = party_leader.char_id ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`homunculus` AS homun ON ch.homun_id = homun.homun_id ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`pet` ON ch.pet_id = pet.pet_id ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`".$mobdb[0]."` AS pet_mob ON pet_mob.ID = pet.class ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`".$mobdb[1]."` AS pet_mob2 ON pet_mob2.ID = pet.class ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char_reg_num` AS reg ON reg.char_id = ch.char_id AND reg.key = 'PC_DIE_COUNTER' ";
$sql .= "WHERE ch.char_id = ?";

$sth  = $server->connection->getStatement($sql);
$sth->execute(array($charID));

$char = $sth->fetch();

if ($char->pet_mob_name2) {
	$char->pet_mob_name = $char->pet_mob_name2;
}

if ($char && $char->char_account_id == $session->account->account_id) {
	$isMine = true;
}
else {
	$isMine = false;
}

if (!$isMine && !$auth->allowedToViewCharacter) {
	$this->deny();
}

if ($char) {
	$title = "Viewing Character ({$char->char_name})";
	
	$sql  = "SELECT fr.char_id, fr.name, fr.class, fr.base_level, fr.job_level, ";
	$sql .= "guild.guild_id, guild.name AS guild_name, fr.online, ";
	$sql .= "guild.emblem_id AS emblem ";
	$sql .= "FROM {$server->charMapDatabase}.`char` AS fr ";
	$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.guild ON guild.guild_id = fr.guild_id ";
	$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.friends ON friends.friend_id = fr.char_id ";
	$sql .= "WHERE friends.char_id = ? ORDER BY fr.name ASC";
	$sth  = $server->connection->getStatement($sql);
	
	$sth->execute(array($char->char_id));
	$friends = $sth->fetchAll();
	
	if ($char->party_leader_id) {
		$sql  = "SELECT p.char_id, p.name, p.class, p.base_level, p.job_level, ";
		$sql .= "guild.guild_id, guild.name AS guild_name, p.online, ";
		$sql .= "guild.emblem_id AS emblem ";
		$sql .= "FROM {$server->charMapDatabase}.`char` AS p ";
		$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.guild ON guild.guild_id = p.guild_id ";
		$sql .= "WHERE p.party_id = ? AND p.char_id != ? ORDER BY p.name ASC";
		$sth  = $server->connection->getStatement($sql);
		
		$sth->execute(array($char->char_party_id, $char->char_id));
		$partyMembers = $sth->fetchAll();
	}
	else {
		$partyMembers = array();
	}
	
	$col  = "inventory.*, items.name_english, items.type, items.slots, c.char_id, c.name AS char_name";
	
	$sql  = "SELECT $col FROM {$server->charMapDatabase}.inventory ";
	$sql .= "LEFT JOIN {$server->charMapDatabase}.items ON items.id = inventory.nameid ";
	$sql .= "LEFT JOIN {$server->charMapDatabase}.`char` AS c ";
	$sql .= "ON c.char_id = IF(inventory.card0 IN (254, 255), ";
	$sql .= "IF(inventory.card2 < 0, inventory.card2 + 65536, inventory.card2) ";
	$sql .= "| (inventory.card3 << 16), NULL) ";
	$sql .= "WHERE inventory.char_id = ? ";
	
	if (!$auth->allowedToSeeUnknownItems) {
		$sql .= 'AND inventory.identify > 0 ';
	}
	
	$sql .= "ORDER BY IF(inventory.equip > 0, 1, 0) DESC, inventory.nameid ASC, inventory.identify DESC, ";
	$sql .= "inventory.attribute DESC, inventory.refine ASC";
	
	$sth  = $server->connection->getStatement($sql);
	$sth->execute(array($char->char_id));
	
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
	
	$col  = "cart_inventory.*, items.name_english, items.type, items.slots, c.char_id, c.name AS char_name";
	
	$sql  = "SELECT $col FROM {$server->charMapDatabase}.cart_inventory ";
	$sql .= "LEFT JOIN {$server->charMapDatabase}.items ON items.id = cart_inventory.nameid ";
	$sql .= "LEFT JOIN {$server->charMapDatabase}.`char` AS c ";
	$sql .= "ON c.char_id = IF(cart_inventory.card0 IN (254, 255), ";
	$sql .= "IF(cart_inventory.card2 < 0, cart_inventory.card2 + 65536, cart_inventory.card2) ";
	$sql .= "| (cart_inventory.card3 << 16), NULL) ";
	$sql .= "WHERE cart_inventory.char_id = ? ";
	
	if (!$auth->allowedToSeeUnknownItems) {
		$sql .= 'AND cart_inventory.identify > 0 ';
	}
	
	$sql .= "ORDER BY cart_inventory.nameid ASC, cart_inventory.identify DESC, ";
	$sql .= "cart_inventory.attribute DESC, cart_inventory.refine ASC";
	
	$sth  = $server->connection->getStatement($sql);
	$sth->execute(array($char->char_id));
	
	$cart_items = $sth->fetchAll();
	$cart_cards = array();

	if ($cart_items) {
		$cardIDs = array();

		foreach ($cart_items as $item) {
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
					$cart_cards[$card->id] = $card->name_english;
				}
			}
		}
	}
	
	$itemAttributes = Flux::config('Attributes')->toArray();
	$type_list = Flux::config('ItemTypes')->toArray();
}
?>
