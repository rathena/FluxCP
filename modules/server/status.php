<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('ServerStatusTitle');
$cache = FLUX_DATA_DIR.'/tmp/ServerStatus.cache';

if (file_exists($cache) && (time() - filemtime($cache)) < (Flux::config('ServerStatusCache') * 60)) {
	$serverStatus = unserialize(file_get_contents($cache));
}
else {
	$serverStatus = array();
	foreach (Flux::$loginAthenaGroupRegistry as $groupName => $loginAthenaGroup) {
		if (!array_key_exists($groupName, $serverStatus)) {
			$serverStatus[$groupName] = array();
		}

		$loginServerUp = $loginAthenaGroup->loginServer->isUp();

		foreach ($loginAthenaGroup->athenaServers as $athenaServer) {
			$serverName = $athenaServer->serverName;

			$sql = "SELECT COUNT(char_id) AS players_online FROM {$athenaServer->charMapDatabase}.char WHERE online > 0";
			$sth = $loginAthenaGroup->connection->getStatement($sql);
			$sth->execute();
			$res = $sth->fetch();

			$serverStatus[$groupName][$serverName] = array(
				'loginServerUp' => $loginServerUp,
				 'charServerUp' => $athenaServer->charServer->isUp(),
				  'mapServerUp' => $athenaServer->mapServer->isUp(),
				'playersOnline' => intval($res ? $res->players_online : 0)
			);
		}
	}
	
	$fp = fopen($cache, 'w');
	if (is_resource($fp)) {
		fwrite($fp, serialize($serverStatus));
		fclose($fp);
	}
}
?>