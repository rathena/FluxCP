<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>PayPal Transactions</h2>
<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form action="<?php echo $this->url ?>" method="get" class="search-form">
	<?php echo $this->moduleActionFormInputs($params->get('module'), $params->get('action')) ?>
	<p>
		<label for="txn_id">Transaction ID:</label>
		<input type="text" name="txn_id" id="txn_id" value="<?php echo htmlspecialchars($params->get('txn_id')) ?>" />
		...
		<label for="parent_txn_id">Parent Transaction ID:</label>
		<input type="text" name="parent_txn_id" id="parent_txn_id" value="<?php echo htmlspecialchars($params->get('parent_txn_id')) ?>" />
		...
		<label for="status">Status:</label>
		<input type="text" name="status" id="status" value="<?php echo htmlspecialchars($params->get('status')) ?>" />
		...
		<label for="email">E-mail:</label>
		<input type="text" name="email" id="email" value="<?php echo htmlspecialchars($params->get('email')) ?>" />
	</p>
	<p>
		<label for="amount">Amount:</label>
		<select name="amount_op">
			<option value="eq"<?php if (($amount_op=$params->get('amount_op')) == 'eq') echo ' selected="selected"' ?>>is equal to</option>
			<option value="gt"<?php if ($amount_op == 'gt') echo ' selected="selected"' ?>>is greater than</option>
			<option value="lt"<?php if ($amount_op == 'lt') echo ' selected="selected"' ?>>is less than</option>
		</select>
		<input type="text" name="amount" id="amount" value="<?php echo htmlspecialchars($params->get('amount')) ?>" />
		...
		<label for="credits">Credits:</label>
		<select name="credits_op">
			<option value="eq"<?php if (($credits_op=$params->get('credits_op')) == 'eq') echo ' selected="selected"' ?>>is equal to</option>
			<option value="gt"<?php if ($credits_op == 'gt') echo ' selected="selected"' ?>>is greater than</option>
			<option value="lt"<?php if ($credits_op == 'lt') echo ' selected="selected"' ?>>is less than</option>
		</select>
		<input type="text" name="credits" id="credits" value="<?php echo htmlspecialchars($params->get('credits')) ?>" />
		...
		<label for="account">Account:</label>
		<input type="text" name="account" id="account" value="<?php echo htmlspecialchars($params->get('account')) ?>" />
	</p>
	<p>
		<label for="use_processed_after">Process Date Between:</label>
		<input type="checkbox" name="use_processed_after" id="use_processed_after"<?php if ($params->get('use_processed_after')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('processed_after') ?>
		<label for="use_processed_before">&mdash;</label>
		<input type="checkbox" name="use_processed_before" id="use_processed_before"<?php if ($params->get('use_processed_before')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('processed_before') ?>
	</p>
	<p>
		<label for="use_received_after">Receive Date Between:</label>
		<input type="checkbox" name="use_received_after" id="use_received_after"<?php if ($params->get('use_received_after')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('received_after') ?>
		<label for="use_received_before">&mdash;</label>
		<input type="checkbox" name="use_received_before" id="use_received_before"<?php if ($params->get('use_received_before')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('received_before') ?>
		
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>
<?php if ($transactions): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('p.txn_id', 'Transaction') ?></th>
		<th><?php echo $paginator->sortableColumn('p.parent_txn_id', 'Parent') ?></th>
		<th><?php echo $paginator->sortableColumn('p.process_date', 'Processed') ?></th>
		<th><?php echo $paginator->sortableColumn('p.payment_date', 'Received') ?></th>
		<th><?php echo $paginator->sortableColumn('p.payment_status', 'Status') ?></th>
		<th><?php echo $paginator->sortableColumn('p.payer_email', 'E-mail') ?></th>
		<th><?php echo $paginator->sortableColumn('p.mc_gross', 'Amount') ?></th>
		<th><?php echo $paginator->sortableColumn('p.credits', 'Credits') ?></th>
		<!--<th><?php echo $paginator->sortableColumn('server_name', 'Server') ?></th>-->
		<th><?php echo $paginator->sortableColumn('l.userid', 'Account') ?></th>
	</tr>
	<?php foreach ($transactions as $txn): ?>
	<tr>
		<td align="right">
			<strong>
				<?php if ($auth->actionAllowed('logdata', 'txnview')): ?>
					<a href="<?php echo $this->url($params->get('module'), 'txnview', array('id' => $txn->id)) ?>">
						<?php echo $txn->txn_id ?>
					</a>
				<?php else: ?>
					<?php echo $txn->txn_id ?>
				<?php endif ?>
			</strong>
		</td>
		<td>
			<?php if ($txn->parent_id): ?>
				<?php if ($auth->actionAllowed('logdata', 'txnview')): ?>
					<a href="<?php echo $this->url($params->get('module'), 'txnview', array('id' => $txn->parent_id)) ?>"><?php echo $txn->parent_txn_id ?></a>
				<?php else: ?>
					<?php echo $txn->parent_txn_id ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
		<td><?php echo $this->formatDateTime($txn->process_date) ?></td>
		<td><?php echo $this->formatDateTime($txn->payment_date) ?></td>
		<td><?php echo $txn->payment_status ?></td>
		<td>
			<?php if ($auth->actionAllowed('account', 'index')): ?>
				<?php echo $this->linkToAccountSearch(array('email' => $txn->payer_email), $txn->payer_email) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($txn->payer_email) ?>
			<?php endif ?>
		</td>
		<td><?php echo $txn->mc_gross ?> <?php echo $txn->mc_currency ?></td>
		<td><?php echo number_format((int)$txn->credits) ?></td>
		<!--<td><?php echo htmlspecialchars($txn->server_name) ?></td>-->
		<td>
			<?php if ($txn->account_id): ?>
				<?php if ($auth->actionAllowed('account', 'view') && $auth->allowedToViewAccount): ?>
					<?php echo $this->linkToAccount($txn->account_id, $txn->userid) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($txn->userid) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable">Unknown</span>
			<?php endif ?>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>There are currently no logged transactions.</p>
<?php endif ?>
