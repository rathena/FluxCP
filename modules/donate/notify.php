<?php
if (!defined('FLUX_ROOT')) exit;

require_once 'Flux/PaymentNotifyRequest.php';
if (count($_POST)) {
	$request = new Flux_PaymentNotifyRequest($_POST);
	$request->process();
}
exit;
?>
