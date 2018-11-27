<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Re-Install Database Schemas';

if (count($_POST) && $params->get('reinstall')) {
	$loginDbFiles   = glob(FLUX_DATA_DIR.'/logs/schemas/logindb/*/*.txt');
	$charMapDbFiles = glob(FLUX_DATA_DIR.'/logs/schemas/charmapdb/*/*.txt');
	
	foreach (array($loginDbFiles, $charMapDbFiles) as $dbDir) {
		if ($dbDir) {
			foreach ($dbDir as $file) {
				unlink($file);
			}
			// Attempt to unlink the directory, but let's not display an error if
			// there are still files in it.
			@rmdir($dbDir);
		}
	}
	
	$this->redirect();
}
?>
