<?php
if (!defined('FLUX_ROOT')) exit;

include 'status.php';
$dom  = new DomDocument('1.0', 'utf-8');
$root = $dom->createElement('ServerStatus'); // Root element.

foreach ($serverStatus as $privServerName => $gameServers) {
	$group  = $dom->createElement('Group');
	$name   = $dom->createAttribute('name');
	$name->nodeValue = $privServerName;
	
	// Append server name element.
	$group->appendChild($name);
	
	foreach ($gameServers as $serverName => $gameServer) {
		$serv = $dom->createElement('Server');
		$name = $dom->createAttribute('name');
		$name->nodeValue = $serverName;
		
		$serv->appendChild($name);
		
		$lserv  = $dom->createAttribute('loginServer');
		$cserv  = $dom->createAttribute('charServer');
		$mserv  = $dom->createAttribute('mapServer');
		$online = $dom->createAttribute('playersOnline');
		
		$lserv->nodeValue  = (int)$gameServer['loginServerUp'];
		$cserv->nodeValue  = (int)$gameServer['charServerUp'];
		$mserv->nodeValue  = (int)$gameServer['mapServerUp'];
		$online->nodeValue = (int)$gameServer['playersOnline'];
		
		$serv->appendChild($lserv);
		$serv->appendChild($cserv);
		$serv->appendChild($mserv);
		$serv->appendChild($online);
		$group->appendChild($serv);
	}
	
	$root->appendChild($group);
}

$dom->appendChild($root);

header('Content-Type: text/xml');
echo $dom->saveXML();
exit;
?>
