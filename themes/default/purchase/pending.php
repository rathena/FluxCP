<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Pending Redemption</h2>
<?php if ($items): ?>
<p>You have <?php echo number_format($total) ?> item(s) pending redemption.</p>
<table class="vertical-table">
	<tr>
		<th>Item Name</th>
		<th>Quantity</th>
		<th>Cost</th>
		<th>Balance (Before)</th>
		<th>Balance (After)</th>
		<th>Purchase Date</th>
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
				<span class="not-applicable">Unknown</span>
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
<p>You currently have no items pending redemption.
	If you would like to make a purchase, please go to the <a href="<?php echo $this->url('purchase') ?>">shop</a>.</p>
<?php endif ?>
