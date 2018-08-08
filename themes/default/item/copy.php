<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Duplicate Item</h2>
<?php if ($item): ?>
<?php if (!empty($errorMessage)): ?>
<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php else: ?>
<p>Here you can copy an item into <em>item_db2</em> with a new item ID.</p>
<?php endif ?>
<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
	<input type="hidden" name="copyitem" value="1" />
	<table class="generic-form-table">
		<tr>
			<th><label>Item Name (Item ID)</label></th>
			<td>
				<p>
					<strong><?php echo htmlspecialchars($item->name_japanese) ?></strong>
					<?php if ($auth->actionAllowed('item', 'view')): ?>
						(<a href="<?php echo $this->url('item', 'view', array('id' => $itemID)) ?>"><?php echo htmlspecialchars($itemID) ?></a>)
					<?php else: ?>
						(<?php echo htmlspecialchars($itemID) ?>)
					<?php endif ?>
				</p>
				
			</td>
			<td></td>
		</tr>
		<tr>
			<th><label for="new_item_id">Duplicate Item ID</label></th>
			<td><input type="text" name="new_item_id" id="new_item_id" value="" /></td>
			<td><p>Specify the new item ID you would like for the duplicate item.</p></td>
		</tr>
		<tr>
			<td colspan="2" align="right"><input type="submit" value="Duplicate Item" /></td>
			<td></td>
		</tr>
	</table>
</form>
<?php else: ?>
<p>No such item found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
