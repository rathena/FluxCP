<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('LogoutTitle');

$session->logout();
$metaRefresh = array('seconds' => 2, 'location' => $this->basePath);
?>
