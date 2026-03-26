<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

require_once 'Flux/ItemShop.php';

if ($server->cart && $server->cart->clear()) {
	$session->setMessageData("Tu carrito ha sido vaciado.");
}
else {
	$session->setMessageData("No se pudo vaciar tu carrito, puede que ya esté vacío.");
}

$this->redirect($this->url('purchase'));
?>
