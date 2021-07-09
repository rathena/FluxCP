<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<?php if (!empty($successMessage)): ?>
    <p class="green"><?php echo htmlspecialchars($successMessage) ?></p>
<?php endif ?>

<h3>PHP Configuration</h3>
<p>These values must be larger than the size of your itemInfo file.</p>
<table class="vertical-table">
	<tr>
		<th>PHP Configs</th><td>Value</td>
	</tr>
	<tr>
		<th>post_max_size</th><td><?php echo ini_get('post_max_size') ?></td>
	</tr>
	<tr>
		<th>upload_max_filesize</th><td><?php echo ini_get('upload_max_filesize') ?></td>
	</tr>
</table>
<p>ShowItemDesc is <?php if(Flux::config('ShowItemDesc')):?>enabled<?php else: ?>disabled<?php endif ?> in your configuration file.</p>

<h3>Upload itemInfo.lua</h3>
<form class="forms" method="post" enctype="multipart/form-data">
    <input type="file" name="iteminfo"><br>
    <input class="btn" type="submit">
</form>

<h3>Current Count</h3>
<p>There are currently <?php echo number_format($return->count) ?> item descriptions in the database</p>
