<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Preferences</h2>
<?php if ($char): ?>
<?php if (!empty($errorMessage)): ?>
<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<h3>Viewing character preferences for “<?php echo ($charName=htmlspecialchars($char->name))  ?>” on <?php echo htmlspecialchars($server->serverName) ?></h3>
<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
	<input type="hidden" name="charprefs" value="1" />
	<table class="generic-form-table">
		<tr>
			<th><label for="hide_from_whos_online">Hide Character From "Who's Online"</label></th>
			<td><input type="checkbox" name="hide_from_whos_online" id="hide_from_whos_online"<?php if ($hideFromWhosOnline) echo ' checked="checked"' ?> /></td>
			<td><p>This will hide <?php echo $charName ?> altogether from the "Who's Online" page.</p></td>
		</tr>
		<tr>
			<th><label for="hide_map_from_whos_online">Hide Current Map From "Who's Online"</label></th>
			<td><input type="checkbox" name="hide_map_from_whos_online" id="hide_map_from_whos_online"<?php if ($hideMapFromWhosOnline) echo ' checked="checked"' ?> /></td>
			<td><p>This will hide <?php echo $charName ?>'s current location from the "Who's Online" page.</p></td>
		</tr>
		<?php if ($auth->allowedToHideFromZenyRank): ?>
		<tr>
			<th><label for="hide_from_zeny_ranking">Hide Character From "Zeny Ranking"</label></th>
			<td><input type="checkbox" name="hide_from_zeny_ranking" id="hide_from_zeny_ranking"<?php if ($hideFromZenyRanking) echo ' checked="checked"' ?> /></td>
			<td><p>This will hide <?php echo $charName ?> from the "Zeny Ranking" page.</p></td>
		</tr>
		<?php endif ?>
		<tr>
			<td align="right"><p><input type="submit" value="Modify Preferences" /></p></td>
			<td colspan="2"></td>
		</tr>
	</table>
</form>
<?php else: ?>
<p>No such character found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
