<?php
if (!defined('FLUX_ROOT')) exit;

if (empty($amount)) {
	return false;
}

$session            = Flux::$sessionData;
$customDataArray    = array('server_name' => $session->loginAthenaGroup->serverName, 'account_id' => $session->account->account_id);
$customDataEscaped  = htmlspecialchars(base64_encode(serialize($customDataArray)));
?>
<link rel="stylesheet" href="<?php echo $this->themePath('css/stripe.css') ?>" type="text/css" media="screen" title="" charset="utf-8" />
<script async src="https://js.stripe.com/v3/buy-button.js"></script>

<div class="button-payment-stripe">
    <stripe-buy-button
        buy-button-id="<?php echo FLUX::config('StripeButtonId') ?>"
        publishable-key="<?php echo FLUX::config('StripePublishableKey') ?>"
        client-reference-id="<?php echo $customDataEscaped ?>"
    >
    </stripe-buy-button>
</div>
