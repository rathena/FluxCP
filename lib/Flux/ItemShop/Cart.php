<?php
require_once 'Flux/Error.php';

class Flux_ItemShop_Cart {
	/**
	 *
	 */
	public $account;
	
	/**
	 *
	 */
	private $cart = array();
	
	public function setAccount(Flux_DataObject $account)
	{
		$this->account = $account;
		return $account;
	}
	
	public function requiresAccount()
	{
		if (!$this->account) {
			throw new Flux_Error('Account is required to use the shopping cart.');
		}
	}
	
	public function add(Flux_DataObject $item)
	{
		$this->cart[] = $item;
		return $item;
	}
	
	public function delete(Flux_DataObject $item, $deleteAll = false)
	{
		$deleted = array();
		
		foreach ($this->cart as $cartItem) {
			if ($cartItem == $item) {
				if ($deleteAll) {
					$deleted[] = $cartItem;
				}
				else {
					return $cartItem;
				}
			}
		}
		
		if ($deleted) {
			return $deleted;
		}
		else {
			return false;
		}
	}
	
	public function deleteAll(Flux_DataObject $item)
	{
		return $this->delete($item, true);
	}
	
	public function clear()
	{
		$itemCount  = count($this->cart);
		$this->cart = array();
		return $itemCount;
	}
	
	public function buy(Flux_ItemShop $fromShop)
	{
		if (!$this->hasFunds()) {
			return false;
		}
		
		$successful = array();
		
		foreach ($this->cart as $cartItem) {
			$successful[] = array(
				'item'     => $cartItem,
				'name'     => $cartItem->shop_item_name,
				'cost'     => $cartItem->shop_item_cost,
				'quantity' => $cartItem->shop_item_qty,
				'success'  => $fromShop->buy($cartItem->shop_item_id)
			);
		}
		
		$this->clear();
		return $successful;
	}
	
	public function getCartItems()
	{
		return $this->cart;
	}
	
	public function getCartItemNames()
	{
		$names = array();
		foreach ($this->cart as $cartItem) {
			$names[] = $cartItem->shop_item_name;
		}
		return $names;
	}
	
	public function getTotal()
	{
		$total = 0;
		foreach ($this->cart as $cartItem) {
			$total += $cartItem->shop_item_cost;
		}
		return $total;
	}
	
	public function hasFunds()
	{
		$this->requiresAccount();
		$creditsAvailable = $this->account->balance;
		$creditsNeeded    = $this->getTotal();
		
		if ($creditsAvailable < $creditsNeeded) {
			return false;
		}
		else {
			return true;
		}
	}
	
	/**
	 * Check if the cart is empty.
	 */
	public function isEmpty()
	{
		$empty = !count($this->cart);
		return $empty;
	}
	
	public function deleteByItemNum($num)
	{
		if (!is_array($num)) {
			$num = array((int)$num);
		}
		else {
			$num = array_map('intval', $num);
		}
		
		$nDeleted = 0;
		
		foreach ($num as $n) {
			if (array_key_exists($n, $this->cart)) {
				unset($this->cart[$n]);
				$nDeleted += 1;
			}
		}
		
		return $nDeleted;
	}
}
?>
