<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

if ($server->cart->isEmpty()) {
	$session->setMessageData('Your cart is currently empty.');
	$this->redirect($this->url('purchase'));
}

$title = 'Shopping Cart';

require_once 'Flux/ItemShop.php';
$items = $server->cart->getCartItems();
?>
