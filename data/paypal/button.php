<?php
if (!defined('FLUX_ROOT')) exit;

if (empty($amount)) {
	return false;
}

$session            = Flux::$sessionData;
$customDataArray    = array('server_name' => $session->loginAthenaGroup->serverName, 'account_id' => $session->account->account_id);
$customDataEscaped  = htmlspecialchars(base64_encode(serialize($customDataArray)));
$businessEmail      = htmlspecialchars(Flux::config('PayPalBusinessEmail'));
$donationCurrency   = htmlspecialchars(Flux::config('DonationCurrency'));
$creditExchangeRate = Flux::config('CreditExchangeRate');
$donationCredits    = floor($amount / $creditExchangeRate);
$itemName           = htmlspecialchars(sprintf('Donation Credits: %s CREDIT(s)', number_format($donationCredits)));
?>
<form action="https://<?php echo Flux::config('PayPalIpnUrl') ?>/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_donations" />
<input type="hidden" name="notify_url" value="<?php echo $this->url('donate', 'notify', array('_host' => true)) ?>" />
<input type="hidden" name="return" value="<?php echo $this->url('main', 'index', array('_host' => true)) ?>" />
<input type="hidden" name="custom" value="<?php echo $customDataEscaped ?>" />
<input type="hidden" name="business" value="<?php echo $businessEmail ?>" />
<input type="hidden" name="item_name" value="<?php echo $itemName ?>" />
<input type="hidden" name="amount" value="<?php echo (float)$amount ?>" />
<input type="hidden" name="no_shipping" value="0" />
<input type="hidden" name="no_note" value="1" />
<input type="hidden" name="currency_code" value="<?php echo $donationCurrency ?>" />
<input type="hidden" name="tax" value="0" />
<input type="hidden" name="lc" value="US" />
<input type="hidden" name="bn" value="PP-DonationsBF" />
<p style="text-align: center"><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif"
	name="submit" alt="PayPal - The safer, easier way to pay online!" /></p>
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
</form>
