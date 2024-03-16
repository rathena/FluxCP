<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Guild Ranking';

$castleNames = Flux::config('CastleNames')->toArray();
$ids  = implode(',', array_fill(0, count($castleNames), '?'));
$bind = array_keys($castleNames);

$col  = "g.guild_id, g.name, g.guild_lv, g.average_lv, g.emblem_id as emblem, ";
$col .= "GREATEST(g.exp, (SELECT SUM(exp) FROM {$server->charMapDatabase}.guild_member WHERE guild_member.guild_id = g.guild_id)) AS exp, ";
$col .= "(SELECT COUNT(char_id) FROM {$server->charMapDatabase}.`char` WHERE `char`.guild_id = g.guild_id) AS members, ";
$col .= "(SELECT COUNT(castle_id) FROM {$server->charMapDatabase}.guild_castle WHERE guild_castle.guild_id = g.guild_id AND castle_id IN ($ids)) AS castles";

$sql  = "SELECT $col FROM {$server->charMapDatabase}.guild AS g ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.`char` AS ch ON ch.char_id = g.char_id ";
$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = ch.account_id ";

$groups = AccountLevel::getGroupID((int)Flux::config('RankingHideGroupLevel'), '<');
if(!empty($groups)) {
	$ids   = implode(', ', array_fill(0, count($groups), '?'));
	$sql  .= "WHERE login.group_id IN ($ids) ";
	$bind  = array_merge($bind, $groups);
}

$sql .= "ORDER BY g.guild_lv DESC, castles DESC, exp DESC, (g.average_lv + members) DESC, ";
$sql .= "g.average_lv DESC, members DESC, g.max_member DESC, g.next_exp ASC ";
$sql .= "LIMIT ".(int)Flux::config('GuildRankingLimit');
$sth  = $server->connection->getStatement($sql);

$sth->execute($bind);
$guilds = $sth->fetchAll();
?>
