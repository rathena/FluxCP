<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Purchase</h2>
<p>Items in this shop are purchased using <span class="keyword">donation credits</span> and not real money.  Donation Credits are rewarded to players who <a href="<?php echo $this->url('donate') ?>">make a donation to our server</a>, helping us cover the costs of maintaining and running the server.</p>
<h2><span class="shop-server-name"><?php echo htmlspecialchars($server->serverName) ?></span> Item Shop</h2>
<p class="action">
	<a href="<?php echo $this->url('purchase', 'index') ?>"<?php if (is_null($category)) echo ' class="current-shop-category"' ?>>
		<?php echo htmlspecialchars(Flux::message('AllLabel')) ?> (<?php echo number_format($total) ?>)
	</a>
<?php foreach ($categories as $catID => $catName): ?>
	/
	<a href="<?php echo $this->url('purchase', 'index', array('category' => $catID)) ?>"<?php if (!is_null($category) && $category === (string)$catID) echo ' class="current-shop-category"' ?>>
		<?php echo htmlspecialchars($catName) ?> (<?php echo number_format($categoryCount[$catID]) ?>)
	</a>
<?php endforeach ?>
</p>
<?php if ($categoryName): ?>
<h3>Category: <?php echo htmlspecialchars($categoryName) ?></h3>
<?php endif ?>
<?php if ($items): ?>
<?php if ($session->isLoggedIn()): ?>
	<?php if ($cartItems=$server->cart->getCartItemNames()): ?><p class="cart-items-text">Items in your cart: <span class="cart-item-name"><?php echo implode('</span>, <span class="cart-item-name">', array_map('htmlspecialchars', $cartItems)) ?></span>.</p><?php endif ?>
	<p class="cart-info-text">You have <span class="cart-item-count"><?php echo number_format(count($cartItems)) ?></span> item(s) in your cart.</p>
	<p class="cart-total-text">Your current subtotal is <span class="cart-sub-total"><?php echo number_format($server->cart->getTotal()) ?></span> credit(s).</p>
<?php endif ?>
<?php echo $paginator->infoText() ?>
<table class="shop-table">
	<?php foreach ($items as $item): ?>
	<tr>
		<td class="shop-item-image">
		<?php if (($item->shop_item_use_existing && ($image=$this->itemImage($item->shop_item_nameid))) || ($image=$this->shopItemImage($item->shop_item_id))): ?>
			<img src="<?php echo $image ?>?nocache=<?php echo rand() ?>" />
		<?php endif ?>
		</td>
		<td>
			<h4 class="shop-item-name">
				<?php if ($item->shop_item_qty > 1): ?>
				<span class="qty"><?php echo number_format($item->shop_item_qty) ?>x</span>
				<?php endif ?>
				<?php echo $this->linkToItem($item->shop_item_nameid, $item->shop_item_name) ?>
			</h4>
			<p class="shop-item-info"><?php echo $item->shop_item_info ?></p>
			<p class="shop-item-action">
				<?php if ($auth->actionAllowed('item', 'view')): ?>
				<?php echo $this->linkToItem($item->shop_item_nameid, 'View Item') ?>
				<?php endif ?>
				<?php if ($auth->allowedToEditShopItem): ?>
				/ <a href="<?php echo $this->url('itemshop', 'edit', array('id' => $item->shop_item_id)) ?>">Modify</a>
				<?php endif ?>
				<?php if ($auth->allowedToDeleteShopItem): ?>
				/ <a href="<?php echo $this->url('itemshop', 'delete', array('id' => $item->shop_item_id)) ?>"
					onclick="return confirm('Are you sure you want to remove this item from the item shop?')">Delete</a>
				<?php endif ?>
			</p>
		</td>
		<td class="shop-item-cost-qty">
			<p><span class="cost"><?php echo number_format($item->shop_item_cost) ?></span> credits.</p>
			<p class="shop-item-action">
				<?php if ($auth->actionAllowed('purchase', 'add')): ?>
				<a href="<?php echo $this->url('purchase', 'add', array('id' => $item->shop_item_id)) ?>"><strong>Add to Cart</strong></a>
				<?php endif ?>
			</p>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>There are currently no items for sale.</p>
<?php endif ?>
