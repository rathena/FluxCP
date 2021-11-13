<?php if (!defined('FLUX_ROOT')) exit ?>
<h2><?php echo htmlspecialchars(Flux::message('HistoryEmailHeading')) ?></h2>
<?php if ($changes): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('request_date', Flux::message('HistoryEmailRequestDate')) ?></th>
		<th><?php echo $paginator->sortableColumn('request_ip', Flux::message('HistoryEmailRequestIp')) ?></th>
		<th><?php echo $paginator->sortableColumn('old_email', Flux::message('HistoryEmailOldAddress')) ?></th>
		<th><?php echo $paginator->sortableColumn('new_email', Flux::message('HistoryEmailNewAddress')) ?></th>
		<th><?php echo $paginator->sortableColumn('change_date', Flux::message('HistoryEmailChangeDate')) ?></th>
		<th><?php echo $paginator->sortableColumn('change_ip', Flux::message('HistoryEmailChangeIp')) ?></th>
	</tr>
	<?php foreach ($changes as $change): ?>
	<tr>
		<td><?php echo $this->formatDateTime($change->request_date) ?></td>
		<td>
		<?php if ($auth->actionAllowed('account', 'index')): ?>
			<?php echo $this->linkToAccountSearch(array('last_ip' => $change->request_ip), $change->request_ip) ?>
		<?php else: ?>
			<?php echo htmlspecialchars($change->request_ip) ?>
		<?php endif ?>
		</td>
		<td>
		<?php if ($auth->actionAllowed('account', 'index')): ?>
			<?php echo $this->linkToAccountSearch(array('email' => $change->old_email), $change->old_email) ?>
		<?php else: ?>
			<?php echo htmlspecialchars($change->old_email) ?>
		<?php endif ?>
		</td>
		<td>
		<?php if ($auth->actionAllowed('account', 'index')): ?>
			<?php echo $this->linkToAccountSearch(array('email' => $change->new_email), $change->new_email) ?>
		<?php else: ?>
			<?php echo htmlspecialchars($change->new_email) ?>
		<?php endif ?>
		</td>
		<td>
			<?php if ($change->change_date): ?>
				<?php echo htmlspecialchars($change->change_date) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NeverLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($change->change_ip): ?>
				<?php if ($auth->actionAllowed('account', 'index')): ?>
					<?php echo $this->linkToAccountSearch(array('last_ip' => $change->change_ip), $change->change_ip) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($change->change_ip) ?>
				<?php endif ?>
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
	<?php echo htmlspecialchars(Flux::message('HistoryNoEmailChanges')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
