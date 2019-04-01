<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>View Cart</h2>
<p class="cart-info-text">You have <span class="cart-item-count"><?php echo number_format(count($items)) ?></span> item(s) in your cart.</p>
<p class="cart-total-text">Your current subtotal is <span class="cart-sub-total"><?php echo number_format($total=$server->cart->getTotal()) ?></span> credit(s).</p>
<br />
<p class="checkout-text"><a href="<?php echo $this->url('purchase', 'checkout') ?>">Proceed to Checkout Area</a></p>
<form action="<?php echo $this->url('purchase', 'remove') ?>" method="post">
	<table class="vertical-table cart">
		<?php foreach ($items as $num => $item): ?>
		<tr>
			<td class="shop-item-image">
			<?php if (($item->shop_item_use_existing && ($image=$this->itemImage($item->shop_item_nameid))) || ($image=$this->shopItemImage($item->shop_item_id))): ?>
				<img src="<?php echo $image ?>?nocache=<?php echo rand() ?>" />
			<?php endif ?>
			</td>
			<td>
				<h4>
					<label>
						<input type="checkbox" name="num[]" value="<?php echo $num ?>" />
						<?php echo htmlspecialchars($item->shop_item_name) ?>
					</label>
				</h4>
				<?php if ($item->shop_item_qty > 1): ?>
				<p class="shop-item-qty">Quantity: <span class="qty"><?php echo number_format($item->shop_item_qty) ?></span></p>
				<?php endif ?>
				<p class="shop-item-cost"><span class="cost"><?php echo number_format($item->shop_item_cost) ?></span> credits</p>
				<p class="shop-item-action">
					<?php if ($auth->actionAllowed('item', 'view')): ?>
						<?php echo $this->linkToItem($item->shop_item_nameid, 'View Item') ?> /
					<?php endif ?>
					<a href="<?php echo $this->url('purchase', 'remove', array('num' => $num)) ?>">Remove from Cart</a> /
					<a href="<?php echo $this->url('purchase', 'add', array('id' => $item->shop_item_id, 'cart' => true)) ?>">Add Another to Cart</a>
				</p>
				<p><?php echo nl2br(htmlspecialchars($item->shop_item_info)) ?></p>
			</td>
		</tr>
		<?php endforeach ?>
	</table>
	<p class="remove-from-cart">
		<input type="submit" value="Remove Selected Items from Cart" />
	</p>
</form>
<form action="<?php echo $this->url('purchase', 'clear') ?>" method="post">
	<p class="remove-from-cart">
		<input type="submit" value="Empty Out Your Cart" />
	</p>
</form>
