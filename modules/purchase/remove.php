<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$num = $params->get('num');
if (!is_null($num)) {
	if ($num instanceOf Flux_Config) {
		$num = $num->toArray();
	}
	
	$nRemoved = $server->cart->deleteByItemNum($num);
	if ($nRemoved) {
		if (!$server->cart->isEmpty()) {
			$session->setMessageData("Removed $nRemoved item(s) from your cart.");
			$this->redirect($this->url('purchase', 'cart'));
		}
		else {
			$session->setMessageData("Removed $nRemoved item(s) from your cart. Your cart is now empty.");
		}
	}
	else {
		$session->setMessageData("There were no items to remove from your cart.");
	}
	
	$this->redirect($this->url('purchase'));
}

$session->setMessageData('No items were removed from your cart because none were selected.');
$this->redirect($this->url('purchase', 'cart'));
?>
