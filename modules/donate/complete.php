<?php
if (!defined('FLUX_ROOT')) exit;

$ppReturn = $session->ppReturn;
$session->setPpReturnData(null);

if (!$ppReturn) {
	$this->deny();
}
?>
