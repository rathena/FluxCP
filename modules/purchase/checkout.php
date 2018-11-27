<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'Checkout Area';

if ($server->cart->isEmpty()) {
	$session->setMessageData('Your cart is currently empty.');
	$this->redirect($this->url('purchase'));
}
elseif (!$server->cart->hasFunds()) {
	$session->setMessageData('You do not have sufficient funds to make this purchase!');
	$this->redirect($this->url('purchase'));
}

$items = $server->cart->getCartItems();

if (count($_POST) && $params->get('process')) {
	$redeemTable = Flux::config('FluxTables.RedemptionTable');
	$creditTable = Flux::config('FluxTables.CreditsTable');
	$deduct      = 0;
	
	$sql  = "INSERT INTO {$server->charMapDatabase}.$redeemTable ";
	$sql .= "(nameid, quantity, cost, account_id, char_id, redeemed, redemption_date, purchase_date, credits_before, credits_after) ";
	$sql .= "VALUES (?, ?, ?, ?, NULL, 0, NULL, NOW(), ?, ?)";
	$sth  = $server->connection->getStatement($sql);
	
	$balance = $session->account->balance;
	
	foreach ($items as $item) {
		$creditsAfter = $balance - $item->shop_item_cost;
		
		$res = $sth->execute(array(
			$item->shop_item_nameid,
			$item->shop_item_qty,
			$item->shop_item_cost,
			$session->account->account_id,
			$balance,
			$creditsAfter
		));
		
		if ($res) {
			$deduct  += $item->shop_item_cost;
			$balance -= $item->shop_item_cost;
		}
	}
	
	$session->loginServer->depositCredits($session->account->account_id, -$deduct);
	
	if ($res) {
		if (!$deduct) {
			$server->cart->clear();
			$session->setMessageData('Failed to purchase all of the items in your cart!');
		}
		elseif ($deduct != $server->cart->getTotal()) {
			$server->cart->clear();
			$session->setMessageData('Items have been purchased, however, some failed (your credits are still there.)');
		}
		else {
			$server->cart->clear();
			$session->setMessageData('Items have been purchased.  You may redeem them from the Redemption NPC.');
		}
	}
	else {
		$session->setMessageData('Purchase went bad, contact an admin!');
	}
	
	$this->redirect();
}
?>
