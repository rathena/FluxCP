<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Castles</h2>
<p>This page shows what castles are activated and which guilds own them.</p>
<?php if ($castles): ?>
<table class="vertical-table">
	<tr>
		<th>Castle ID</th>
		<th>Castle</th>
		<th colspan="2">Guild</th>
		<th>Economy</th>
	</tr>
	<?php foreach ($castles as $castle): ?>
		<tr>
			<td align="right"><?php echo htmlspecialchars($castle->castle_id) ?></td>
			<td><?php echo htmlspecialchars($castleNames[$castle->castle_id]) ?></td>
			<?php if ($castle->guild_name): ?>
				<?php if ($castle->emblem): ?>
					<td width="24"><img src="<?php echo $this->emblem($castle->guild_id) ?>" /></td>
					<td>
						<?php if ($auth->actionAllowed('guild', 'view') && $auth->allowedToViewGuild): ?>
							<?php echo $this->linkToGuild($castle->guild_id, $castle->guild_name) ?>
							<td><?php echo $castle->economy; ?></td>
						<?php else: ?>
							<?php echo htmlspecialchars($castle->guild_name) ?>
							
						<?php endif ?>
					</td>
				<?php else: ?>
					<td colspan="2"><?php echo htmlspecialchars($castle->guild_name) ?></td>
				<?php endif ?>
			<?php else: ?>
				<td colspan="2"><span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span></td>
			<?php endif ?>
		</tr>
		
	<?php endforeach ?>
</table>
<?php else: ?>
<p>No castles found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
