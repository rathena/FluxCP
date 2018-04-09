<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'List IP Ban History';

$ipBanTable = Flux::config('FluxTables.IpBanTable');
$sqlpartial = "WHERE 1=1 ";
$bind       = array();

$ipAddress   = trim($params->get('ip'));
$bannedBy    = trim($params->get('banned_by'));
$banType     = $params->get('ban_type');
$banDate     = $params->get('ban_date');
$banUntil    = $params->get('ban_until_date');

if ($ipAddress) {
	$sqlpartial .= 'AND i.ip_address LIKE ? ';
	$bind[]      = "%$ipAddress%";
}

if ($bannedBy) {
	if (preg_match('/^\d+$/', $bannedBy)) {
		$sqlpartial .= 'AND i.banned_by = ? ';
		$bind[]      = $bannedBy;
	}
	else {
		$sqlpartial .= 'AND (l.userid LIKE ? OR l.userid = ?) ';
		$bind[]      = "%$bannedBy%";
		$bind[]      = $bannedBy;
	}
}

if ($banType) {
	if ($banType == 'unban') {
		$sqlpartial .= 'AND i.ban_type = 0 ';
	}
	elseif ($banType == 'ban') {
		$sqlpartial .= 'AND i.ban_type = 1 ';
	}
}

if ($banDate) {
	$sqlpartial .= 'AND i.ban_date = ? ';
	$bind[]      = $banDate;
}

if ($banUntil) {
	$sqlpartial .= 'AND i.ban_until = ? ';
	$bind[]      = $banUntil;
}

$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$ipBanTable $sqlpartial";
$sth = $server->connection->getStatement($sql);
$sth->execute($bind);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'ip', 'banned_by', 'ban_type', 'ban_date' => 'desc', 'ban_until'
));

$sql  = "SELECT i.ip_address, i.banned_by, i.ban_type, i.ban_until, i.ban_date, i.ban_reason, ";
$sql .= "l.userid FROM {$server->loginDatabase}.$ipBanTable AS i ";
$sql .= "LEFT JOIN {$server->loginDatabase}.login AS l ON l.account_id = i.banned_by $sqlpartial";
$sql  = $paginator->getSQL($sql);
$sth  = $server->connection->getStatement($sql);
$sth->execute($bind);

$ipbans = $sth->fetchAll();
?>
