<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Donate</h2>
<?php if (Flux::config('AcceptDonations')): ?>
	<?php if (!empty($errorMessage)): ?>
		<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
	<?php endif ?>
	
	<p>By donating, you're supporting the costs of <em>running</em> this server and <em>maintaining</em> it.  In return, you will be rewarded <span class="keyword">donation credits</span> that you may use to purchase items from our <a href="<?php echo $this->url('purchase') ?>">item shop</a>.</p>
	<h3>Are you ready to donate?</h3>
	<p>All donations towards us are received by PayPal, but don't worry!  Even if you don't have an account with PayPal, you can still use your credit card to donate!</p>
		
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
		<p>To prevent fraudulent payments, our server currently locks the crediting process for
			<span class="hold-hours"><?php echo number_format($hoursHeld) ?> hours</span>
			after the donation has been made to ensure legitimate gameplay and a healthy PayPal reputation.</p>
		<p>This hold is applied only once for the associated PayPal e-mail and RO account.</p>
	<?php endif ?>

	<div class="generic-form-div" style="margin-bottom: 10px">
		<table class="generic-form-table">
			<tr>
				<th><label>Current Credit Exchange Rate:</label></th>
				<td><p><?php echo $this->formatCurrency($dollarAmount) ?> <?php echo htmlspecialchars($currency) ?>
				= <?php echo number_format($creditAmount) ?> credit(s).</p></td>
			</tr>
			<tr>
				<th><label>Minimum Donation Amount:</label></th>
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
				Enter an amount you would like to donate:
				<input class="money-input" type="text" name="amount"
					value="<?php echo htmlspecialchars($params->get('amount') ?: 0) ?>"
					size="<?php echo (strlen((string)+Flux::config('CreditExchangeRate')) * 2) + 2 ?>" />
				<?php echo htmlspecialchars(Flux::config('DonationCurrency')) ?>
			</label>
			or
			<label>
				<input class="credit-input" type="text" name="credit-amount"
					value="<?php echo htmlspecialchars(intval($params->get('amount') / Flux::config('CreditExchangeRate'))) ?>"
					size="<?php echo (strlen((string)+Flux::config('CreditExchangeRate')) * 2) + 2 ?>" />
				Credits
			</label>
		</p>
		<input type="submit" value="Confirm Donation Amount" class="submit_button" />
	</form>
	<?php else: ?>
	<p>When you're ready to donate, click the big “Donate” button to proceed with your transaction.
		(You can choose to donate from your existing PayPal balance or use your credit card if you don't have an account).</p>
		
	<p class="credit-amount-text">
		&mdash;
		<span class="credit-amount"><?php echo number_format(floor($donationAmount / Flux::config('CreditExchangeRate'))) ?></span> credits
		&mdash;
	</p>
		
	<p class="donation-amount-text">Amount:
		<span class="donation-amount">
		<?php echo $this->formatCurrency($donationAmount) ?>
		<?php echo htmlspecialchars(Flux::config('DonationCurrency')) ?>
		</span>
	</p>
	<p class="reset-amount-text">
		<a href="<?php echo $this->url('donate', 'index', array('resetamount' => true)) ?>">(Reset Amount)</a>
	</p>
	<p><?php echo $this->donateButton($donationAmount) ?></p>
	<?php endif ?>
<?php else: ?>
	<p><?php echo Flux::message('NotAcceptingDonations') ?></p>
<?php endif ?>
