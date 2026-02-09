<?php
if (!defined('FLUX_ROOT')) exit;

$title = null;
if(Flux::config('CMSNewsOnHomepage')) {
	include 'modules/news/index.php';
}
?>
