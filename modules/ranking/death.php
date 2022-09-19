<?php
if (!defined('FLUX_ROOT')) exit;

$title    = 'Death Ranking';
$classes  = Flux::config('JobClasses')->toArray();
$jobClass = $params->get('jobclass');
$bind     = array();

if (trim($jobClass) === '') {
	$jobClass = null;
}

if (!is_null($jobClass) && !array_key_exists($jobClass, $classes)) {
	$this->deny();
}

$col  = "ch.char_id, ch.name AS char_name, ch.class AS char_class, ch.base_level, ch.job_level, ";
$col .= "ch.guild_id, guild.name AS guild_name, guild.emblem_id as emblem, ";

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

if ($days=Flux::config('DeathRankingThreshold')) {
	$sql    .= 'AND TIMESTAMPDIFF(DAY, login.lastlogin, NOW()) <= ? ';
	$bind[]  = $days * 24 * 60 * 60;
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
?>
