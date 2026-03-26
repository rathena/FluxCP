<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Emails de PayPal confiables</h2>
<?php if ($emails): ?>
<p>A continuaci&oacute;n se muestra la lista de tus emails de PayPal confiables.</p>
<p>Los emails confiables no pasan por el proceso de retenci&oacute;n, por lo que las donaciones realizadas desde ellos te otorgan cr&eacute;ditos <strong>instant&aacute;neamente</strong>.</p>
<table class="vertical-table">
	<tr>
		<th>Email</th>
		<th>Fecha de registro</th>
	</tr>
	<?php foreach ($emails as $email): ?>
	<tr>
		<td><?php echo htmlspecialchars($email->email) ?></td>
		<td><?php echo $this->formatDateTime($email->create_date) ?></td>
	</tr>
	<?php endforeach ?>
</table>
<?php else: ?>
<p>No tienes emails de PayPal confiables registrados.</p>
<?php if (!Flux::config('HoldUntrustedAccount')): ?>
<p>Esto se debe a que el sistema de retenci&oacute;n de cr&eacute;ditos <strong>no est&aacute; activo</strong>, lo que significa que las donaciones desde cualquier email se acreditan inmediatamente.</p>
<?php endif ?>
<?php endif ?>
