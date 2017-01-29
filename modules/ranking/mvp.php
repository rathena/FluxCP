<?php
if (!defined('FLUX_ROOT')) exit;
$title = 'MVP Ranking';
$classes  = Flux::config('JobClasses')->toArray();
$jobClass = $params->get('jobclass');
$bind     = array();

if (trim($jobClass) === '') {
	$jobClass = null;
}

if (!is_null($jobClass) && !array_key_exists($jobClass, $classes)) {
	$this->deny();
}

if($server->isRenewal) {
	$fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
} else {
	$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
}
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);
	

// Latest Kills
$sql = "SELECT mvp.mvp_date, mvp.kill_char_id, mvp.monster_id, mvp.map,  FROM {$server->logsDatabase}.`mvplog` AS mvp ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.guild ON guild.guild_id = ch.guild_id ";





// Players with most kills
$col  = "ch.char_id, ch.name AS char_name, ch.class AS char_class,  ";
$col .= "ch.guild_id, guild.name AS guild_name, guild.emblem_len AS guild_emblem_len, ";
$col .= "CAST(IFNULL(reg.value, '0') AS UNSIGNED) AS death_count";

$sql  = "SELECT $col FROM {$server->charMapDatabase}.`char` AS ch ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.guild ON guild.guild_id = ch.guild_id ";
$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = ch.account_id ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.`char_reg_num` AS reg ON reg.char_id = ch.char_id AND reg.key = 'PC_DIE_COUNTER' ";
$sql .= "WHERE 1=1 ";

if (Flux::config('HidePermBannedDeathRank')) {
	$sql .= "AND login.state != 5 ";
}
if (Flux::config('HideTempBannedDeathRank')) {
	$sql .= "AND (login.unban_time IS NULL OR login.unban_time = 0) ";
}

$groups = AccountLevel::getGroupID((int)Flux::config('RankingHideGroupLevel'), '<');
if(!empty($groups)) {
	$ids   = implode(', ', array_fill(0, count($groups), '?'));
	$sql  .= "AND login.group_id IN ($ids) ";
	$bind  = array_merge($bind, $groups);
}

if (!is_null($jobClass)) {
	$sql .= "AND ch.class = ? ";
	$bind[] = $jobClass;
}

$sql .= "ORDER BY death_count DESC, ch.char_id DESC ";
$sql .= "LIMIT ".(int)Flux::config('DeathRankingLimit');
$sth  = $server->connection->getStatement($sql);

$sth->execute($bind);

$chars = $sth->fetchAll();

