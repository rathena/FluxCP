<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('InterLogHeading')) ?></h2>

<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="logmsg">Log Message:</label>
		<input type="text" name="logmsg" id="logmsg" value="<?php echo htmlspecialchars($params->get('logmsg')) ?>" />
		...
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
