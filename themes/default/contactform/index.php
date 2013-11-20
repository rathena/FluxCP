<?php
/* Contact Form Addon
 * Created and maintained by Akkarin
 * Current Version: 1.00.01
 */
 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
?>
<?php if (isset($errorMessage)): ?>
<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>

<h2><?php echo htmlspecialchars(Flux::message('CFTitleSubmit')) ?></h2>
<form action="<?php echo $this->urlWithQs ?>" method="post">
	<table class="vertical-table" width="100%">
		<tr>
			<th>Account ID</th>
			<td><input type="text" name="account_id" id="account_id" value="<?php echo $session->account->account_id ?>" readonly="readonly" /></td>
		</tr>
		<tr>
			<th>Account Name</th>
			<td><input type="text" name="userid" id="userid" value="<?php echo $session->account->userid ?>" readonly="readonly" /></td>
		</tr>
		<tr>
			<th>From Email</th>
			<td><input type="text" name="email" id="email" value="<?php echo $session->account->email ?>" readonly="readonly" /></td>
		</tr>
		<tr>
			<th>Subject</th>
			<td><input type="text" name="subject" id="subject" size="50" /><br />Type a very brief description about this message.</td>
		</tr>
		<tr>
			<th>Main Body</th>
			<td>
				<textarea name="body"></textarea>
			</td>
		</tr>

		<tr>
			<td colspan="2"><input type="hidden" name="ip" value="<?php echo $_SERVER['REMOTE_ADDR'] ?>" /><input type="submit" value="Submit" /></td>
		</tr>
    </table>
</form>