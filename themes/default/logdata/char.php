<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('CharLogHeading')) ?></h2>
<?php if ($chars1): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('time', Flux::message('CharLogDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_msg', Flux::message('CharLogMsgLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('account_id', Flux::message('CharLogAccountIDLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_num', Flux::message('CharLogCharNumLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('name', Flux::message('CharLogCharNameLabel')) ?></th>
	</tr>
	<?php foreach ($chars1 as $char1): ?>
	<tr>
		<td align="center"><?php echo $this->formatDateTime($char1->time) ?></td>
		<td align="center"><?php echo htmlspecialchars($char1->char_msg) ?></td>
		<td align="center">
			<?php if ($char1->account_id): ?>
				<?php if ($auth->actionAllowed('account', 'view') && $auth->allowedToViewAccount): ?>
					<?php echo $this->linkToAccount($char1->account_id, $char1->account_id) ?>
				<?php else: ?>
					<?php echo $char1->account_id ?>
				<?php endif ?>
			<?php else: ?>	
					<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td align="center"><?php echo htmlspecialchars($char1->char_num) ?></td>
		<td align="center"><?php echo htmlspecialchars($char1->name) ?></td>
		
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('CharLogNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
