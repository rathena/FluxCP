<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('PasswordChangeHeading')) ?></h2>
<?php if (!empty($errorMessage)): ?>
	<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php else: ?>
	<p><?php echo htmlspecialchars(Flux::message('PasswordChangeInfo')) ?></p>
<?php endif ?>
<br />
<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
	<table class="generic-form-table">
		<tr>
			<th><label for="currentpass"><?php echo htmlspecialchars(Flux::message('CurrentPasswordLabel')) ?></label></th>
			<td><input type="password" name="currentpass" id="currentpass" value="" /></td>
			<td rowspan="3">
				<p><?php echo htmlspecialchars(Flux::message('PasswordChangeNote')) ?></p>
				<p class="important"><?php echo htmlspecialchars(Flux::message('PasswordChangeNote2')) ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="newpass"><?php echo htmlspecialchars(Flux::message('NewPasswordLabel')) ?></label></th>
			<td><input type="password" name="newpass" id="newpass" value="" /></td>
		</tr>
		<tr>
			<th><label for="confirmnewpass"><?php echo htmlspecialchars(Flux::message('NewPasswordConfirmLabel')) ?></label></th>
			<td><input type="password" name="confirmnewpass" id="confirmnewpass" value="" /></td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				<input type="submit" value="<?php echo htmlspecialchars(Flux::message('PasswordChangeButton')) ?>" />
			</td>
		</tr>
	</table>
</form>
