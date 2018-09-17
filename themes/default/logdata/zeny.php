<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('ZenyLogHeading')) ?></h2>
<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="to_char">Received Char ID:</label>
		<input type="text" name="to_char" id="to_char" value="<?php echo htmlspecialchars($params->get('to_char')) ?>" />
		...
		<label for="from_char">Source Char ID:</label>
		<input type="text" name="from_char" id="from_char" value="<?php echo htmlspecialchars($params->get('from_char')) ?>" />
		...
		<label for="zeny_min">Zeny Min:</label>
		<input type="text" name="zeny_min" id="zeny_min" value="<?php echo htmlspecialchars($params->get('zeny_min')) ?>" />
		...
		<label for="zeny_max">Zeny Max:</label>
		<input type="text" name="zeny_max" id="zeny_max" value="<?php echo htmlspecialchars($params->get('zeny_max')) ?>" />
		...
		<label for="map">Map:</label>
		<input type="text" name="map" id="map" value="<?php echo htmlspecialchars($params->get('map')) ?>" />
		...
		<br />
		<br />
		<label>Transaction type:</label><!-- shared same values -->
		<?php foreach (Flux::config('PickTypes')->toArray() as $picktype => $pickname): ?>
			<label title="<?php echo $pickname ?>"><input type="checkbox" name="type[<?php echo $picktype ?>]" value="1" <?php if (in_array($picktype,$type)) echo " checked=\"yes\" " ?> /> <?php echo $pickname ?> ..</label>
		<?php endforeach ?>
		<br />
		<br />
		<label for="from_date">Date from:</label>
		<input type="date" name="from_date" id="from_date" value="<?php echo htmlspecialchars($params->get('from_date')) ?>" />
		...
		<label for="to_date">Date to:</label>
		<input type="date" name="to_date" id="to_date" value="<?php echo htmlspecialchars($params->get('to_date')) ?>" />
		...
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>
<?php if ($logs): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('time', Flux::message('ZenyLogDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_id', Flux::message('ZenyLogCharacterLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('src_id', Flux::message('ZenyLogSourceLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('type', Flux::message('ZenyLogTypeLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('amount', Flux::message('ZenyLogAmountLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('map', Flux::message('ZenyLogMapLabel')) ?></th>
	</tr>
	<?php foreach ($logs as $log): ?>
	<tr>
		<td align="right"><?php echo $this->formatDateTime($log->time) ?></td>
		<td>
			<?php if ($log->char_name): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<strong><?php echo $this->linkToCharacter($log->char_id, $log->char_name) ?></strong>
				<?php else: ?>
					<strong><?php echo htmlspecialchars($log->char_name) ?></strong>	
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
			<?php if ($log->src_name): ?>
				<?php if ($log->type == 'M'): ?>
					<?php if ($auth->actionAllowed('monster', 'view')): ?>
						<em><?php echo $this->linkToMonster($log->src_id, $log->src_name) ?></em>
					<?php else: ?>
						<em><?php echo htmlspecialchars($log->src_name) ?></em>
					<?php endif ?>
				<?php else: ?>
					<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
						<strong><?php echo $this->linkToCharacter($log->src_id, $log->src_name) ?></strong>
					<?php else: ?>
						<strong><?php echo htmlspecialchars($log->src_name) ?></strong>	
					<?php endif ?>
				<?php endif ?>
			<?php elseif ($log->src_id): ?>
				<?php if ($log->type == 'M'): ?>
					<?php if ($auth->actionAllowed('monster', 'view')): ?>
						<em><?php echo $this->linkToMonster($log->src_id, $log->src_id) ?></em>
					<?php else: ?>
						<em><?php echo htmlspecialchars($log->src_id) ?></em>
					<?php endif ?>
				<?php else: ?>
					<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
						<strong><?php echo $this->linkToCharacter($log->src_id, $log->src_id) ?></strong>
					<?php else: ?>
						<strong><?php echo htmlspecialchars($log->src_id) ?></strong>	
					<?php endif ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($log->pick_type): ?>
				<?php echo htmlspecialchars($log->pick_type) ?>
			<?php elseif ($log->type): ?>
				<?php echo $log->type ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td><?php echo $log->amount >= 0 ? '+'.number_format((int)$log->amount) : number_format((int)$log->amount) ?></td>
		<td>
			<?php if ($log->map): ?>
				<?php echo htmlspecialchars(basename($log->map, '.gat')) ?>
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
	<?php echo htmlspecialchars(Flux::message('ZenyLogNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
