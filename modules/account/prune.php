<?php 
if (!defined('FLUX_ROOT')) exit;


if (!($password=$params->get('password')) || $password !== Flux::config('InstallerPassword')) {
	$this->deny();
}
else {
	Flux::pruneUnconfirmedAccounts();
	exit('DONE');
}
?>
