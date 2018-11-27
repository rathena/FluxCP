<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Donation History</h2>
<h3>Transactions: Completed</h3>
<?php if ($completedTxn): ?>
<p>You have <?php echo number_format($completedTotal) ?> completed transaction(s).</p>
<table class="vertical-table">
	<tr>
		<th>Transaction</th>
		<th>Payment Date</th>
		<th>E-mail</th>
		<th>Amount</th>
		<th>Currency</th>
		<th>Credits</th>
	</tr>
	<?php foreach ($completedTxn as $txn): ?>
	<tr>
		<td><?php echo htmlspecialchars($txn->txn_id) ?></td>
		<td><?php echo $this->formatDateTime($txn->payment_date) ?></td>
		<td><?php echo htmlspecialchars($txn->payer_email) ?></td>
		<td><?php echo htmlspecialchars($txn->mc_gross) ?></td>
		<td><?php echo htmlspecialchars($txn->mc_currency) ?></td>
		<td><?php echo number_format($txn->credits) ?></td>
	</tr>
	<?php endforeach ?>
</table>
<?php else: ?>
<p>You have no completed transactions.</p>
<?php endif ?>

<h3>Transactions: Held</h3>
<?php if ($heldTxn): ?>
<p>You have <?php echo number_format($heldTotal) ?> held transaction(s).</p>
<table class="vertical-table">
	<tr>
		<th>Transaction</th>
		<th>Payment Date</th>
		<th>E-mail</th>
		<th>Amount</th>
		<th>Currency</th>
		<th>Credits</th>
	</tr>
	<?php foreach ($heldTxn as $txn): ?>
	<tr>
		<td><?php echo htmlspecialchars($txn->txn_id) ?></td>
		<td><?php echo $this->formatDateTime($txn->payment_date) ?></td>
		<td><?php echo htmlspecialchars($txn->payer_email) ?></td>
		<td><?php echo htmlspecialchars($txn->mc_gross) ?></td>
		<td><?php echo htmlspecialchars($txn->mc_currency) ?></td>
		<td><?php echo number_format($txn->credits) ?></td>
	</tr>
	<tr>
		<td colspan="6">
			↳ Hold Until:
			<strong><?php echo $this->formatDateTime($txn->hold_until) ?></strong>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php else: ?>
<p>You have no held transactions.</p>
<?php endif ?>

<h3>Transactions: Failed</h3>
<?php if ($failedTxn): ?>
<p>You have <?php echo number_format($failedTotal) ?> held transaction(s).</p>
<table class="vertical-table">
	<tr>
		<th>Transaction</th>
		<th>Payment Date</th>
		<th>E-mail</th>
		<th>Amount</th>
		<th>Currency</th>
		<th>Credits</th>
	</tr>
	<?php foreach ($failedTxn as $txn): ?>
	<tr>
		<td><?php echo htmlspecialchars($txn->txn_id) ?></td>
		<td><?php echo $this->formatDateTime($txn->payment_date) ?></td>
		<td><?php echo htmlspecialchars($txn->payer_email) ?></td>
		<td><?php echo htmlspecialchars($txn->mc_gross) ?></td>
		<td><?php echo htmlspecialchars($txn->mc_currency) ?></td>
		<td><?php echo number_format($txn->credits) ?></td>
	</tr>
	<?php endforeach ?>
</table>
<?php else: ?>
<p>You have no failed transactions.</p>
<?php endif ?>
