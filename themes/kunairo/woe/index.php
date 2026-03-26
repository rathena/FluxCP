<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php
$_krDayMap = array(
	'Sunday' => Flux::message('KunaiRODaySunday'),
	'Monday' => Flux::message('KunaiRODayMonday'),
	'Tuesday' => Flux::message('KunaiRODayTuesday'),
	'Wednesday' => Flux::message('KunaiRODayWednesday'),
	'Thursday' => Flux::message('KunaiRODayThursday'),
	'Friday' => Flux::message('KunaiRODayFriday'),
	'Saturday' => Flux::message('KunaiRODaySaturday'),
);
$_krServerDay = $server->getServerTime('l');
$_krServerDayTranslated = isset($_krDayMap[$_krServerDay]) ? $_krDayMap[$_krServerDay] : $_krServerDay;
?>
<h2><?php echo htmlspecialchars(Flux::message('WoeHeading')) ?></h2>
<?php if ($woeTimes): ?>
<p><?php echo htmlspecialchars(sprintf(Flux::message('WoeInfo'), $session->loginAthenaGroup->serverName)) ?></p>
<p><?php echo htmlspecialchars(Flux::message('WoeServerTimeInfo')) ?> <strong class="important"><?php echo $server->getServerTime('Y-m-d H:i:s') ?> (<?php echo htmlspecialchars($_krServerDayTranslated) ?>)</strong>.</p>
<table class="woe-table">
	<tr>
		<th><?php echo htmlspecialchars(Flux::message('WoeServerLabel')) ?></th>
		<th colspan="3"><?php echo htmlspecialchars(Flux::message('WoeTimesLabel')) ?></th>
	</tr>
	<?php foreach ($woeTimes as $serverName => $times): ?>
	<tr>
		<td class="server" rowspan="<?php echo count($times)+1 ?>">
			<?php echo htmlspecialchars($serverName) ?>
		</td>
	</tr>
	<?php foreach ($times as $time): ?>
	<tr>
		<td class="time">
			<?php echo htmlspecialchars($time['startingDay']) ?>
			@ <?php echo htmlspecialchars($time['startingHour']) ?>
		</td>
		<td>~</td>
		<td class="time">
			<?php echo htmlspecialchars($time['endingDay']) ?>
			@ <?php echo htmlspecialchars($time['endingHour']) ?>
		</td>
	</tr>
	<?php endforeach ?>
	<?php endforeach ?>
</table>
<?php else: ?>
<p><?php echo htmlspecialchars(Flux::message('WoeNotScheduledInfo')) ?></p>
<?php endif ?>
