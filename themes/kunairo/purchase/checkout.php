<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Pagar</h2>
<p>El proceso de compra es simple. Cuando termines, podr&aacute;s canjear tus items en el juego a trav&eacute;s del <span class="keyword">NPC de Canjeo</span>.</p>

<h3>Informaci&oacute;n de compra</h3>
<p class="cart-total-text">Tu subtotal actual es <span class="cart-sub-total"><?php echo number_format($total=$server->cart->getTotal()) ?></span> cr&eacute;dito(s).</p>
<p class="checkout-info-text">Tu saldo restante despu&eacute;s de esta compra ser&aacute; <span class="remaining-balance"><?php echo number_format($session->account->balance - $total) ?></span> cr&eacute;dito(s).</p>
<p>Revisa la informaci&oacute;n de los items y haz click en "Comprar items" para confirmar.</p>
<p class="important">Nota: Estos items son para canjear en el servidor <span class="server-name"><?php echo htmlspecialchars($server->serverName) ?></span> SOLAMENTE.</p>
<p>
	<form action="<?php echo $this->url ?>" method="post">
		<?php echo $this->moduleActionFormInputs($params->get('module'), 'checkout') ?>
		<input type="hidden" name="process" value="1" />
		<button type="submit" onclick="return confirm('&iquest;Est&aacute;s seguro de que deseas comprar los items listados abajo?')">
			<strong>Comprar items</strong>
		</button>
	</form>
</p>

<h3>Items en tu carrito:</h3>
<p class="cart-info-text">Tienes <span class="cart-item-count"><?php echo number_format(count($items)) ?></span> item(s) en tu carrito.</p>
<table class="vertical-table cart">
	<?php foreach ($items as $item): ?>
	<tr>
		<td>
			<h4>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($item->shop_item_nameid, $item->shop_item_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($item->shop_item_nameid) ?>
				<?php endif ?>
			</h4>
			<?php if ($item->shop_item_qty > 1): ?>
			<p class="shop-item-qty">Cantidad: <span class="qty"><?php echo number_format($item->shop_item_qty) ?></span></p>
			<?php endif ?>
			<p class="shop-item-cost"><span class="cost"><?php echo number_format($item->shop_item_cost) ?></span> cr&eacute;ditos</p>
			<p><?php echo nl2br(htmlspecialchars($item->shop_item_info)) ?></p>
		</td>
	</tr>
	<?php endforeach ?>
</table>
