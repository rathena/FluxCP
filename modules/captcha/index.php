<?php

use rAthena\FluxCp\Captcha;

if (!defined('FLUX_ROOT')) exit;

$captcha = new Captcha();
$session->setSecurityCodeData($captcha->code);
$captcha->display();
?>
