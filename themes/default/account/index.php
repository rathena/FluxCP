<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Accounts</h2>
<p class="toggler"><a href="javascript:toggleSearchForm()"><?php echo htmlspecialchars(Flux::message('SearchLabel')) ?></a></p>
<form action="<?php echo $this->url ?>" method="get" class="search-form">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="account_id"><?php echo htmlspecialchars(Flux::message('AccountIdLabel')) ?>:</label>
		<input type="text" name="account_id" id="account_id" value="<?php echo htmlspecialchars($params->get('account_id') ?: '') ?>" />
		...
		<label for="username"><?php echo htmlspecialchars(Flux::message('UsernameLabel')) ?>:</label>
		<input type="text" name="username" id="username" value="<?php echo htmlspecialchars($params->get('username') ?: '') ?>" />
		<?php if ($searchPassword): ?>
		...
		<label for="password"><?php echo htmlspecialchars(Flux::message('PasswordLabel')) ?>:</label>
		<input type="text" name="password" id="password" value="<?php echo htmlspecialchars($params->get('password') ?: '') ?>" />
		<?php endif ?>
		...
		<label for="email"><?php echo htmlspecialchars(Flux::message('EmailAddressLabel')) ?>:</label>
		<input type="text" name="email" id="email" value="<?php echo htmlspecialchars($params->get('email') ?: '') ?>" />
		...
		<label for="last_ip"><?php echo htmlspecialchars(Flux::message('LastUsedIpLabel')) ?>:</label>
		<input type="text" name="last_ip" id="last_ip" value="<?php echo htmlspecialchars($params->get('last_ip') ?: '') ?>" />
		...
		<label for="gender"><?php echo htmlspecialchars(Flux::message('GenderLabel')) ?>:</label>
		<select name="gender" id="gender">
			<option value=""<?php if (!in_array($gender=$params->get('gender'), array('M', 'F'))) echo ' selected="selected"' ?>><?php echo htmlspecialchars(Flux::message('AllLabel')) ?></option>
			<option value="M"<?php if ($gender == 'M') echo ' selected="selected"' ?>><?php echo $this->genderText('M') ?></option>
			<option value="F"<?php if ($gender == 'F') echo ' selected="selected"' ?>><?php echo $this->genderText('F') ?></option>
		</select>
	</p>
	<p>
		<label for="account_state"><?php echo htmlspecialchars(Flux::message('AccountStateLabel')) ?>:</label>
		<select name="account_state" id="account_state">
			<option value=""<?php if (!($account_state=$params->get('account_state'))) echo ' selected="selected"' ?>><?php echo htmlspecialchars(Flux::message('AllLabel')) ?></option>
			<option value="normal"<?php if ($account_state == 'normal') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Flux::message('AccountStateNormal')) ?></option>
			<option value="pending"<?php if ($account_state == 'pending') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Flux::message('AccountStatePending')) ?></option>
			<option value="banned"<?php if ($account_state == 'banned') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Flux::message('AccountStateTempBanLbl')) ?></option>
			<option value="permabanned"<?php if ($account_state == 'permabanned') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Flux::message('AccountStatePermBanned')) ?></option>
		</select>
		...
		<label for="account_group_id"><?php echo htmlspecialchars(Flux::message('AccountGroupIDLabel')) ?>:</label>
		<select name="account_group_id_op">
			<option value="eq"<?php if (($account_group_id_op=$params->get('account_group_id_op')) == 'eq') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Flux::message('IsEqualToLabel')) ?></option>
			<option value="gt"<?php if ($account_group_id_op == 'gt') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Flux::message('IsGreaterThanLabel')) ?></option>
			<option value="lt"<?php if ($account_group_id_op == 'lt') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Flux::message('IsLessThanLabel')) ?></option>
		</select>
		<input type="text" name="account_group_id" id="account_group_id" value="<?php echo htmlspecialchars($params->get('account_group_id') ?: '') ?>" />
		...
		<label for="balance"><?php echo htmlspecialchars(Flux::message('CreditBalanceLabel')) ?>:</label>
		<select name="balance_op">
			<option value="eq"<?php if (($balance_op=$params->get('balance_op')) == 'eq') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Flux::message('IsEqualToLabel')) ?></option>
			<option value="gt"<?php if ($balance_op == 'gt') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Flux::message('IsGreaterThanLabel')) ?></option>
			<option value="lt"<?php if ($balance_op == 'lt') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Flux::message('IsLessThanLabel')) ?></option>
		</select>
		<input type="text" name="balance" id="balance" value="<?php echo htmlspecialchars($params->get('balance') ?: '') ?>" />
	</p>
	<p>
		<label for="logincount"><?php echo htmlspecialchars(Flux::message('LoginCountLabel')) ?>:</label>
		<select name="logincount_op">
			<option value="eq"<?php if (($logincount_op=$params->get('logincount_op')) == 'eq') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Flux::message('IsEqualToLabel')) ?></option>
			<option value="gt"<?php if ($logincount_op == 'gt') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Flux::message('IsGreaterThanLabel')) ?></option>
			<option value="lt"<?php if ($logincount_op == 'lt') echo ' selected="selected"' ?>><?php echo htmlspecialchars(Flux::message('IsLessThanLabel')) ?></option>
		</select>
		<input type="text" name="logincount" id="logincount" value="<?php echo htmlspecialchars($params->get('logincount') ?: '') ?>" />
		...
		<label for="use_birthdate_after"><?php echo htmlspecialchars(Flux::message('BirthdateBetweenLabel')) ?>:</label>
		<input type="checkbox" name="use_birthdate_after" id="use_birthdate_after"<?php if ($params->get('use_birthdate_after')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('birthdate_after') ?>
		<label for="use_birthdate_before">&mdash;</label>
		<input type="checkbox" name="use_birthdate_before" id="use_birthdate_before"<?php if ($params->get('use_birthdate_before')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('birthdate_before') ?>
	</p>
	<p>
		<label for="use_last_login_after"><?php echo htmlspecialchars(Flux::message('LoginBetweenLabel')) ?>:</label>
		<input type="checkbox" name="use_last_login_after" id="use_last_login_after"<?php if ($params->get('use_last_login_after')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('last_login_after') ?>
		<label for="use_last_login_before">&mdash;</label>
		<input type="checkbox" name="use_last_login_before" id="use_last_login_before"<?php if ($params->get('use_last_login_before')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('last_login_before') ?>		
		
		<input type="submit" value="<?php echo htmlspecialchars(Flux::message('SearchButton')) ?>" />
		<input type="button" value="<?php echo htmlspecialchars(Flux::message('ResetButton')) ?>" onclick="reload()" />
	</p>
</form>
<?php if ($accounts): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('login.account_id', Flux::message('AccountIdLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('login.userid', Flux::message('UsernameLabel')) ?></th>
		<?php if ($showPassword): ?><th><?php echo $paginator->sortableColumn('login.user_pass', Flux::message('PasswordLabel')) ?></th><?php endif ?>
		<th><?php echo $paginator->sortableColumn('login.sex', Flux::message('GenderLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('group_id', Flux::message('AccountGroupIDLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('state', Flux::message('AccountStateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('balance', Flux::message('CreditBalanceLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('login.email', Flux::message('EmailAddressLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('logincount', Flux::message('LoginCountLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('birthdate', Flux::message('AccountBirthdateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('lastlogin', Flux::message('LastLoginDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('last_ip', Flux::message('LastUsedIpLabel')) ?></th>
		<!-- <th><?php echo $paginator->sortableColumn('reg_date', 'Register Date') ?></th> -->
	</tr>
	<?php foreach ($accounts as $account): ?>
	<tr>
		<td align="right">
			<?php if ($auth->actionAllowed('account', 'view') && $auth->allowedToViewAccount): ?>
				<?php echo $this->linkToAccount($account->account_id, $account->account_id) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($account->account_id) ?>
			<?php endif ?>
		</td>
		<td><?php echo htmlspecialchars($account->userid) ?></td>
		<?php if ($showPassword): ?><td><?php echo htmlspecialchars($account->user_pass) ?></td><?php endif ?>
		<td>
			<?php if ($gender = $this->genderText($account->sex)): ?>
				<?php echo htmlspecialchars($gender) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td><?php echo (int)$account->group_id ?></td>
		<td>
			<?php if (!$account->confirmed && $account->confirm_code): ?>
				<span class="account-state state-pending">
					<?php echo htmlspecialchars(Flux::message('AccountStatePending')) ?>
				</span>
			<?php elseif (($state = $this->accountStateText($account->state)) && !$account->unban_time): ?>
				<?php echo $state ?>
			<?php elseif ($account->unban_time): ?>
				<span class="account-state state-banned">
					<?php echo htmlspecialchars(sprintf(Flux::message('AccountStateTempBanned'), date(Flux::config('DateTimeFormat'), $account->unban_time))) ?>
				</span>
			<?php else: ?>
				<span class="account-state state-unknown"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td><?php echo number_format((int)$account->balance) ?></td>
		<td>
			<?php if ($account->email): ?>
				<?php echo $this->linkToAccountSearch(array('email' => $account->email), $account->email) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td><?php echo number_format((int)$account->logincount) ?></td>
		<td><?php echo $account->birthdate ?></td>
		<td>
			<?php if (!$account->lastlogin || $account->lastlogin <= '1000-01-01 00:00:00'): ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NeverLabel')) ?></span>
			<?php else: ?>
				<?php echo $this->formatDateTime($account->lastlogin) ?>
			<?php endif ?>
		</td>
		<td>
			<?php if ($account->last_ip): ?>
				<?php echo $this->linkToAccountSearch(array('last_ip' => $account->last_ip), $account->last_ip) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<!-- <td>
			<?php if (!$account->reg_date || $account->reg_date <= '1000-01-01 00:00:00'): ?>
				<span class="not-applicable">Unknown</span>
			<?php else: ?>
				<?php echo $this->formatDateTime($account->reg_date) ?>
			<?php endif ?>
		</td> -->
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('AccountIndexNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
