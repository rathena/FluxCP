<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars($title) ?></h2>

<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="char_id">Char ID:</label>
		<input type="text" name="char_id" id="char_id" value="<?php echo htmlspecialchars($params->get('char_id') ?: '') ?>" />
		...
		<label for="map">Map:</label>
		<input type="text" name="map" id="map" value="<?php echo htmlspecialchars($params->get('map') ?: '') ?>" />
		...
		<label for="target">Target ID:</label>
		<input type="text" name="target" id="target" value="<?php echo htmlspecialchars($params->get('target') ?: '') ?>" />
		...
		<label for="item_id">Item ID:</label>
		<input type="text" name="item_id" id="item_id" value="<?php echo htmlspecialchars($params->get('item_id') ?: '') ?>" />
		...
		<label>Feeding Type:</label><!-- shared same values -->
		<?php foreach (Flux::config('FeedingTypes')->toArray() as $feedtype => $typename): ?>
			<label title="<?php echo $typename ?>"><input type="checkbox" name="type[<?php echo $feedtype ?>]" value="1" <?php if (in_array($feedtype,$type)) echo " checked=\"yes\" " ?> /> <?php echo $typename ?> ..</label>
		<?php endforeach ?>
		<br />
		<br />
		<label for="from_date">Date from:</label>
		<input type="date" name="from_date" id="from_date" value="<?php echo htmlspecialchars($params->get('from_date') ?: '') ?>" />
		...
		<label for="to_date">Date to:</label>
		<input type="date" name="to_date" id="to_date" value="<?php echo htmlspecialchars($params->get('to_date') ?: '') ?>" />
		...
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>

<?php if ($feeds): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('time', 'Time') ?></th>
		<th><?php echo $paginator->sortableColumn('char_id', 'Char ID') ?></th>
		<th><?php echo $paginator->sortableColumn('type', 'Type') ?></th>
		<th><?php echo $paginator->sortableColumn('target_class', 'Target Class') ?></th>
		<th><?php echo $paginator->sortableColumn('intimacy', 'Intimacy') ?></th>
		<th><?php echo $paginator->sortableColumn('item_id', 'Item ID') ?></th>
		<th><?php echo $paginator->sortableColumn('map', 'Map') ?></th>
	</tr>
	<?php foreach ($feeds as $log): ?>
	<tr>
		<td align="right"><?php echo $this->formatDateTime($log->time) ?></td>
		<td>
			<?php if (array_key_exists($log->char_id, $charIDs)): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<strong><?php echo $this->linkToCharacter($log->char_id, $charIDs[$log->char_id]) ?></strong>
				<?php else: ?>
					<strong><?php echo htmlspecialchars($charIDs[$log->char_id]) ?></strong>
				<?php endif ?>
			<?php elseif ($log->char_id): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<strong><?php echo $this->linkToCharacter($log->char_id, $log->char_id) ?></strong>
				<?php else: ?>
					<strong><?php echo htmlspecialchars($log->char_id) ?></strong>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($log->type_name): ?>
				<?php echo htmlspecialchars($log->type_name) ?>
			<?php elseif ($log->type): ?>
				<?php echo $log->type ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($log->target_name): ?>
				<?php echo $this->linkToMonster($log->target_class, htmlspecialchars($log->target_name)) ?>
			<?php else: ?>
				<?php echo $this->linkToMonster($log->target_class, $log->target_class) ?>
			<?php endif ?>
		</td>
		<td><?php echo $log->intimacy ?></td>
		<td>
			<?php if (array_key_exists($log->item_id, $itemIDs)): ?>
				<?php echo $this->linkToItem($log->item_id, htmlspecialchars($itemIDs[$log->item_id])) ?>
			<?php else: ?>
				<?php echo $this->linkToItem($log->item_id, $log->item_id) ?>
			<?php endif ?>
		</td>
		<td>
			<?php if ($log->map): ?>
				<?php echo htmlspecialchars(basename($log->map, '.gat')) ?>
				<?php echo $log->x ?>,<?php echo $log->y ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('FeedingLogNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
