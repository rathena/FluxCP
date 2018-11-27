<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

if (!$auth->allowedToManageCashShop) {
	$this->deny();
}

require_once 'Flux/CashShop.php';

$shop       = new Flux_CashShop($server);
$shopItemID = $params->get('id');
$deleted    = $shopItemID ? $shop->delete($shopItemID) : false;

if ($deleted) {
	$session->setMessageData('Item successfully deleted from the CashShop. You will need to reload your itemdb for this to take effect in-game.');
	$this->redirect($this->url('cashshop'));
}
?>
