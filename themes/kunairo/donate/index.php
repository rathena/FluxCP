<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Donar</h2>
<?php if (Flux::config('AcceptDonations')): ?>
	<?php if (!empty($errorMessage)): ?>
		<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
	<?php endif ?>

	<p>Al donar, est&aacute;s apoyando los costos de <em>mantener</em> este servidor y <em>operarlo</em>. A cambio, recibir&aacute;s <span class="keyword">cr&eacute;ditos de donaci&oacute;n</span> que puedes usar para comprar items en nuestra <a href="<?php echo $this->url('purchase') ?>">tienda</a>.</p>
	<h3>&iquest;Listo para donar?</h3>
	<p>Todas las donaciones se reciben a trav&eacute;s de PayPal. Si no tienes cuenta de PayPal, puedes usar tu tarjeta de cr&eacute;dito igualmente.</p>

	<?php
	$currency         = Flux::config('DonationCurrency');
	$dollarAmount     = (float)+Flux::config('CreditExchangeRate');
	$creditAmount     = 1;
	$rateMultiplier   = 10;
	$hoursHeld        = +(int)Flux::config('HoldUntrustedAccount');

	while ($dollarAmount < 1) {
		$dollarAmount  *= $rateMultiplier;
		$creditAmount  *= $rateMultiplier;
	}
	?>

	<?php if ($hoursHeld): ?>
		<p>Para prevenir pagos fraudulentos, el servidor retiene los cr&eacute;ditos por
			<span class="hold-hours"><?php echo number_format($hoursHeld) ?> horas</span>
			despu&eacute;s de la donaci&oacute;n.</p>
		<p>Esta retenci&oacute;n se aplica solo una vez por email de PayPal y cuenta RO.</p>
	<?php endif ?>

	<div class="generic-form-div" style="margin-bottom: 10px">
		<table class="generic-form-table">
			<tr>
				<th><label>Tasa de cambio actual:</label></th>
				<td><p><?php echo $this->formatCurrency($dollarAmount) ?> <?php echo htmlspecialchars($currency) ?>
				= <?php echo number_format($creditAmount) ?> cr&eacute;dito(s).</p></td>
			</tr>
			<tr>
				<th><label>Monto m&iacute;nimo de donaci&oacute;n:</label></th>
				<td><p><?php echo $this->formatCurrency(Flux::config('MinDonationAmount')) ?> <?php echo htmlspecialchars($currency) ?></p></td>
			</tr>
		</table>
	</div>

	<?php if (!$donationAmount): ?>
	<form action="<?php echo $this->url ?>" method="post">
		<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
		<input type="hidden" name="setamount" value="1" />
		<p class="enter-donation-amount">
			<label>
				Ingresa el monto que deseas donar:
				<input class="money-input" type="text" name="amount"
					value="<?php echo htmlspecialchars($params->get('amount') ?: 0) ?>"
					size="<?php echo (strlen((string)+Flux::config('CreditExchangeRate')) * 2) + 2 ?>" />
				<?php echo htmlspecialchars(Flux::config('DonationCurrency')) ?>
			</label>
			o
			<label>
				<input class="credit-input" type="text" name="credit-amount"
					value="<?php echo htmlspecialchars(intval($params->get('amount') / Flux::config('CreditExchangeRate'))) ?>"
					size="<?php echo (strlen((string)+Flux::config('CreditExchangeRate')) * 2) + 2 ?>" />
				Cr&eacute;ditos
			</label>
		</p>
		<input type="submit" value="Confirmar monto de donaci&oacute;n" class="submit_button" />
	</form>
	<?php else: ?>
	<p>Cuando est&eacute;s listo para donar, haz click en el bot&oacute;n "Donar" para proceder con tu transacci&oacute;n.
		(Puedes donar desde tu saldo de PayPal o usar tu tarjeta de cr&eacute;dito).</p>

	<p class="credit-amount-text">
		&mdash;
		<span class="credit-amount"><?php echo number_format(floor($donationAmount / Flux::config('CreditExchangeRate'))) ?></span> cr&eacute;ditos
		&mdash;
	</p>

	<p class="donation-amount-text">Monto:
		<span class="donation-amount">
		<?php echo $this->formatCurrency($donationAmount) ?>
		<?php echo htmlspecialchars(Flux::config('DonationCurrency')) ?>
		</span>
	</p>
	<p class="reset-amount-text">
		<a href="<?php echo $this->url('donate', 'index', array('resetamount' => true)) ?>">(Restablecer monto)</a>
	</p>
	<p><?php echo $this->donateButton($donationAmount) ?></p>
	<?php endif ?>
<?php else: ?>
	<p><?php echo Flux::message('NotAcceptingDonations') ?></p>
<?php endif ?>
