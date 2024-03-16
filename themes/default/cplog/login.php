<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Logins</h2>
<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form action="<?php echo $this->url ?>" method="get" class="search-form">
	<?php echo $this->moduleActionFormInputs($params->get('module'), $params->get('action')) ?>
	<p>
		<label for="use_login_after">Login Date Between:</label>
		<input type="checkbox" name="use_login_after" id="use_login_after"<?php if ($params->get('use_login_after')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('login_after') ?>
		<label for="use_login_before">&mdash;</label>
		<input type="checkbox" name="use_login_before" id="use_login_before"<?php if ($params->get('use_login_before')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('login_before') ?>
		<?php if ($auth->allowedToSearchCpLoginLogPw): ?>
		...
		<label for="password">Password:</label>
		<input type="text" name="password" id="password" value="<?php echo htmlspecialchars($params->get('password') ?: '') ?>" />
		<?php endif ?>
	</p>
	<p>
		<label for="account_id">Account ID:</label>
		<input type="text" name="account_id" id="account_id" value="<?php echo htmlspecialchars($params->get('account_id') ?: '') ?>" />
		...
		<label for="username">Username:</label>
		<input type="text" name="username" id="username" value="<?php echo htmlspecialchars($params->get('username') ?: '') ?>" />
		...
		<label for="ip">IP Address:</label>
		<input type="text" name="ip" id="ip" value="<?php echo htmlspecialchars($params->get('ip') ?: '') ?>" />
		...
		<label for="error_code">Error Code:</label>
		<select name="error_code" id="error_code">
			<option value="all"<?php if (is_null($params->get('error_code')) || strtolower($params->get('error_code') == 'all')) echo ' selected="selected"' ?>>All</option>
			<option value="none"<?php if (strtolower($params->get('error_code')) == 'none') echo ' selected="selected"' ?>>None</option>
		<?php foreach ($loginErrors->toArray() as $errorCode => $errorType): ?>
			<option value="<?php echo $errorCode ?>"<?php if (ctype_digit($params->get('error_code')) && $params->get('error_code') == $errorCode) echo ' selected="selected"' ?>><?php echo htmlspecialchars($errorType) ?></option>
		<?php endforeach ?>
		</select>
		
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>
<?php if ($logins): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('account_id', 'Account ID') ?></th>
		<th><?php echo $paginator->sortableColumn('username', 'Username') ?></th>
		<?php if (($showPassword=Flux::config('CpLoginLogShowPassword')) && ($seePassword=$auth->allowedToSeeCpLoginLogPass)): ?>
		<th><?php echo $paginator->sortableColumn('password', 'Password') ?></th>
		<?php endif ?>
		<th><?php echo $paginator->sortableColumn('ip', 'IP Address') ?></th>
		<th><?php echo $paginator->sortableColumn('login_date', 'Login Date') ?></th>
		<th><?php echo $paginator->sortableColumn('error_code', 'Error Code') ?></th>
	</tr>
	<?php foreach ($logins as $login): ?>
	<tr>
		<td align="right">
			<?php if ($auth->actionAllowed('account', 'view') && $auth->allowedToViewAccount): ?>
				<?php echo $this->linkToAccount($login->account_id, $login->account_id) ?>
			<?php else: ?>
				<?php echo $login->account_id ?>
			<?php endif ?>
		</td>
		<td><?php echo htmlspecialchars($login->username) ?></td>
		<?php if ($showPassword && $seePassword): ?>
		<td><?php echo htmlspecialchars($login->password) ?></td>
		<?php endif ?>
		<td>
			<?php if ($auth->actionAllowed('account', 'index')): ?>
				<?php echo $this->linkToAccountSearch(array('last_ip' => $login->ip), $login->ip) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($login->ip) ?>
			<?php endif ?>
		</td>
		<td><?php echo $this->formatDateTime($login->login_date) ?></td>
		<td>
			<?php if (!is_null($login->error_code)): ?>
				<?php echo $login->error_type ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>No logs were found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
