<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('ResetPassTitle')) ?></h2>
<?php if (!empty($errorMessage)): ?>
<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<p><?php echo htmlspecialchars(Flux::message('ResetPassInfo')) ?></p>
<p><?php echo htmlspecialchars(Flux::message('ResetPassInfo2')) ?></p>
<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
	<table class="generic-form-table">
		<?php if (count($serverNames) > 1): ?>
		<tr>
			<th><label for="login"><?php echo htmlspecialchars(Flux::message('ResetPassServerLabel')) ?></label></th>
			<td>
				<select name="login" id="login"<?php if (count($serverNames) === 1) echo ' disabled="disabled"' ?>>
				<?php foreach ($serverNames as $serverName): ?>
					<option value="<?php echo htmlspecialchars($serverName) ?>"<?php if ($params->get('server') == $serverName) echo ' selected="selected"' ?>><?php echo htmlspecialchars($serverName) ?></option>
				<?php endforeach ?>
				</select>
			</td>
			<td><p><?php echo htmlspecialchars(Flux::message('ResetPassServerInfo')) ?></p></td>
		</tr>
		<?php endif ?>
		<tr>
			<th><label for="userid"><?php echo htmlspecialchars(Flux::message('ResetPassAccountLabel')) ?></label></th>
			<td><input type="text" name="userid" id="userid" /></td>
			<td><p><?php echo htmlspecialchars(Flux::message('ResetPassAccountInfo')) ?></p></td>
		</tr>
		<tr>
			<th><label for="email"><?php echo htmlspecialchars(Flux::message('ResetPassEmailLabel')) ?></label></th>
			<td><input type="text" name="email" id="email" /></td>
			<td><p><?php echo htmlspecialchars(Flux::message('ResetPassEmailInfo')) ?></p></td>
		</tr>
		<tr>
			<td colspan="2" align="right"><input type="submit" value="<?php echo htmlspecialchars(Flux::message('ResetPassButton')) ?>" /></td>
			<td></td>
		</tr>
	</table>
</form>
