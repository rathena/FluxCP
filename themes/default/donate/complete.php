<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Donation Complete</h2>
<p class="important">Your transaction has been processed and you should receive credits in a short amount of time.</p>
<?php $hoursHeld = +(int)Flux::config('HoldUntrustedAccount'); ?>
<?php if ($hoursHeld): ?>
	<p>
		Note: There is currently an account holding system in effect. If this is your first time donating with the selected account
		and configured PayPal e-mail address, you will not receive your credits for <?php echo number_format($hoursHeld) ?> hours.
	</p>
<?php endif ?>
<p>Additionally, an e-mail has been sent to you outlining the details of your transaction.</p>
<p>You may also view your account history from your PayPal account.</p>

<br />
<br />
<p class="important" style="text-align: center; font-weight: bold">“Thank you for your generous donation!”</p>
<p class="important" style="text-align: center">&mdash; <?php echo htmlspecialchars($session->loginAthenaGroup->serverName) ?></p>
