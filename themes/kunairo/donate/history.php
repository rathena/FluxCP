<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Historial de donaciones</h2>
<h3>Transacciones: Completadas</h3>
<?php if ($completedTxn): ?>
<p>Tienes <?php echo number_format($completedTotal) ?> transacci&oacute;n(es) completada(s).</p>
<table class="vertical-table">
	<tr>
		<th>Transacci&oacute;n</th>
		<th>Fecha de pago</th>
		<th>Email</th>
		<th>Monto</th>
		<th>Moneda</th>
		<th>Cr&eacute;ditos</th>
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
<p>No tienes transacciones completadas.</p>
<?php endif ?>

<h3>Transacciones: Retenidas</h3>
<?php if ($heldTxn): ?>
<p>Tienes <?php echo number_format($heldTotal) ?> transacci&oacute;n(es) retenida(s).</p>
<table class="vertical-table">
	<tr>
		<th>Transacci&oacute;n</th>
		<th>Fecha de pago</th>
		<th>Email</th>
		<th>Monto</th>
		<th>Moneda</th>
		<th>Cr&eacute;ditos</th>
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
			&#x21B3; Retenida hasta:
			<strong><?php echo $this->formatDateTime($txn->hold_until) ?></strong>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php else: ?>
<p>No tienes transacciones retenidas.</p>
<?php endif ?>

<h3>Transacciones: Fallidas</h3>
<?php if ($failedTxn): ?>
<p>Tienes <?php echo number_format($failedTotal) ?> transacci&oacute;n(es) fallida(s).</p>
<table class="vertical-table">
	<tr>
		<th>Transacci&oacute;n</th>
		<th>Fecha de pago</th>
		<th>Email</th>
		<th>Monto</th>
		<th>Moneda</th>
		<th>Cr&eacute;ditos</th>
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
<p>No tienes transacciones fallidas.</p>
<?php endif ?>
