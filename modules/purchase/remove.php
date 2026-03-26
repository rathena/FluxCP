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
			$session->setMessageData("Se eliminaron $nRemoved item(s) de tu carrito.");
			$this->redirect($this->url('purchase', 'cart'));
		}
		else {
			$session->setMessageData("Se eliminaron $nRemoved item(s) de tu carrito. Tu carrito ahora está vacío.");
		}
	}
	else {
		$session->setMessageData("No había items para eliminar de tu carrito.");
	}

	$this->redirect($this->url('purchase'));
}

$session->setMessageData('No se eliminaron items porque no seleccionaste ninguno.');
$this->redirect($this->url('purchase', 'cart'));
?>
