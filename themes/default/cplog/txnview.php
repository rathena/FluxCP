<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Viewing PayPal Transaction Details</h2>
<?php if ($txn): ?>
<p>If the transaction contains negative payment and settle amounts, it is likely there was a chargeback and the donor was reimbursed.</p>
<table class="vertical-table">
	<tr>
		<th>Transaction ID</th>
		<td><?php echo htmlspecialchars($txn->txn_id) ?></td>
		<th>Account</th>
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
		<th>Credits Earned</th>
		<td><?php echo number_format((int)$txn->credits) ?></td>
	</tr>
	<tr>
		<th>Amount</th>
		<td>
			<?php echo $txn->mc_gross ?>
			<?php echo $txn->mc_currency ?>
		</td>
		<th>Settle Amount</th>
		<td colspan="3">
			<?php echo $txn->mc_gross - $txn->mc_fee ?>
			<?php echo $txn->mc_currency ?>
		</td>
	</tr>
	<tr>
		<th>Payment Date</th>
		<td><?php echo htmlspecialchars(date(Flux::config('DateTimeFormat'), strtotime($txn->payment_date))) ?></td>
		<th>Date Processed</th>
		<td colspan="3"><?php echo $this->formatDateTime($txn->process_date) ?></td>
	</tr>
	<tr>
		<th>Status</th>
		<td><?php echo htmlspecialchars($txn->payment_status) ?></td>
		<th>Item Name</th>
		<td colspan="3"><?php echo htmlspecialchars($txn->item_name) ?></td>
	</tr>
	<tr>
		<th>First Name</th>
		<td><?php echo htmlspecialchars($txn->first_name) ?></td>
		<th rowspan="2">Address</th>
		<td colspan="3" rowspan="2">
			<?php echo htmlspecialchars($txn->address_street) ?><br />
			<?php echo htmlspecialchars($txn->address_city) ?>,
			<?php echo htmlspecialchars($txn->address_state) ?>,
			<?php echo htmlspecialchars($txn->address_country) ?>
			<?php echo htmlspecialchars($txn->address_zip) ?>
		</td>
	</tr>
	<tr>
		<th>Last Name</th>
		<td><?php echo htmlspecialchars($txn->last_name) ?></td>
	</tr>
</table>
<?php if ($auth->allowedToViewRawTxnLogData): ?>
	<h3>Raw Transaction Log</h3>
	<?php if ($txnFileLog): ?>
	<pre class="raw-txn-log"><?php echo htmlspecialchars($txnFileLog) ?></pre>
	<?php else: ?>
	<p>The raw log for this transaction could not be found.</p>
	<?php endif ?>	

	<?php else: ?>
	<p>Records indicate that such a transaction was never recorded. <a href="javascript:history.go(-1)">Go back</a>.</p>
	<?php endif ?>
<?php endif ?>
