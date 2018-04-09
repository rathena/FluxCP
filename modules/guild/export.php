<?php
if (!defined('FLUX_ROOT')) exit;

if (!extension_loaded('zip')) {
	throw new Flux_Error('The `zip` extension needs to be loaded for this feature to work.  Please consult the PHP manual for instructions.');
}

$this->loginRequired();

$title = 'Export Guild Emblems';

require_once 'Flux/EmblemExporter.php';
$exporter = new Flux_EmblemExporter($session->loginAthenaGroup);

$serverNames = $session->getAthenaServerNames();

if (count($_POST)) {
	$serverArr = $params->get('server');
	
	if ($serverArr instanceOf Flux_Config) {
		$array = $serverArr->toArray();
		
		foreach ($array as $serv) {
			$athenaServer = $session->getAthenaServer($serv);
			
			if ($athenaServer) {
				$exporter->addAthenaServer($athenaServer);
			}
		}
		
		$exporter->exportArchive();
	}
	else {
		$session->setMessageData('You must select a server first.');
	}
}
?>
