<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars($title); ?></h2>
<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="item">Item ID/Name:</label>
		<input type="text" name="item" id="item" value="<?php echo htmlspecialchars($params->get('item')) ?>" />
		...
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>

<?php if ($items): ?>
	<?php echo $paginator->infoText() ?>
	<table class="horizontal-table">
		<thead>
			<tr>
				<th width="40"><?php echo $paginator->sortableColumn('nameid', 'Item ID') ?></th>
				<th colspan="2"><?php echo $paginator->sortableColumn('item_name', 'Name') ?></th>
				<th><?php echo $paginator->sortableColumn('price', 'Price') ?></th>
				<th>Amount</th>
				<th width="100"><?php echo $paginator->sortableColumn('title', 'Shop Name')?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($items as $item): ?>
				<tr>
					<td width="50" align="right" style="">
						<?php if ($auth->actionAllowed('item', 'view')): ?>
							<a href="<?php echo $this->url('item', 'view', array("id" => $item->nameid)); ?>"><?php echo $item->nameid; ?></a>
						<?php else: ?>
							<?php echo $item->nameid ?>
						<?php endif ?>
					</td>
					<td width="26">
						<img src="<?php echo $this->iconImage($item->nameid) ?>?nocache=<?php echo rand() ?>" />
					</td>
					<td>
						<?php echo $item->item_name ?>
					</td>
					<td align="right" class="price <?php echo $this->getPriceStyle($item->price) ?>">
						<?php echo number_format($item->price, 0, '.', ','); ?> z
					</td>
					<td align="right" width="50">
						<?php echo $item->amount ?>
					</td>
					<td>
						<?php if ($auth->actionAllowed('buyingstore', 'viewshop')): ?>
							<span title="Click for items bought by this buyer and location"><a href="<?php echo $this->url('buyingstore', 'viewshop', array("id" => $item->buyid)); ?>"><?php echo $item->title; ?></a></span>
						<?php else: ?>
							<span title="Click for items bought by this buyer and location"><?php echo $item->title ?></span>
						<?php endif ?>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
	<?php echo $paginator->getHTML() ?>
<?php else: ?>
	<p>No Items found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
