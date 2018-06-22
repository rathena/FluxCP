<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars($title); ?></h2>
<?php if ($store): ?>
	<h3 style="text-align:right; margin:0; padding:0;font-style: italic"><img style="position:relative;top:7px;" src="<?php echo $this->iconImage(671) ?>?nocache=<?php echo rand() ?>" /> <?php echo $store->title ?> </h3>
	<h4 style="text-align:right; color:blue; margin:0; margin-bottom:15px; "> <?php echo $store->map; ?>, <?php echo $store->x; ?>, <?php echo $store->y; ?> </h4>

	<?php if ($items): ?>
		<table class="horizontal-table">
			<thead>
				<tr>
					<th width="40">Item ID</th>
					<th colspan="2">Name</th>
					<th>Price</th>
					<th>Amount</th>
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
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	<?php else: ?>
		<p>No Items found. <a href="javascript:history.go(-1)">Go back</a>.</p>
	<?php endif ?>


<?php else: ?>
	<p>No Buyer found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
