<?php
if (!defined('FLUX_ROOT')) exit;

$title         = 'Alchemist Ranking';
$alchemistJobs = Flux::config('AlchemistJobClasses')->toArray();
$jobClass      = $params->get('jobclass');
$bind          = array();

if (trim($jobClass) === '') {
	$jobClass = null;
}

if (!is_null($jobClass) && !array_key_exists($jobClass, $alchemistJobs)) {
	$this->deny();
}

$col  = "ch.char_id, ch.name AS char_name, ch.fame, ch.class AS char_class, ch.base_level, ch.job_level, ";
$col .= "ch.guild_id, guild.name AS guild_name, guild.emblem_len AS guild_emblem_len";

$sql  = "SELECT $col FROM {$server->charMapDatabase}.`char` AS ch ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.guild ON guild.guild_id = ch.guild_id ";
$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = ch.account_id ";

$ids = implode(',', array_fill(0, count($alchemistJobs), '?'));
$bind = array_keys($alchemistJobs);

$sql .= "WHERE 1=1 AND fame > 0 AND ch.class IN ($ids) ";

if (Flux::config('HidePermBannedAlcheRank')) {
	$sql .= "AND login.state != 5 ";
}
if (Flux::config('HideTempBannedAlcheRank')) {
	$sql .= "AND (login.unban_time IS NULL OR login.unban_time = 0) ";
}

if (!is_null($jobClass)) {
	$sql .= "AND ch.class = ? ";
	$bind[] = $jobClass;
}

$sql .= "ORDER BY ch.fame DESC, ch.base_level DESC, ch.base_exp DESC, ch.job_level DESC, ch.job_exp DESC, ch.char_id ASC ";
$sql .= "LIMIT ". (int)Flux::config('AlchemistRankingLimit');
$sth  = $server->connection->getStatement($sql);

$sth->execute($bind);

$chars = $sth->fetchAll();
?>