<?php
if (!defined('FLUX_ROOT')) exit;

require_once 'Flux/Captcha.php';
$captcha = new Flux_Captcha();
$session->setSecurityCodeData($captcha->code);
$captcha->display();
?>
