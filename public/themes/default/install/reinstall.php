<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Re-Install Database Schemas</h2>
<p>You may re-install your database schema files (*.sql files) from this interface. If you are absolutely sure you want to proceed with this then click "continue".</p>
<p><strong>Note:</strong> By doing so, you may end up with duplicate indexes on your MySQL tables, but they are not harmful (this feature is highly experimental).</p>
<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
	<input type="hidden" name="reinstall" value="1" />
	<table class="generic-form-table">
		<tr>
			<td><p>Are you absolutely sure you want to continue?</p></td>
		</tr>
		<tr>
			<td><input type="submit" value="Continue" /></td>
		</tr>
	</table>
</form>
