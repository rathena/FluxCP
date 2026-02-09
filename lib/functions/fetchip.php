<?php
/**
 * Get user IP.
 * Checks if CloudFlare or reverse proxy used to get real IP.
 *
 * @access public
 */
function fetchIP()
{
	$alt_ip = $_SERVER['REMOTE_ADDR'];
	if (isset($_SERVER['HTTP_X_REAL_IP'])) {
		$alt_ip = $_SERVER['HTTP_X_REAL_IP'];
	} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$alt_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
		$alt_ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
		$alt_ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
	}

	return $alt_ip;
}
?>
