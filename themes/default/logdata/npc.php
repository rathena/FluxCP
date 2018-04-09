<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('NPCLogHeading')) ?></h2>
<?php if ($npcs): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('npc_date', Flux::message('NPCLogDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('account_id', Flux::message('NPCLogAccountIDLabe')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_id', Flux::message('CharLogCharIDLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_name', Flux::message('NPCLogCharNameLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('map', Flux::message('NPCLogMapLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('mes', Flux::message('NPCLogMsgLabel')) ?></th>
	</tr>
	<?php foreach ($npcs as $npc): ?>
	<tr>
		<td align="center"><?php echo $this->formatDateTime($npc->npc_date) ?></td>
		<td align="center">
			<?php if ($npc->account_id): ?>
				<?php if ($auth->actionAllowed('account', 'view') && $auth->allowedToViewAccount): ?>
					<?php echo $this->linkToAccount($npc->account_id, $npc->account_id) ?>
				<?php else: ?>
					<?php echo $npc->account_id ?>
				<?php endif ?>
			<?php else: ?>	
					<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td align="center">
			<?php if ($npc->char_id): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<?php echo $this->linkToCharacter($npc->char_id, $npc->char_id) ?>
				<?php else: ?>
					<?php echo $npc->char_id ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td align="center"><?php echo htmlspecialchars($npc->char_name) ?></td>
		<td align="center">
			<?php if (strlen(basename($npc->map, '.gat')) > 0): ?>
				<?php echo htmlspecialchars(basename($npc->map, '.gat')) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td align="center"><?php echo htmlspecialchars($npc->mes) ?></td>
		
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('NPCLogNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
