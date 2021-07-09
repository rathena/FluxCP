<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('HistoryCpLoginHeading')) ?></h2>
<?php if ($logins): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('login_date', Flux::message('HistoryLoginDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('ip', Flux::message('HistoryIpAddrLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('error_code', Flux::message('HistoryErrorCodeLabel')) ?></th>
	</tr>
	<?php foreach ($logins as $login): ?>
	<tr>
		<td><?php echo $this->formatDateTime($login->login_date) ?></td>
		<td>
		<?php if ($auth->actionAllowed('account', 'index')): ?>
			<?php echo $this->linkToAccountSearch(array('last_ip' => $login->ip), $login->ip) ?>
		<?php else: ?>
			<?php echo htmlspecialchars($login->ip) ?>
		<?php endif ?>
		</td>
		<td>
			<?php if (is_null($login->error_code)): ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php else: ?>
				<?php echo htmlspecialchars($login->error_type) ?>
			<?php endif ?>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
	<p>
		<?php echo htmlspecialchars(Flux::message('HistoryNoCpLogins')) ?>
		<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
	</p>
<?php endif ?>
