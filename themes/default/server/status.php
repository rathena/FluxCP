<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('ServerStatusHeading')) ?></h2>
<p><?php echo htmlspecialchars(Flux::message('ServerStatusInfo')) ?></p>
<?php foreach ($serverStatus as $privServerName => $gameServers): ?>
<h3>Server Status for <?php echo htmlspecialchars($privServerName) ?></h3>
<table id="server_status">
	<tr>
		<td class="status"><?php echo htmlspecialchars(Flux::message('ServerStatusServerLabel')) ?></td>
		<td class="status"><?php echo htmlspecialchars(Flux::message('ServerStatusLoginLabel')) ?></td>
		<td class="status"><?php echo htmlspecialchars(Flux::message('ServerStatusCharLabel')) ?></td>
		<td class="status"><?php echo htmlspecialchars(Flux::message('ServerStatusMapLabel')) ?></td>
		<td class="status"><?php echo htmlspecialchars(Flux::message('ServerStatusOnlineLabel')) ?></td>
		<?php if(Flux::config('EnablePeakDisplay')): ?>
			<td class="status"><?php echo htmlspecialchars(Flux::message('ServerStatusPeakLabel')) ?></td>
		<?php endif ?>
	</tr>
	<?php foreach ($gameServers as $serverName => $gameServer): ?>
	<tr>
		<th class="server"><?php echo htmlspecialchars($serverName) ?></th>
		<td class="status"><?php echo $this->serverUpDown($gameServer['loginServerUp']) ?></td>
		<td class="status"><?php echo $this->serverUpDown($gameServer['charServerUp']) ?></td>
		<td class="status"><?php echo $this->serverUpDown($gameServer['mapServerUp']) ?></td>
		<td class="status"><?php echo $gameServer['playersOnline'] ?></td>
		<?php if(Flux::config('EnablePeakDisplay')): ?>
			<td class="status"><?php echo $gameServer['playersPeak'] ?></td>
		<?php endif ?>
	</tr>
	<?php endforeach ?>
</table>


<?php endforeach ?>
