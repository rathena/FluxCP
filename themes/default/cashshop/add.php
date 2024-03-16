<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>CashShop</h2>
<h3>Add Item to the CashShop</h3>
<?php if ($item): ?>
<?php if (!empty($errorMessage)): ?>
<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<form action="<?php echo $this->urlWithQs ?>" method="post" enctype="multipart/form-data">
<table class="vertical-table">
	<tr>
		<th>Item ID</th>
		<td><?php echo $this->linkToItem($item->item_id, $item->item_id) ?></td>
	</tr>
	<tr>
		<th>Name</th>
		<td><?php echo htmlspecialchars($item->item_name) ?></td>
	</tr>
	<tr>
		<th><label for="tab">In-Game Tab</label></th>
		<td>
			<select name="tab" id="tab">
				<?php foreach ($categories as $categoryID => $cat): ?>
					<option value="<?php echo (int)$categoryID ?>"<?php if ($category === (string)$categoryID) echo ' selected="selected"' ?>><?php echo htmlspecialchars($cat) ?></option>
				<?php endforeach ?>
			</select>
		</td>
	</tr>
	<tr>
		<th><label for="price">CashPoints Cost</label></th>
		<td><input type="text" class="short" name="price" id="price" value="<?php echo htmlspecialchars($params->get('price') ?: '') ?>" /></td>
	</tr>
	<tr>
		<td colspan="2" align="right">
			<input type="submit" value="Add" />
		</td>
	</tr>
</table>
</form>
<?php else: ?>
<p>Cannot add an unknown item to the item shop. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
