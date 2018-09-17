<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars($title); ?></h2>

<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="vendor">Vendor:</label>
		<input type="text" name="vendor" id="vendor" value="<?php echo htmlspecialchars($params->get('vendor')) ?>" />
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

<?php if ($vendors): ?>
	<?php echo $paginator->infoText() ?>
	<table class="horizontal-table">
		<thead>
			<tr>
				<th><?php echo $paginator->sortableColumn('vending_id', 'ID') ?></th>
				<th> <?php echo $paginator->sortableColumn('name', 'Vendor Name') ?></th>
				<th><?php echo $paginator->sortableColumn('title', 'Title') ?></th>
				<th><?php echo $paginator->sortableColumn('map', 'Map') ?></th>
				<th>X</th>
				<th>Y</th>
				<th>Gender</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($vendors as $vending): ?>
				<tr>
					<td width="50" align="right"  style="">
						<?php if ($auth->actionAllowed('vending', 'viewshop')): ?>
							<a href="<?php echo $this->url('vending', 'viewshop', array("id" => $vending->vending_id)); ?>"><?php echo $vending->vending_id; ?></a>
						<?php else: ?>
							<?php echo $vending->vending_id ?>
						<?php endif ?>
					</td>
					<td style="font-weight:bold;">
						<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
							<?php echo $this->linkToCharacter($vending->char_id, $vending->name); ?>
						<?php else: ?>
							<?php echo $vending->name; ?>
						<?php endif ?>
					</td>

					<td>
					   <img src="<?php echo $this->iconImage(671) ?>?nocache=<?php echo rand() ?>" />
					  <?php if ($auth->actionAllowed('vending', 'viewshop')): ?>
							<a href="<?php echo $this->url('vending', 'viewshop', array("id" => $vending->vending_id)); ?>"><?php echo $vending->title; ?></a>
						<?php else: ?>
							<?php echo $vending->title ?>
						<?php endif ?>
					</td>

					<td  style="color:blue;">
					  <?php echo $vending->map ?>
					</td>

					<td>
					  <?php echo $vending->x ?>
					</td>

					<td>
					  <?php echo $vending->y ?>
					</td>

					 <td>
					  <?php echo $vending->sex ?>
					</td>

				</tr>

			<?php endforeach ?>
		</tbody>
	</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
	<p>No Vendors found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
