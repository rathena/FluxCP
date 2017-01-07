<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('ServerInfoTitle');
$info  = array(
		'accounts'   => 0,
		'characters' => 0,
		'guilds'     => 0,
		'parties'    => 0,
		'zeny'       => 0,
		'classes'    => array()
);

// Accounts.
$sql = "SELECT COUNT(account_id) AS total FROM {$server->loginDatabase}.login WHERE sex != 'S' ";
if (Flux::config('HideTempBannedStats')) {
	$sql .= "AND unban_time <= UNIX_TIMESTAMP() ";
}
if (Flux::config('HidePermBannedStats')) {
	if (Flux::config('HideTempBannedStats')) {
		$sql .= "AND state != 5 ";
	} else {
		$sql .= "AND state != 5 ";
	}
}
$sth = $server->connection->getStatement($sql);
$sth->execute();
$info['accounts'] += $sth->fetch()->total;

// Characters.
$sql = "SELECT COUNT(`char`.char_id) AS total FROM {$server->charMapDatabase}.`char` ";
if (Flux::config('HideTempBannedStats')) {
	$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
	$sql .= "WHERE login.unban_time <= UNIX_TIMESTAMP()";
}
if (Flux::config('HidePermBannedStats')) {
	if (Flux::config('HideTempBannedStats')) {
		$sql .= " AND login.state != 5";
	} else {
		$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
		$sql .= "WHERE login.state != 5";
	}
}
$sth = $server->connection->getStatement($sql);
$sth->execute();
$info['characters'] += $sth->fetch()->total;

// Guilds.
$sql = "SELECT COUNT(guild_id) AS total FROM {$server->charMapDatabase}.guild";
$sth = $server->connection->getStatement($sql);
$sth->execute();
$info['guilds'] += $sth->fetch()->total;

// Parties.
$sql = "SELECT COUNT(party_id) AS total FROM {$server->charMapDatabase}.party";
$sth = $server->connection->getStatement($sql);
$sth->execute();
$info['parties'] += $sth->fetch()->total;

// Zeny.
$bind = array();
$sql  = "SELECT SUM(`char`.zeny) AS total FROM {$server->charMapDatabase}.`char` ";
if ($hideGroupLevel=Flux::config('InfoHideZenyGroupLevel')) {
	$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
	
	$groups = AccountLevel::getGroupID($hideGroupLevel, '<');
	if(!empty($groups)) {
		$ids   = implode(', ', array_fill(0, count($groups), '?'));
		$sql  .= "WHERE login.group_id IN ($ids) ";
		$bind  = array_merge($bind, $groups);
	}
}
if (Flux::config('HideTempBannedStats')) {
	if ($hideGroupLevel) {
		$sql .= " AND unban_time <= UNIX_TIMESTAMP()";
	} else {
		$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
		$sql .= "WHERE unban_time <= UNIX_TIMESTAMP()";
	}
}
if (Flux::config('HidePermBannedStats')) {
	if ($hideGroupLevel || Flux::config('HideTempBannedStats')) {
		$sql .= " AND state != 5";
	} else {
		$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
		$sql .= "WHERE state != 5";
	}
}

$sth = $server->connection->getStatement($sql);
$sth->execute($hideGroupLevel ? $bind : array());
$info['zeny'] += $sth->fetch()->total;

// Job classes.
$sql = "SELECT `char`.class, COUNT(`char`.class) AS total FROM {$server->charMapDatabase}.`char` ";
if (Flux::config('HideTempBannedStats')) {
	$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
	$sql .= "WHERE login.unban_time <= UNIX_TIMESTAMP() ";
}
if (Flux::config('HidePermBannedStats')) {
	if (Flux::config('HideTempBannedStats')) {
		$sql .= " AND login.state != 5 ";
	} else {
		$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
		$sql .= "WHERE login.state != 5 ";
	}
}
$sql .= "GROUP BY `char`.class";
$sth = $server->connection->getStatement($sql);
$sth->execute();

$classes = $sth->fetchAll();
if ($classes) {
	foreach ($classes as $class) {
		$classnum = (int)$class->class;
		$info['classes'][Flux::config("JobClasses.$classnum")] = $class->total;
	}
}

if (Flux::config('SortJobsByAmount')) {
	arsort($info['classes']);
}
?>
