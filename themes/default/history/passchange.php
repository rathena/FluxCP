<?php if (!defined('FLUX_ROOT')) exit ?>
<h2><?php echo htmlspecialchars(Flux::message('HistoryPassChangeHeading')) ?></h2>
<?php if ($changes): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('change_date', Flux::message('HistoryPassChangeChangeDate')) ?></th>
		<th><?php echo $paginator->sortableColumn('change_ip', Flux::message('HistoryPassChangeChangeIp')) ?></th>
	</tr>
	<?php foreach ($changes as $change): ?>
	<tr>
		<td><?php echo htmlspecialchars($change->change_date) ?></td>
		<td>
		<?php if ($auth->actionAllowed('account', 'index')): ?>
			<?php echo $this->linkToAccountSearch(array('last_ip' => $change->change_ip), $change->change_ip) ?>
		<?php else: ?>
			<?php echo htmlspecialchars($change->change_ip) ?>
		<?php endif ?>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('HistoryNoPassChanges')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
