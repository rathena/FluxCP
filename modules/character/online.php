<?php
if (!defined('FLUX_ROOT')) exit;

$title = "Who's Online";

$charPrefsTable = Flux::config('FluxTables.CharacterPrefsTable');


$sqlpartial  = "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = ch.account_id ";
$sqlpartial .= "LEFT JOIN {$server->charMapDatabase}.guild ON guild.guild_id = ch.guild_id ";
if(Flux::config('EmblemUseWebservice'))
	$sqlpartial .= "LEFT JOIN {$server->charMapDatabase}.`guild_emblems` ON `guild_emblems`.guild_id = ch.guild_id ";	

if (!$auth->allowedToIgnoreHiddenPref) {
	$sqlpartial .= "LEFT JOIN {$server->charMapDatabase}.$charPrefsTable AS pref1 ON ";
	$sqlpartial .= "(pref1.account_id = ch.account_id AND pref1.char_id = ch.char_id AND pref1.name = 'HideFromWhosOnline') ";
}

$sqlpartial .= "LEFT JOIN {$server->charMapDatabase}.$charPrefsTable AS pref2 ON ";
$sqlpartial .= "(pref2.account_id = ch.account_id AND pref2.char_id = ch.char_id AND pref2.name = 'HideMapFromWhosOnline') ";
$sqlpartial .= "WHERE ch.online > 0 ";

if (!$auth->allowedToIgnoreHiddenPref) {
	$sqlpartial .= "AND (pref1.value IS NULL) ";
}

$bind = array();

if ($auth->allowedToSearchWhosOnline) {
	$charName  = $params->get('char_name');
	$charClass = $params->get('char_class');
	$guildName = $params->get('guild_name');

	if ($charName) {
		$sqlpartial .= "AND (ch.name LIKE ? OR ch.name = ?) ";
		$bind[]      = "%$charName%";
		$bind[]      = $charName;
	}

	if ($guildName) {
		$sqlpartial .= "AND (guild.name LIKE ? OR guild.name = ?) ";
		$bind[]      = "%$guildName%";
		$bind[]      = $guildName;
	}

	if ($charClass) {
		$className = preg_quote($charClass, '/');
		$classIDs  = preg_grep("/.*?$className.*?/i", Flux::config('JobClasses')->toArray());

		if (count($classIDs)) {
			$classIDs    = array_keys($classIDs);
			$sqlpartial .= "AND (";
			$partial     = '';

			foreach ($classIDs as $id) {
				$partial .= "ch.class = ? OR ";
				$bind[]   = $id;
			}

			$partial     = preg_replace('/\s*OR\s*$/', '', $partial);
			$sqlpartial .= "$partial) ";
		}
		else {
			$sqlpartial .= 'AND ch.class IS NULL ';
		}
	}
}

// Hide groups greater than or equal to
if (($hideGroupLevel=Flux::config('HideFromWhosOnline')) && !$auth->allowedToIgnoreHiddenPref2) {
	$groups = AccountLevel::getGroupID($hideGroupLevel, '<');

	if(!empty($groups)) {
		$ids = implode(', ', array_fill(0, count($groups), '?'));
		$sqlpartial .= "AND login.group_id IN ($ids) ";
		$bind = array_merge($bind, $groups);
	}
}

$sql  = "SELECT COUNT(ch.char_id) AS total FROM {$server->charMapDatabase}.`char` AS ch $sqlpartial";
$sth  = $server->connection->getStatement($sql);

$sth->execute($bind);

$sortable = array('char_name' => 'asc', 'base_level', 'job_level', 'guild_name');
if ($auth->allowedToViewOnlinePosition) {
	$sortable[] = 'last_map';
}

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns($sortable);

$sql  = "SELECT COUNT(ch.char_id) - {$paginator->total} AS total FROM {$server->charMapDatabase}.`char` AS ch ";
$sql .= "WHERE ch.online > 0";
$sth  = $server->connection->getStatement($sql);

$sth->execute();

// Number of hidden players (not including the ones hidden by the 'HideFromWhosOnline' app config).
$hiddenCount = (int)$sth->fetch()->total;

$col  = "ch.char_id, ch.name AS char_name, ch.class AS char_class, ch.base_level, ch.job_level, ";
$col .= "guild.name AS guild_name, guild.guild_id, ch.last_map, pref2.value AS hidemap, ";
if(Flux::config('EmblemUseWebservice'))
	$col .= "guild_emblems.file_data as guild_emblem_len ";
else
	$col .= "guild.emblem_len as guild_emblem_len ";

$sql  = $paginator->getSQL("SELECT $col FROM {$server->charMapDatabase}.`char` AS ch $sqlpartial");
$sth  = $server->connection->getStatement($sql);

$sth->execute($bind);

$chars = $sth->fetchAll();

?>
