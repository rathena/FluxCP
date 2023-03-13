<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('LoginHeading')) ?></h2>
<?php if (isset($errorMessage)): ?>
<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php else: ?>

<?php if ($auth->actionAllowed('account', 'create')): ?>
<p><?php printf(Flux::message('LoginPageMakeAccount'), $this->url('account', 'create')); ?></p>
<?php endif ?>

<?php endif ?>
<form action="<?php echo $this->url('account', 'login', array('return_url' => $params->get('return_url'))) ?>" method="post" class="generic-form">
	<?php if (count($serverNames) === 1): ?>
	<input type="hidden" name="server" value="<?php echo htmlspecialchars($session->loginAthenaGroup->serverName) ?>">
	<?php endif ?>
	<table class="generic-form-table">
		<tr>
			<th><label for="login_username"><?php echo htmlspecialchars(Flux::message('AccountUsernameLabel')) ?></label></th>
			<td><input type="text" name="username" id="login_username" value="<?php echo htmlspecialchars($params->get('username') ?: '') ?>" /></td>
		</tr>
		<tr>
			<th><label for="login_password"><?php echo htmlspecialchars(Flux::message('AccountPasswordLabel')) ?></label></th>
			<td><input type="password" name="password" id="login_password" /></td>
		</tr>
		<?php if (count($serverNames) > 1): ?>
		<tr>
			<th><label for="login_server"><?php echo htmlspecialchars(Flux::message('AccountServerLabel')) ?></label></th>
			<td>
				<select name="server" id="login_server"<?php if (count($serverNames) === 1) echo ' disabled="disabled"' ?>>
					<?php foreach ($serverNames as $serverName): ?>
					<option value="<?php echo htmlspecialchars($serverName) ?>"><?php echo htmlspecialchars($serverName) ?></option>
					<?php endforeach ?>
				</select>
			</td>
		</tr>
		<?php endif ?>
		<?php if (Flux::config('UseLoginCaptcha')): ?>
		<tr>
			<?php if (Flux::config('EnableReCaptcha')): ?>
			<th><label for="register_security_code"><?php echo htmlspecialchars(Flux::message('AccountSecurityLabel')) ?></label></th>
			<td><div class="g-recaptcha" data-theme = "<?php echo $theme;?>" data-sitekey="<?php echo $recaptcha ?>"></div></td>
			<?php else: ?>
			<th><label for="register_security_code"><?php echo htmlspecialchars(Flux::message('AccountSecurityLabel')) ?></label></th>
			<td>
				<div class="security-code">
					<img src="<?php echo $this->url('captcha') ?>" />
				</div>
				<input type="text" name="security_code" id="register_security_code" />
				<div style="font-size: smaller;" class="action">
					<strong><a href="javascript:refreshSecurityCode('.security-code img')"><?php echo htmlspecialchars(Flux::message('RefreshSecurityCode')) ?></a></strong>
				</div>
			</td>
			<?php endif ?>
		</tr>
		<?php endif ?>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="<?php echo htmlspecialchars(Flux::message('LoginButton')) ?>" />
			</td>
		</tr>
	</table>
</form>
