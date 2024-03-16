<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'List Guilds';

$bind        = array();
$sqlpartial  = "LEFT JOIN {$server->charMapDatabase}.`char` ON `char`.char_id = guild.char_id ";
$sqlpartial .= "WHERE 1=1 ";

$guildID = $params->get('id');
if ($guildID) {
	$sqlpartial .= "AND guild.guild_id = ? ";
	$bind[]      = $guildID;
}
else {
	$opMapping    = array('eq' => '=', 'gt' => '>', 'lt' => '<');
	$opValues     = array_keys($opMapping);
	$guildName    = $params->get('guild_name');
	$charID       = $params->get('char_id');
	$charName     = $params->get('master');
	$guildLevel   = $params->get('guild_level');
	$guildLevelOp = $params->get('guild_level_op');
	$connectMem   = $params->get('connect_member');
	$connectMemOp = $params->get('connect_member_op');
	$maxMem       = $params->get('max_member');
	$maxMemOp     = $params->get('max_member_op');
	$avgLevel     = $params->get('average_lv');
	$avgLevelOp   = $params->get('average_lv_op');
	
	if ($guildName) {
		$sqlpartial .= "AND (guild.name LIKE ? OR guild.name = ?) ";
		$bind[]      = "%$guildName%";
		$bind[]      = $guildName;
	}
	
	if ($charID) {
		$sqlpartial .= "AND guild.char_id = ? ";
		$bind[]      = $charID;
	}
	
	if ($charName) {
		$sqlpartial .= "AND (guild.master LIKE ? OR guild.master = ?) ";
		$bind[]      = "%$charName%";
		$bind[]      = $charName;
	}
	
	if (in_array($guildLevelOp, $opValues) && trim($guildLevel) != '') {
		$op          = $opMapping[$guildLevelOp];
		$sqlpartial .= "AND guild.guild_lv $op ? ";
		$bind[]      = $guildLevel;
	}
	
	if (in_array($connectMemOp, $opValues) && trim($connectMem) != '') {
		$op          = $opMapping[$connectMemOp];
		$sqlpartial .= "AND guild.connect_member $op ? ";
		$bind[]      = $connectMem;
	}
	
	if (in_array($maxMemOp, $opValues) && trim($maxMem) != '') {
		$op          = $opMapping[$maxMemOp];
		$sqlpartial .= "AND guild.max_member $op ? ";
		$bind[]      = $maxMem;
	}
	
	if (in_array($avgLevelOp, $opValues) && trim($avgLevel) != '') {
		$op          = $opMapping[$avgLevelOp];
		$sqlpartial .= "AND guild.average_lv $op ? ";
		$bind[]      = $avgLevel;
	}
}

$sql  = "SELECT COUNT(guild.guild_id) AS total FROM {$server->charMapDatabase}.guild $sqlpartial";
$sth  = $server->connection->getStatement($sql);

$sth->execute($bind);
$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'guild.guild_id' => 'asc', 'guildName', 'charID', 'charName', 'guildLevel',
	'connectMem', 'maxMem', 'avgLevel'
));

$col  = "guild.guild_id, guild.name AS guildName, guild.char_id AS charID, `char`.name AS charName, ";
$col .= "guild.guild_lv AS guildLevel, guild.connect_member AS connectMem, guild.max_member AS maxMem, ";
$col .= "guild.average_lv AS avgLevel, guild.emblem_id as emblem ";
	
$sql  = "SELECT $col FROM {$server->charMapDatabase}.`guild` $sqlpartial";
$sql  = $paginator->getSQL($sql);
$sth  = $server->connection->getStatement($sql);

$sth->execute($bind);

$guilds     = $sth->fetchAll();
$authorized = $auth->actionAllowed('guild', 'view') && $auth->allowedToViewGuild;

if ($guilds && count($guilds) === 1 && $authorized && Flux::config('SingleMatchRedirect')) {
	$this->redirect($this->url('guild', 'view', array('id' => $guilds[0]->guild_id)));
}

?>
