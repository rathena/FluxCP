<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Re-Install Database Schemas</h2>
<p>You may re-install your database schema files (*.sql files) from this interface. If you are absolutely sure you want to proceed with this then click "continue".</p>
<p><strong>Note:</strong> By doing so, you may end up with duplicate indexes on your MySQL tables, but they are not harmful (this feature is highly experimental).</p>
<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
	<input type="hidden" name="reinstall" value="1" />
	<table class="generic-form-table">
		<tr>
			<td colspan="2" style="height:100px; line-height:0;"></td>
		</tr>
		<tr>
			<td><p><b>Are you absolutely sure you want to continue?</b></p></td>
			<td><p>Please enter your <em>installer password</em> to continue with the re-install.</p></td>
		</tr>
		<tr>
			<td>
				<label for="installer_password" title="Installer password">Password:</label>
				<input class="form-control" type="password" id="installer_password" name="installer_password" required />
				<button type="submit" class="btn btn-success">Authenticate</button>
			</td>
		</tr>
		<tr>
			<td colspan="2"><p style="color:red;"><b><em><?php echo $params->get('errorMessage') ?></em></b></p></td>
		</tr>
	</table>
</form>
