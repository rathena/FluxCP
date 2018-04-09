<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Password Changes</h2>
<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form action="<?php echo $this->url ?>" method="get" class="search-form">
	<?php echo $this->moduleActionFormInputs($params->get('module'), $params->get('action')) ?>
	<p>
		<label for="use_change_after">Change Date Between:</label>
		<input type="checkbox" name="use_change_after" id="use_change_after"<?php if ($params->get('use_change_after')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('change_after') ?>
		<label for="use_change_before">&mdash;</label>
		<input type="checkbox" name="use_change_before" id="use_change_before"<?php if ($params->get('use_change_before')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('change_before') ?>
	</p>
	<p>
		<label for="account_id">Account ID:</label>
		<input type="text" name="account_id" id="account_id" value="<?php echo htmlspecialchars($params->get('account_id')) ?>" />
		...
		<label for="username">Username:</label>
		<input type="text" name="username" id="username" value="<?php echo htmlspecialchars($params->get('username')) ?>" />
		...
		<label for="change_ip">Change IP:</label>
		<input type="text" name="change_ip" id="change_ip" value="<?php echo htmlspecialchars($params->get('change_ip')) ?>" />
		
		<?php if (!$auth->allowedToSearchCpChangePass): ?>
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
		<?php endif ?>
	</p>
	<?php if ($auth->allowedToSearchCpChangePass): ?>
	<p>
		<label for="old_password">Old Password:</label>
		<input type="text" name="old_password" id="old_password" value="<?php echo htmlspecialchars($params->get('old_password')) ?>" />
		...
		<label for="new_password">New Password:</label>
		<input type="text" name="new_password" id="new_password" value="<?php echo htmlspecialchars($params->get('new_password')) ?>" />
		
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
	<?php endif ?>
</form>
<?php if ($changes): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('log.account_id', 'Account ID') ?></th>
		<th><?php echo $paginator->sortableColumn('userid', 'Username') ?></th>
		<?php if (Flux::config('CpChangeLogShowPassword') && $auth->allowedToSeeCpChangePass): ?>
		<th><?php echo $paginator->sortableColumn('old_password', 'Old Password') ?></th>
		<th><?php echo $paginator->sortableColumn('new_password', 'New Password') ?></th>
		<?php endif ?>
		<th><?php echo $paginator->sortableColumn('change_date', 'Change Date') ?></th>
		<th><?php echo $paginator->sortableColumn('change_ip', 'Change IP') ?></th>
	</tr>
	<?php foreach ($changes as $change): ?>
	<tr>
		<td align="right">
			<?php if ($auth->actionAllowed('account', 'view')): ?>
				<?php echo $this->linkToAccount($change->account_id, $change->account_id) ?>
			<?php else: ?>
				<?php echo $change->account_id ?>
			<?php endif ?>
		</td>
		<td>
			<?php if ($change->userid): ?>
				<?php echo htmlspecialchars($change->userid) ?>
			<?php else: ?>
				<span class="not-applicable">Unknown</span>
			<?php endif ?>
		</td>
		<?php if (Flux::config('CpChangeLogShowPassword') && $auth->allowedToSeeCpChangePass): ?>
		<td><?php echo htmlspecialchars($change->old_password) ?></td>
		<td><?php echo htmlspecialchars($change->new_password) ?></td>
		<?php endif ?>
		<td><?php echo $this->formatDateTime($change->change_date) ?></td>
		<td>
			<?php if ($auth->actionAllowed('account', 'index')): ?>
				<?php echo $this->linkToAccountSearch(array('last_ip' => $change->change_ip), $change->change_ip) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($change->change_ip) ?>
			<?php endif ?>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>No password changes found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
