<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'WoE Hours';

$col  = "sday.value AS sday, eday.value AS eday, ";
$col .= "stime.value AS stime, etime.value AS etime";

$dayNames = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
$woeTimes = array();
foreach ($session->loginAthenaGroup->athenaServers as $athenaServer) {
	$sql  = "SELECT $col FROM {$athenaServer->charMapDatabase}.mapreg AS sday ";
	$sql .= "JOIN {$athenaServer->charMapDatabase}.mapreg AS eday ON (eday.varname = '\$eday' AND eday.index  = sday.index) ";
	$sql .= "JOIN {$athenaServer->charMapDatabase}.mapreg AS stime ON (stime.varname = '\$woetime' AND stime.index = sday.index) ";
	$sql .= "JOIN {$athenaServer->charMapDatabase}.mapreg AS etime ON (etime.varname = '\$woetime2' AND etime.index = sday.index) ";
	$sql .= "WHERE sday.varname = '\$sday' ORDER BY sday.value ASC";
	$sth  = $athenaServer->connection->getStatement($sql);
	$sth->execute();
	
	$times = $sth->fetchAll();
	
	if ($times) {
		$woeTimes[$athenaServer->serverName] = array();
		foreach ($times as $time) {
			$woeTimes[$athenaServer->serverName][] = array(
				'startingDay'  => $dayNames[$time->sday],
				'startingHour' => sprintf('%02d:00', $time->stime),
				'endingDay'    => $dayNames[$time->eday],
				'endingHour'   => sprintf('%02d:00', $time->etime)
			);
		}
	}
}
?>
