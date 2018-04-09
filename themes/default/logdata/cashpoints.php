<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('CashLogHeading')) ?></h2>
<?php if ($logs): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('time', Flux::message('CashLogDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_id', Flux::message('CashLogCharacterLabel')) ?></th>
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
