<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Export Guild Emblems</h2>
<p>Please select the servers for which you would like to have the guild emblems exported as an archived ZIP file.</p>
<form action="<?php echo $this->url ?>" method="post">
	<input type="hidden" name="post" value="1" />
	<?php foreach ($serverNames as $serverName): ?>
	<p class="emblem-server"><label>
		&raquo;
		<input type="checkbox" name="server[]" checked="checked" value="<?php echo htmlspecialchars($serverName) ?>" />
		<span><?php echo htmlspecialchars($serverName) ?></span>
	</label></p>
	<?php endforeach ?>
	<button type="submit" class="submit_button">Export</button>
</form>
