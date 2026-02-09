<?php
if (!defined('FLUX_ROOT')) exit;

require_once 'functions/rrmdir.php';

$title = 'Re-Install Database Schemas';

if (count($_POST) && $params->get('reinstall')) {
	$inputPassword  = $params->get('installer_password');
	$actualPassword = Flux::config('InstallerPassword');

	if ($inputPassword != $actualPassword) {
		$errorMessage = 'Incorrect password.';
		$this->redirect($this->url('install', 'reinstall', array('errorMessage' => $errorMessage)));
		exit();
	}

	$session->setInstallerAuthData(true);

	$loginDbFolder   = FLUX_DATA_DIR . '/logs/schemas/logindb';
	$charMapDbFolder = FLUX_DATA_DIR . '/logs/schemas/charmapdb';

	rrmdir($loginDbFolder);
	rrmdir($charMapDbFolder);

	$this->redirect($this->url('install'));
}
?>
