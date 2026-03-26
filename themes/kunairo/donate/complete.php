<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Donaci&oacute;n completada</h2>
<p class="important">Tu transacci&oacute;n ha sido procesada y deber&iacute;as recibir tus cr&eacute;ditos en breve.</p>
<?php $hoursHeld = +(int)Flux::config('HoldUntrustedAccount'); ?>
<?php if ($hoursHeld): ?>
	<p>
		Nota: Existe un sistema de retenci&oacute;n de cuentas activo. Si es tu primera vez donando con la cuenta
		y email de PayPal seleccionados, no recibir&aacute;s tus cr&eacute;ditos por <?php echo number_format($hoursHeld) ?> horas.
	</p>
<?php endif ?>
<p>Adem&aacute;s, se te ha enviado un email con los detalles de tu transacci&oacute;n.</p>
<p>Tambi&eacute;n puedes revisar el historial desde tu cuenta de PayPal.</p>

<br />
<br />
<p class="important" style="text-align: center; font-weight: bold">&iexcl;Gracias por tu generosa donaci&oacute;n!</p>
<p class="important" style="text-align: center">&mdash; <?php echo htmlspecialchars($session->loginAthenaGroup->serverName) ?></p>
