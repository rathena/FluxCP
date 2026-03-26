<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Items</h2>
<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="item_id">Item ID:</label>
		<input type="text" name="item_id" id="item_id" value="<?php echo htmlspecialchars($params->get('item_id') ?: '') ?>" />
		...
		<label for="name">Name:</label>
		<input type="text" name="name" id="name" value="<?php echo htmlspecialchars($params->get('name') ?: '') ?>" />
		...
		<label for="type">Type:</label>
		<select name="type">
			<option value="-1"<?php if (($type=$params->get('type')) === '-1') echo ' selected="selected"' ?>>Any</option>
			<?php foreach (Flux::config('ItemTypes')->toArray() as $typeId => $typeName): ?>
				<option value="<?php echo $typeId ?>"<?php if (($type=$params->get('type')) === strval($typeId)) echo ' selected="selected"' ?>><?php echo htmlspecialchars($typeName) ?></option>
				<?php $itemSubTypes = Flux::config('ItemSubTypes')->toArray() ?>
				<?php if (array_key_exists($typeId, $itemSubTypes)): ?>
					<?php foreach ($itemSubTypes[$typeId] as $subtypeId => $subtypeName): ?>
					<option value="<?php echo $typeId ?>-<?php echo $subtypeId ?>"<?php if (($type=$params->get('type')) === ($typeId . '-' . $subtypeId)) echo ' selected="selected"' ?>><?php echo htmlspecialchars($typeName . ' - ' . $subtypeName) ?></option>
					<?php endforeach ?>
				<?php endif ?>
			<?php endforeach ?>
		</select>
		...
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>
<?php if ($items): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('item_id', 'ID') ?></th>
		<th colspan="2"><?php echo $paginator->sortableColumn('name', 'Name') ?></th>
		<th><?php echo $paginator->sortableColumn('type', 'Type') ?></th>
		<th><?php echo $paginator->sortableColumn('price_buy', 'Buy') ?></th>
		<th><?php echo $paginator->sortableColumn('weight', 'Weight') ?></th>
		<th><?php echo $paginator->sortableColumn('attack', 'ATK') ?></th>
		<th><?php echo $paginator->sortableColumn('defense', 'DEF') ?></th>
		<th><?php echo $paginator->sortableColumn('slots', 'Slots') ?></th>
	</tr>
	<?php foreach ($items as $item): ?>
	<tr>
		<td align="right">
			<?php if ($auth->actionAllowed('item', 'view')): ?>
				<?php echo $this->linkToItem($item->item_id, $item->item_id) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($item->item_id) ?>
			<?php endif ?>
		</td>
		<?php if ($icon=$this->iconImage($item->item_id)): ?>
			<td width="24"><img src="<?php echo htmlspecialchars($icon) ?>?nocache=<?php echo rand() ?>" /></td>
			<td><?php echo htmlspecialchars($item->name ?: '') ?></td>
		<?php else: ?>
			<td colspan="2"><?php echo htmlspecialchars($item->name ?: '') ?></td>
		<?php endif ?>
		<td>
			<?php if ($type=$this->itemTypeText($item->type)): ?>
				<?php echo htmlspecialchars($type) ?>
			<?php else: ?>
				<span class="not-applicable">—</span>
			<?php endif ?>
		</td>
		<td><?php echo number_format((int)$item->price_buy) ?></td>
		<td><?php echo round($item->weight ?: 0, 1) ?></td>
		<td><?php echo number_format((int)$item->attack) ?></td>
		<td><?php echo number_format((int)$item->defense) ?></td>
		<td><?php echo number_format((int)$item->slots) ?></td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
	<p>No items found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
