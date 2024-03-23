<?php
if (!defined('FLUX_ROOT')) exit;

require_once 'Flux/PaypalNotifyRequest.php';
require_once 'Flux/StripeNotifyRequest.php';
if (count($_POST)) {
    if (in_array("paypal", Flux::config('PaymentGateway')->toArray())) {
        $request = new Flux_PaypalNotifyRequest($_POST);
        $request->process();
    }

    if (in_array("stripe", Flux::config('PaymentGateway')->toArray())) {
        $request = new Flux_StripeNotifyRequest($server);
        $request->process();
    }
}
exit;
?>
