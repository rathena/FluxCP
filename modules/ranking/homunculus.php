<?php
if (!defined('FLUX_ROOT')) exit;

$title    = 'Homunculus Ranking';
$classes  = Flux::config('HomunClasses')->toArray();
$homunClass = $params->get('homunclass');
$bind     = array();

if (trim($homunClass) === '') {
	$homunClass = null;
}

if (!is_null($homunClass) && !array_key_exists($homunClass, $classes)) {
	$this->deny();
}

$col  = "hm.name AS homun_name, hm.char_id AS owner, `char`.name AS owner_name, hm.class AS homun_class, hm.intimacy, hm.level, hm.exp";

$sql  = "SELECT $col FROM {$server->charMapDatabase}.homunculus AS hm ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.`char` ON `char`.homun_id = hm.homun_id ";
$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
$sql .= "WHERE 1=1 ";

if (Flux::config('HidePermBannedHomunRank')) {
	$sql .= "AND login.state != 5 ";
}
if (Flux::config('HideTempBannedHomunRank')) {
	$sql .= "AND (login.unban_time IS NULL OR login.unban_time = 0) ";
}

$groups = AccountLevel::getGroupID((int)Flux::config('RankingHideGroupLevel'), '<');
if(!empty($groups)) {
	$ids   = implode(', ', array_fill(0, count($groups), '?'));
	$sql  .= "AND login.group_id IN ($ids) ";
	$bind  = array_merge($bind, $groups);
}

if ($days=Flux::config('HomunRankingThreshold')) {
	$sql    .= 'AND TIMESTAMPDIFF(DAY, login.lastlogin, NOW()) <= ? ';
	$bind[]  = $days * 24 * 60 * 60;
}

if (!is_null($homunClass)) {
	$sql .= "AND hm.class = ? ";
	$bind[] = $homunClass;
}

$sql .= "ORDER BY hm.level DESC, hm.exp DESC, hm.intimacy DESC, hm.homun_id ASC ";
$sql .= "LIMIT ".(int)Flux::config('HomunRankingLimit');
$sth  = $server->connection->getStatement($sql);

$sth->execute($bind);

$homuns = $sth->fetchAll();

?>
