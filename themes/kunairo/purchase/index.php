<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Tienda</h2>
<p>Los items de esta tienda se compran con <span class="keyword">cr&eacute;ditos de donaci&oacute;n</span>, no con dinero real. Los cr&eacute;ditos se obtienen al <a href="<?php echo $this->url('donate') ?>">hacer una donaci&oacute;n</a>, ayudando a cubrir los costos del servidor.</p>
<h2><span class="shop-server-name"><?php echo htmlspecialchars($server->serverName) ?></span> &mdash; Tienda de Items</h2>
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
<h3>Categor&iacute;a: <?php echo htmlspecialchars($categoryName) ?></h3>
<?php endif ?>
<?php if ($items): ?>
<?php if ($session->isLoggedIn()): ?>
	<?php if ($cartItems=$server->cart->getCartItemNames()): ?><p class="cart-items-text">Items en tu carrito: <span class="cart-item-name"><?php echo implode('</span>, <span class="cart-item-name">', array_map('htmlspecialchars', $cartItems)) ?></span>.</p><?php endif ?>
	<p class="cart-info-text">Tienes <span class="cart-item-count"><?php echo number_format(count($cartItems)) ?></span> item(s) en tu carrito.</p>
	<p class="cart-total-text">Tu subtotal actual es <span class="cart-sub-total"><?php echo number_format($server->cart->getTotal()) ?></span> cr&eacute;dito(s).</p>
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
				<?php echo $this->linkToItem($item->shop_item_nameid, 'Ver item') ?>
				<?php endif ?>
				<?php if ($auth->allowedToEditShopItem): ?>
				/ <a href="<?php echo $this->url('itemshop', 'edit', array('id' => $item->shop_item_id)) ?>">Modificar</a>
				<?php endif ?>
				<?php if ($auth->allowedToDeleteShopItem): ?>
				/ <a href="<?php echo $this->url('itemshop', 'delete', array('id' => $item->shop_item_id)) ?>"
					onclick="return confirm('&iquest;Est&aacute;s seguro de que quieres eliminar este item de la tienda?')">Eliminar</a>
				<?php endif ?>
			</p>
		</td>
		<td class="shop-item-cost-qty">
			<p><span class="cost"><?php echo number_format($item->shop_item_cost) ?></span> cr&eacute;ditos</p>
			<p class="shop-item-action">
				<?php if ($auth->actionAllowed('purchase', 'add')): ?>
				<a href="<?php echo $this->url('purchase', 'add', array('id' => $item->shop_item_id)) ?>"><strong>Agregar al carrito</strong></a>
				<?php endif ?>
			</p>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>No hay items a la venta actualmente.</p>
<?php endif ?>
