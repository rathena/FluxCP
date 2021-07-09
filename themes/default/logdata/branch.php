<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('BranchLogHeading')) ?></h2>
<?php if ($branchs): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('branch_date', Flux::message('BranchLogDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('account_id', Flux::message('BranchLogAccountIDLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_id', Flux::message('BranchLogCharIDLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_name', Flux::message('BranchLogCharNameLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('map', Flux::message('BranchLogMapLabel')) ?></th>
	</tr>
	<?php foreach ($branchs as $branch): ?>
	<tr>
		<td align="center"><?php echo $this->formatDateTime($branch->branch_date) ?></td>
		<td align="center">
			<?php if ($branch->account_id): ?>
				<?php if ($auth->actionAllowed('account', 'view') && $auth->allowedToViewAccount): ?>
					<?php echo $this->linkToAccount($branch->account_id, $branch->account_id) ?>
				<?php else: ?>
					<?php echo $branch->account_id ?>
				<?php endif ?>
			<?php else: ?>	
					<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td align="center">
			<?php if ($branch->char_id): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<?php echo $this->linkToCharacter($branch->char_id, $branch->char_id) ?>
				<?php else: ?>
					<?php echo $branch->char_id ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td align="center"><?php echo htmlspecialchars($branch->char_name) ?></td>
		<td align="center">
			<?php if (strlen(basename($branch->map, '.gat')) > 0): ?>
				<?php echo htmlspecialchars(basename($branch->map, '.gat')) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('BranchLogNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
