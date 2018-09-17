<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('CashLogHeading')) ?></h2>

<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="char_id">Char ID:</label>
		<input type="text" name="char_id" id="char_id" value="<?php echo htmlspecialchars($params->get('char_id')) ?>" />
		...
		<label for="cash_min">Cash Min:</label>
		<input type="text" name="cash_min" id="cash_min" value="<?php echo htmlspecialchars($cash_min) ?>" />
		...
		<label for="cash_max">Cash Max:</label>
		<input type="text" name="cash_max" id="cash_max" value="<?php echo htmlspecialchars($cash_max) ?>" />
		...
		<label for="map">Map:</label>
		<input type="text" name="map" id="map" value="<?php echo htmlspecialchars($params->get('map')) ?>" />
		...
		<br />
		<br />
		<label>Cash type:</label>
		<?php foreach (Flux::config('CashTypes')->toArray() as $cashtype => $cashname): ?>
			<label title="<?php echo $cashname ?>"><input type="checkbox" name="cash_type[<?php echo $cashtype ?>]" value="1" <?php if (in_array($cashtype,$cash_type)) echo " checked=\"yes\" " ?> /> <?php echo $cashname ?> ..</label>
		<?php endforeach ?>
		<br />
		<br />
		<label>Pick type:</label><!-- shared same values -->
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
		<th><?php echo $paginator->sortableColumn('time', Flux::message('CashLogDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_id', Flux::message('CashLogCharacterLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('cash_type', Flux::message('CashLogCashTypeLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('type', Flux::message('CashLogTypeLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('amount', Flux::message('CashLogAmountLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('map', Flux::message('CashLogMapLabel')) ?></th>
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
			<?php if ($log->cash_type_name): ?>
				<?php echo htmlspecialchars($log->cash_type_name) ?>
			<?php elseif ($log->cash_type): ?>
				<?php echo $log->cash_type ?>
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
	<?php echo htmlspecialchars(Flux::message('CashLogNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
