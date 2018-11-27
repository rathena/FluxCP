<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Buyers</h2>

<?php if ($stores): ?>
	<?php echo $paginator->infoText() ?>
	<table class="horizontal-table">
		<thead>
			<tr>
				<th><?php echo $paginator->sortableColumn('id', 'Buyer ID') ?></th>
				<th> <?php echo $paginator->sortableColumn('char_name', 'Buyer Name') ?></th>
				<th>Title</th>
				<th><?php echo $paginator->sortableColumn('map', 'Map') ?></th>
				<th>X</th>
				<th>Y</th>
				<th>Gender</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($stores as $store): ?>
				<tr>
					<td width="50" align="right" style="">
						<?php if ($auth->actionAllowed('buyingstore', 'viewshop')): ?>
							<a href="<?php echo $this->url('buyingstore', 'viewshop', array("id" => $store->id)); ?>"><?php echo $store->id; ?></a>
						<?php else: ?>
							<?php echo $store->id ?>
						<?php endif ?>
					</td>
					<td style="font-weight:bold;">
						<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
							<?php echo $this->linkToCharacter($store->char_id, $store->char_name); ?>
						<?php else: ?>
							<?php echo $store->char_name; ?>
						<?php endif ?>
					</td>

					<td>
					  <img src="<?php echo $this->iconImage(671) ?>?nocache=<?php echo rand() ?>" />
					 <?php if ($auth->actionAllowed('buyingstore', 'viewshop')): ?>
							<a href="<?php echo $this->url('buyingstore', 'viewshop', array("id" => $store->id)); ?>"><?php echo $store->title; ?></a>
						<?php else: ?>
							<?php echo $store->title ?>
						<?php endif ?>
					</td>

					<td style="color:blue;">
						<?php echo $store->map ?>
					</td>

					<td>
						<?php echo $store->x ?>
					</td>

					<td>
						<?php echo $store->y ?>
					</td>

					<td>
						<?php echo $store->sex ?>
					</td>

				</tr>

			<?php endforeach ?>
		</tbody>
	</table>
	<?php echo $paginator->getHTML() ?>
<?php else: ?>
	<p>No Buyers found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
