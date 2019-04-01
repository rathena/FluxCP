<?php

use rAthena\FluxCp\ItemShop;

if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$shopItemID = $params->get('id');

if (!$shopItemID) {
	$this->deny();
}

$shop = new ItemShop($server);
$shop->deleteShopItemImage($shopItemID);

$session->setMessageData('Shop item image has been deleted.');
$this->redirect($this->referer);
?>
