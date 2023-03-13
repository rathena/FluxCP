<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>E-mail Changes</h2>
<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form action="<?php echo $this->url ?>" method="get" class="search-form">
	<?php echo $this->moduleActionFormInputs($params->get('module'), $params->get('action')) ?>
	<p>
		<label for="use_request_after">Request Date Between:</label>
		<input type="checkbox" name="use_request_after" id="use_request_after"<?php if ($params->get('use_request_after')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('request_after') ?>
		<label for="use_request_before">&mdash;</label>
		<input type="checkbox" name="use_request_before" id="use_request_before"<?php if ($params->get('use_request_before')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('request_before') ?>
	</p>
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
		<input type="text" name="account_id" id="account_id" value="<?php echo htmlspecialchars($params->get('account_id') ?: '') ?>" />
		...
		<label for="username">Username:</label>
		<input type="text" name="username" id="username" value="<?php echo htmlspecialchars($params->get('username') ?: '') ?>" />
		...
		<label for="request_ip">Request IP:</label>
		<input type="text" name="request_ip" id="request_ip" value="<?php echo htmlspecialchars($params->get('request_ip') ?: '') ?>" />
		...
		<label for="change_ip">Change IP:</label>
		<input type="text" name="change_ip" id="change_ip" value="<?php echo htmlspecialchars($params->get('change_ip') ?: '') ?>" />
	</p>
	<p>
		<label for="old_email">Old Email:</label>
		<input type="text" name="old_email" id="old_email" value="<?php echo htmlspecialchars($params->get('old_email') ?: '') ?>" />
		...
		<label for="new_email">New Email:</label>
		<input type="text" name="new_email" id="new_email" value="<?php echo htmlspecialchars($params->get('new_email') ?: '') ?>" />
		
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>
<?php if ($changes): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('log.account_id', 'Account ID') ?></th>
		<th><?php echo $paginator->sortableColumn('userid', 'Username') ?></th>
		<th><?php echo $paginator->sortableColumn('old_email', 'Old Email') ?></th>
		<th><?php echo $paginator->sortableColumn('new_email', 'New Email') ?></th>
		<th><?php echo $paginator->sortableColumn('request_date', 'Request Date') ?></th>
		<th><?php echo $paginator->sortableColumn('request_ip', 'Request IP') ?></th>
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
		<td>
		<?php if ($auth->actionAllowed('account', 'index')): ?>
			<?php echo $this->linkToAccountSearch(array('email' => $change->old_email), $change->old_email) ?>
		<?php else: ?>
			<?php echo htmlspecialchars($change->old_email) ?>
		<?php endif ?>
		</td>
		<td>
		<?php if ($auth->actionAllowed('account', 'index')): ?>
			<?php echo $this->linkToAccountSearch(array('email' => $change->new_email), $change->new_email) ?>
		<?php else: ?>
			<?php echo htmlspecialchars($change->new_email) ?>
		<?php endif ?>
		</td>
		<td><?php echo $this->formatDateTime($change->request_date) ?></td>
		<td>
			<?php if ($auth->actionAllowed('account', 'index')): ?>
				<?php echo $this->linkToAccountSearch(array('last_ip' => $change->request_ip), $change->request_ip) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($change->request_ip) ?>
			<?php endif ?>
		</td>
		<td>
			<?php if ($change->change_date): ?>
				<?php echo $this->formatDateTime($change->change_date) ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($change->change_ip): ?>
				<?php if ($auth->actionAllowed('account', 'index')): ?>
					<?php echo $this->linkToAccountSearch(array('last_ip' => $change->change_ip), $change->change_ip) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($change->change_ip) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>No email changes found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
