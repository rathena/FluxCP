<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'List Logins';

$sqlpartial = '';
$bind       = array();

$dateAfter  = $params->get('log_after_date');
$dateBefore = $params->get('log_before_date');
$ipAddress  = trim($params->get('ip'));
$username   = trim($params->get('user'));
$logMessage = trim($params->get('log'));
$response   = trim($params->get('rcode'));

if ($dateAfter) {
	$sqlpartial .= 'AND time >= :dateAfter ';
	$bind[':dateAfter'] = [ $dateAfter, PDO::PARAM_STR ];
}

if ($dateBefore) {
	$sqlpartial .= 'AND time <= :dateBefore ';
	$bind[':dateBefore'] = [ $dateBefore, PDO::PARAM_STR ];
}

if ($ipAddress) {
	$sqlpartial .= 'AND ip LIKE :ipAddress ';
	$bind[':ipAddress'] = [ "%$ipAddress%", PDO::PARAM_STR ];
}

if ($username) {
	$sqlpartial .= 'AND user LIKE :username ';
	$bind[':username'] = [ "%$username%", PDO::PARAM_STR ];
}

if ($logMessage) {
	$sqlpartial .= 'AND log LIKE :logmes ';
	$bind[':logmes'] = [ "%$logMessage%", PDO::PARAM_STR ];
}

if ($response) {
	$sqlpartial .= 'AND rcode = :rcode ';
	$bind[':rcode'] = [ $response, PDO::PARAM_INT ];
}

$sql = "SELECT COUNT(time) AS total FROM {$server->logsDatabase}.loginlog WHERE 1=1 $sqlpartial";
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($bind, true);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'time' => 'desc', 'ip', 'user', 'log', 'rcode'
));

$sql = "SELECT time, ip, user, rcode, log FROM {$server->logsDatabase}.loginlog WHERE 1=1 $sqlpartial";
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($bind, true);

$logins = $sth->fetchAll();

if ($logins) {
	$noCase    = $server->loginServer->config->getNoCase();
	$usernames = array();
	foreach ($logins as $_tmplogin) {
		$usernames[] = $noCase ? strtolower($_tmplogin->user) : $_tmplogin->user;
	}

	$uid  = $noCase ? 'LOWER(userid)' : 'userid';
	$sql  = "SELECT $uid AS userid, account_id FROM {$server->loginDatabase}.login WHERE ";
	$sql .= "sex != 'S' AND group_id >= 0 AND $uid IN (".implode(',', array_fill(0, count($usernames), '?')).")";
	$sth  = $server->connection->getStatement($sql);
	$sth->execute($usernames, true);

	$data = $sth->fetchAll();
	if ($data) {
		$accounts = array();
		foreach ($data as $row) {
			$userid = $noCase ? strtolower($row->userid) : $row->userid;
			$accounts[$userid] = $row->account_id;
		}

		foreach ($logins as $_tmplogin) {
			$userid = $noCase ? strtolower($_tmplogin->user) : $_tmplogin->user;
			if (array_key_exists($userid, $accounts)) {
				$_tmplogin->account_id = $accounts[$userid];
			}
		}
	}
}
