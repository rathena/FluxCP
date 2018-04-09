<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('AccountEditHeading')) ?></h2>
<?php if ($account): ?>
	<?php if (!empty($errorMessage)): ?>
		<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
	<?php endif ?>
	<form action="<?php echo $this->urlWithQs ?>" method="post">
		<table class="vertical-table">
			<tr>
				<th><?php echo htmlspecialchars(Flux::message('UsernameLabel')) ?></th>
				<td><?php echo $account->userid ?></td>
				<th><?php echo htmlspecialchars(Flux::message('AccountIdLabel')) ?></th>
				<td><?php echo $account->account_id ?></td>
			</tr>
			<tr>
				<th><label for="email"><?php echo htmlspecialchars(Flux::message('EmailAddressLabel')) ?></label></th>
				<td><input type="text" name="email" id="email" value="<?php echo htmlspecialchars($account->email) ?>" /></td>
				<?php if ($auth->allowedToEditAccountGroupID && !$isMine): ?>
					<th><label for="group_id"><?php echo htmlspecialchars(Flux::message('AccountGroupIDLabel')) ?></label></th>
					<td><input type="text" name="group_id" id="group_id" value="<?php echo (int)$account->group_id ?>" /></td>
				<?php else: ?>
					<th><?php echo htmlspecialchars(Flux::message('AccountGroupIDLabel')) ?></th>
					<td>
						<input type="hidden" name="group_id" value="<?php echo (int)$account->group_id ?>" />
						<?php echo number_format((int)$account->group_id) ?>
					</td>
				<?php endif ?>
			</tr>
			<tr>
				<th><label for="gender"><?php echo htmlspecialchars(Flux::message('GenderLabel')) ?></label></th>
				<td>
					<select name="gender" id="gender">
						<option value="M"<?php if ($account->sex == 'M') echo ' selected="selected"' ?>><?php echo $this->genderText('M') ?></option>
						<option value="F"<?php if ($account->sex == 'F') echo ' selected="selected"' ?>><?php echo $this->genderText('F') ?></option>
					</select>
				</td>
				<th><?php echo htmlspecialchars(Flux::message('AccountStateLabel')) ?></th>
				<td>
					<?php if (($state = $this->accountStateText($account->state)) && !$account->unban_time): ?>
						<?php echo $state ?>
					<?php elseif ($account->unban_time): ?>
						<span class="account-state state-banned">
							Banned Until
							<?php echo htmlspecialchars(date(Flux::config('DateTimeFormat'), $account->unban_time)) ?>
						</span>
					<?php else: ?>
						<span class="account-state state-unknown"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
					<?php endif ?>
				</td>
			</tr>
			<tr>
				<th><label for="logincount"><?php echo htmlspecialchars(Flux::message('LoginCountLabel')) ?></label></th>
				<td><input type="text" name="logincount" id="logincount" value="<?php echo (int)$account->logincount ?>" /></td>
				<?php if ($auth->allowedToEditAccountBalance): ?>
					<th><label for="balance"><?php echo htmlspecialchars(Flux::message('CreditBalanceLabel')) ?></label></th>
					<td><input type="text" name="balance" id="balance" value="<?php echo (int)$account->balance ?>" /></td>
				<?php else: ?>
					<th><?php echo htmlspecialchars(Flux::message('CreditBalanceLabel')) ?></th>
					<td><?php echo number_format((int)$account->balance) ?></td>
				<?php endif ?>
			</tr>
			<tr>
				<th><label for="use_birthdate"><?php echo htmlspecialchars(Flux::message('AccountBirthdateLabel')) ?></label></th>
				<td colspan="3">
					<input type="checkbox" name="use_birthdate" id="use_birthdate" />
					<?php echo $this->dateField('birthdate', $account->birthdate) ?>
				</td>
			</tr>
			<tr>
				<th><label for="use_lastlogin"><?php echo htmlspecialchars(Flux::message('LastLoginDateLabel')) ?></label></th>
				<td colspan="3">
					<input type="checkbox" name="use_lastlogin" id="use_lastlogin" />
					<?php echo $this->dateTimeField('lastlogin', $account->lastlogin) ?>
				</td>
			</tr>
			<tr>
				<th><label for="use_vip_time"><?php echo htmlspecialchars(Flux::message('VIPTimeDateLabel')) ?></label></th>
				<td colspan="3">
					You will need to login via the client to change the VIP time.
				</td>
			</tr>
			<tr>
				<th><label for="last_ip"><?php echo htmlspecialchars(Flux::message('LastUsedIpLabel')) ?></label></th>
				<td colspan="3"><input type="text" name="last_ip" id="last_ip" value="<?php echo htmlspecialchars($account->last_ip) ?>" /></td>
			</tr>
			<tr>
				<td colspan="4" align="right">
					<input type="submit" value="<?php echo htmlspecialchars(Flux::message('AccountEditButton')) ?>" />
				</td>
			</tr>
		</table>
	</form>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('AccountEditNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
