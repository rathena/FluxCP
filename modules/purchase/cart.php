<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

if ($server->cart->isEmpty()) {
	$session->setMessageData('Tu carrito está vacío.');
	$this->redirect($this->url('purchase'));
}

$title = 'Carrito de compras';

require_once 'Flux/ItemShop.php';
$items = $server->cart->getCartItems();
?>
