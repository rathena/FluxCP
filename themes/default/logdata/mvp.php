<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('MVPLogHeading')) ?></h2>
<?php if ($mvps): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('mvp_date', Flux::message('MVPLogDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('kill_char_id', Flux::message('MVPLogCharacterLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('monster_id', Flux::message('MVPLogMonsterLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('prize', Flux::message('MVPLogPrizeLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('mvpexp', Flux::message('MVPLogExpLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('map', Flux::message('MVPLogMapLabel')) ?></th
	</tr>
	<?php foreach ($mvps as $mvp): ?>
	<tr>
		<td align="center"><?php echo $this->formatDateTime($mvp->mvp_date) ?></td>
		<td align="center">
			<?php if ($mvp->kill_char_id): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<?php echo $this->linkToCharacter($mvp->kill_char_id, $mvp->kill_char_id) ?>
				<?php else: ?>
					<?php echo $mvp->kill_char_id ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td align="center">
		<?php if ($auth->actionAllowed('monster', 'view')): ?>
				<?php echo $this->linkToMonster($mvp->monster_id, $mvp->monster_id) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($mvp->monster_id) ?>
			<?php endif ?>
		</td>
		<td align="center"><?php echo htmlspecialchars($mvp->prize) ?></td>
		<td align="center"><?php echo htmlspecialchars($mvp->mvpexp) ?></td>
		<td align="center">
			<?php if (strlen(basename($mvp->map, '.gat')) > 0): ?>
				<?php echo htmlspecialchars(basename($mvp->map, '.gat')) ?>
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
	<?php echo htmlspecialchars(Flux::message('MVPLogNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
