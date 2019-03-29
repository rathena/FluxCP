<?php

use rAthena\FluxCp\PaymentNotifyRequest;

if (!defined('FLUX_ROOT')) exit;

if (count($_POST)) {
	$request = new PaymentNotifyRequest($_POST);
	$request->process();
}
exit;
?>
