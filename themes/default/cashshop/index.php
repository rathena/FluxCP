<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>CashShop</h2>
<?php if (!empty($errorMessage)): ?>
<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<table class="vertical-table">
	<tr>
		<th>Tab</th>
		<th>Item ID</th>
		<th>Item Name</th>
		<th>Price</th>
		<th>Options</th>
	</tr>
	<?php foreach($items as $item):?>
	<tr>
		<td><?php echo $tabs[$item->tab] ?></td>
		<td><?php echo $this->linkToItem($item->item_id, $item->item_id) ?></td>
		<td><?php echo $this->linkToItem($item->item_id, htmlspecialchars($item->item_name)) ?></td>
		<td><?php echo $item->price ?></td>
		<td><a href="<?php echo $this->url('cashshop', 'edit', array('id' => $item->item_id)) ?>">Edit</a> | <a href="<?php echo $this->url('cashshop', 'delete', array('id' => $item->item_id)) ?>">Remove</a></td>
	</tr>	
	<?php endforeach ?>
</table>
