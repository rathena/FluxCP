<?php if (!defined('FLUX_ROOT')) exit; ?>
<link rel="stylesheet" href="<?php echo $this->themePath('css/stripe.css') ?>" type="text/css" media="screen" title="" charset="utf-8" />

<h2>Donate - Admin</h2>

<p>Url to be registered in Hosted endpoints (Stripe): <code><?php echo $this->url('donate', 'stripenotify', array('_host' => true)) ?></code></p>
<p>Donates today: <span class="donates-today"><?php echo number_format($donates->count); ?></span> </p>
