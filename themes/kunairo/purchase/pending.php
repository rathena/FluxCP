<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Pendientes de canjear</h2>
<?php if ($items): ?>
<p>Tienes <?php echo number_format($total) ?> item(s) pendientes de canjear.</p>
<table class="vertical-table">
	<tr>
		<th>Item</th>
		<th>Cantidad</th>
		<th>Costo</th>
		<th>Saldo (antes)</th>
		<th>Saldo (despu&eacute;s)</th>
		<th>Fecha de compra</th>
	</tr>
	<?php foreach ($items as $item): ?>
	<tr>
		<td align="right">
			<?php if ($item->item_name): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($item->nameid, $item->item_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($item->nameid) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable">Desconocido</span>
			<?php endif ?>
		</td>
		<td><?php echo number_format($item->quantity) ?></td>
		<td><?php echo number_format($item->cost) ?></td>
		<td><?php echo number_format($item->credits_before) ?></td>
		<td><?php echo number_format($item->credits_after) ?></td>
		<td><?php echo $this->formatDateTime($item->purchase_date) ?></td>
	</tr>
	<?php endforeach ?>
</table>
<?php else: ?>
<p>No tienes items pendientes de canjear.
	Si deseas hacer una compra, visita la <a href="<?php echo $this->url('purchase') ?>">tienda</a>.</p>
<?php endif ?>
