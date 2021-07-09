<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Map Statistics</h2>
<?php if ($maps): ?>
<?php $playerTotal = 0; foreach ($maps as $map) $playerTotal += $map->player_count ?>
<p>This page shows how many online players are located a specific map, for all maps that have <em>any</em> online players at all.</p>
<p><strong><?php echo number_format($playerTotal) ?></strong> online player(s) were found
distributed across <strong><?php echo number_format(count($maps)) ?></strong> map(s).</p>
<div class="generic-form-div">
	<table class="generic-form-table">
		<?php foreach ($maps as $map): ?>
		<tr>
			<td align="right"><p class="important"><strong><?php echo htmlspecialchars(basename($map->map_name, '.gat')) ?></strong></p></td>
			<td><p><strong><em><?php echo number_format($map->player_count) ?></em></strong> player(s)</p></td>
		</tr>
		<?php endforeach ?>
	</table>
</div>
<?php else: ?>
<p>No players found on any maps. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
