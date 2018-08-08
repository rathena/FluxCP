<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$shopItemID = $params->get('id');

if (!$shopItemID) {
	$this->deny();
}

require_once 'Flux/ItemShop.php';

$shop = new Flux_ItemShop($server);
$shop->deleteShopItemImage($shopItemID);

$session->setMessageData('Shop item image has been deleted.');
$this->redirect($this->referer);
?>
