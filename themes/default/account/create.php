<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('AccountCreateHeading')) ?></h2>
<p><?php printf(htmlspecialchars(Flux::message('AccountCreateInfo')), '<a href="'.$this->url('service', 'tos').'">'.Flux::message('AccountCreateTerms').'</a>') ?></p>
<?php if (Flux::config('RequireEmailConfirm')): ?>
<p><strong>Note:</strong> You will need to provide a working e-mail address to confirm your account before you can log-in.</p>
<?php endif ?>
<p><strong>Note:</strong> <?php echo sprintf("Your password must be between %d and %d characters.", Flux::config('MinPasswordLength'), Flux::config('MaxPasswordLength')) ?></p>
<?php if (Flux::config('PasswordMinUpper') > 0): ?>
<p><strong>Note:</strong> <?php echo sprintf(Flux::message('PasswordNeedUpper'), Flux::config('PasswordMinUpper')) ?></p>
<?php endif ?>
<?php if (Flux::config('PasswordMinLower') > 0): ?>
<p><strong>Note:</strong> <?php echo sprintf(Flux::message('PasswordNeedLower'), Flux::config('PasswordMinLower')) ?></p>
<?php endif ?>
<?php if (Flux::config('PasswordMinNumber') > 0): ?>
<p><strong>Note:</strong> <?php echo sprintf(Flux::message('PasswordNeedNumber'), Flux::config('PasswordMinNumber')) ?></p>
<?php endif ?>
<?php if (Flux::config('PasswordMinSymbol') > 0): ?>
<p><strong>Note:</strong> <?php echo sprintf(Flux::message('PasswordNeedSymbol'), Flux::config('PasswordMinSymbol')) ?></p>
<?php endif ?>
<?php if (!Flux::config('AllowUserInPassword')): ?>
<p><strong>Note:</strong> <?php echo Flux::message('PasswordContainsUser') ?></p>
<?php endif ?>
<?php if (isset($errorMessage)): ?>
<p class="red" style="font-weight: bold"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<form action="<?php echo $this->url ?>" method="post" class="generic-form">
	<?php if (count($serverNames) === 1): ?>
	<input type="hidden" name="server" value="<?php echo htmlspecialchars($session->loginAthenaGroup->serverName) ?>">
	<?php endif ?>
	<table class="generic-form-table">
		<?php if (count($serverNames) > 1): ?>
		<tr>
			<th><label for="register_server"><?php echo htmlspecialchars(Flux::message('AccountServerLabel')) ?></label></th>
			<td>
				<select name="server" id="register_server"<?php if (count($serverNames) === 1) echo ' disabled="disabled"' ?>>
				<?php foreach ($serverNames as $serverName): ?>
					<option value="<?php echo htmlspecialchars($serverName) ?>"<?php if ($params->get('server') == $serverName) echo ' selected="selected"' ?>><?php echo htmlspecialchars($serverName) ?></option>
				<?php endforeach ?>
				</select>
			</td>
		</tr>
		<?php endif ?>

		<tr>
			<th><label for="register_username"><?php echo htmlspecialchars(Flux::message('AccountUsernameLabel')) ?></label></th>
			<td><input type="text" name="username" id="register_username" value="<?php echo htmlspecialchars($params->get('username') ?: '') ?>" /></td>
		</tr>

		<tr>
			<th><label for="register_password"><?php echo htmlspecialchars(Flux::message('AccountPasswordLabel')) ?></label></th>
			<td><input type="password" name="password" id="register_password" /></td>
		</tr>

		<tr>
			<th><label for="register_confirm_password"><?php echo htmlspecialchars(Flux::message('AccountPassConfirmLabel')) ?></label></th>
			<td><input type="password" name="confirm_password" id="register_confirm_password" /></td>
		</tr>

		<tr>
			<th><label for="register_email_address"><?php echo htmlspecialchars(Flux::message('AccountEmailLabel')) ?></label></th>
			<td><input type="text" name="email_address" id="register_email_address" value="<?php echo htmlspecialchars($params->get('email_address') ?: '') ?>" /></td>
		</tr>

		<tr>
			<th><label for="register_email_address"><?php echo htmlspecialchars(Flux::message('AccountEmailLabel2')) ?></label></th>
			<td><input type="text" name="email_address2" id="register_email_address2" value="<?php echo htmlspecialchars($params->get('email_address2') ?: '') ?>" /></td>
		</tr>

		<tr>
			<th><label><?php echo htmlspecialchars(Flux::message('AccountGenderLabel')) ?></label></th>
			<td>
				<p>
					<label><input type="radio" name="gender" id="register_gender_m" value="M"<?php if ($params->get('gender') === 'M') echo ' checked="checked"' ?> /> <?php echo $this->genderText('M') ?></label>
					<label><input type="radio" name="gender" id="register_gender_f" value="F"<?php if ($params->get('gender') === 'F') echo ' checked="checked"' ?> /> <?php echo $this->genderText('F') ?></label>
					<strong title="<?php echo htmlspecialchars(Flux::message('AccountCreateGenderInfo')) ?>">?</strong>
				</p>
			</td>
		</tr>

		<tr>
			<th><label><?php echo htmlspecialchars(Flux::message('AccountBirthdateLabel')) ?></label></th>
			<td><?php echo $this->dateField('birthdate',null,0) ?></td>
		</tr>

		<?php if (Flux::config('UseCaptcha')): ?>
		<tr>
			<?php if (Flux::config('ReCaptchaPublicKey') === '...' || Flux::config('ReCaptchaPrivateKey') === '...'): ?>
            <th><label for="register_security_code"><?php echo htmlspecialchars(Flux::message('AccountSecurityLabel')) ?></label></th>
			<td><div class="no-recaptcha" style="color: red"><?php echo htmlspecialchars(Flux::message('AccountRecaptchaKey')) ?></div></td>
			<?php else: ?>
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
			<?php endif ?>
		</tr>
		<?php endif ?>

		<tr>
			<td></td>
			<td>
				<div style="margin-bottom: 5px">
					<?php printf(htmlspecialchars(Flux::message('AccountCreateInfo2')), '<a href="'.$this->url('service', 'tos').'">'.Flux::message('AccountCreateTerms').'</a>') ?>
				</div>
				<div>
					<button type="submit"><strong><?php echo htmlspecialchars(Flux::message('AccountCreateButton')) ?></strong></button>
				</div>
			</td>
		</tr>
	</table>
</form>
