<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars($title); ?></h2>
<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="buyer">Buyer:</label>
		<input type="text" name="buyer" id="buyer" value="<?php echo htmlspecialchars($params->get('buyer')) ?>" />
		...
		<label for="title">Title:</label>
		<input type="text" name="title" id="title" value="<?php echo htmlspecialchars($params->get('title')) ?>" />
		...
		<label for="map">Map:</label>
		<input type="text" name="map" id="map" value="<?php echo htmlspecialchars($params->get('map')) ?>" />
		...
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>

<?php if ($stores): ?>
	<?php echo $paginator->infoText() ?>
	<table class="horizontal-table">
		<thead>
			<tr>
				<th><?php echo $paginator->sortableColumn('buyid', 'ID') ?></th>
				<th> <?php echo $paginator->sortableColumn('name', 'Buyer Name') ?></th>
				<th><?php echo $paginator->sortableColumn('title', 'Title') ?></th>
				<th><?php echo $paginator->sortableColumn('map', 'Map') ?></th>
				<th>X</th>
				<th>Y</th>
				<th>Gender</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($stores as $item): ?>
				<tr>
					<td width="50" align="right" style="">
						<?php if ($auth->actionAllowed('buyingstore', 'viewshop')): ?>
							<a href="<?php echo $this->url('buyingstore', 'viewshop', array("id" => $item->buyid)); ?>"><?php echo $item->buyid; ?></a>
						<?php else: ?>
							<?php echo $item->buyid ?>
						<?php endif ?>
					</td>
					<td style="font-weight:bold;">
						<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
							<?php echo $this->linkToCharacter($item->char_id, $item->name); ?>
						<?php else: ?>
							<?php echo $item->name; ?>
						<?php endif ?>
					</td>

					<td>
					  <img src="<?php echo $this->iconImage(671) ?>?nocache=<?php echo rand() ?>" />
					 <?php if ($auth->actionAllowed('buyingstore', 'viewshop')): ?>
							<a href="<?php echo $this->url('buyingstore', 'viewshop', array("id" => $item->buyid)); ?>"><?php echo $item->title; ?></a>
						<?php else: ?>
							<?php echo $item->title ?>
						<?php endif ?>
					</td>

					<td style="color:blue;">
						<?php echo $item->map ?>
					</td>

					<td>
						<?php echo $item->x ?>
					</td>

					<td>
						<?php echo $item->y ?>
					</td>

					<td>
						<?php echo $item->sex ?>
					</td>

				</tr>

			<?php endforeach ?>
		</tbody>
	</table>
	<?php echo $paginator->getHTML() ?>
<?php else: ?>
	<p>No Buyers found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
