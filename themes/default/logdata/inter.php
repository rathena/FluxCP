<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('InterLogHeading')) ?></h2>
<?php if ($inters): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('time', Flux::message('InterLogDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('log', Flux::message('InterLogLabel')) ?></th>
	</tr>
	<?php foreach ($inters as $inter): ?>
	<tr>
		<td align="center"><?php echo $this->formatDateTime($inter->time) ?></td>
		<td align="center"><?php echo htmlspecialchars($inter->log) ?></td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('InterLogNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
