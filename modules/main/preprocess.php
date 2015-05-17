<?php
if (!defined('FLUX_ROOT')) exit;

// Check for "special" date fields.
$__dates = array();
foreach ($params->toArray() as $key => $value) {
	if (preg_match('&^(.+?)_(year|month|day|hour|minute|second)$&', $key, $m)) {
		$__dateParam = $m[1];
		$__dateType  = $m[2];
		
		if (!array_key_exists($__dateParam, $__dates)) {
			// Not too sure why, but if I don't create a separate index for this array,
			// It will use the previous iteration's reference.
			$__dateArray[$__dateParam] = array();
			$__dates[$__dateParam] = new Flux_Config($__dateArray[$__dateParam]);
		}
		
		$__dates[$__dateParam]->set($__dateType, $value);
	}
}

foreach ($__dates as $__dateName => $__date) {
	$_year   = $__date->get('year');
	$_month  = $__date->get('month');
	$_day    = $__date->get('day');
	$_hour   = $__date->get('hour');
	$_minute = $__date->get('minute');
	$_second = $__date->get('second');
	
	// Construct DATE.
	if (!is_null($_year) && !is_null($_month) && !is_null($_day)) {
		$_format = sprintf('%04d-%02d-%02d', $_year, $_month, $_day);
		// Construct DATETIME.
		if (!is_null($_hour) && !is_null($_minute) && !is_null($_second)) {
			$_format .= sprintf(' %02d:%02d:%02d', $_hour, $_minute, $_second);
		}
		$params->set("{$__dateName}_date", $_format);
	}
}

$installer = Flux_Installer::getInstance();
if ($installer->updateNeeded() && $params->get('module') != 'install') {
	$this->redirect($this->url('install'));
}

if (Flux::config('AutoUnholdAccount')) {
	Flux::processHeldCredits();
}

if (Flux::config('AutoPruneAccounts')) {
	Flux::pruneUnconfirmedAccounts();
}

$ppReturn = array(
	'txn_id'      => $params->get('txn_id'),
	'txn_type'    => $params->get('txn_type'),
	'first_name'  => $params->get('first_name'),
	'last_name'   => $params->get('last_name'),
	'item_name'   => $params->get('item_name'),
	'verify_sign' => $params->get('verify_sign')
);

if ($params->get('merchant_return_link') && $ppReturn['txn_id'] && $ppReturn['txn_type'] &&
	$ppReturn['first_name'] && $ppReturn['last_name'] && $ppReturn['item_name'] && $ppReturn['verify_sign']) {
		
	$session->setPpReturnData($ppReturn);
	$this->redirect($this->url('donate', 'complete'));
}


// Update preferred server.
if (($preferred_server = $params->get('preferred_server')) && $session->getAthenaServer($preferred_server)) {
	$session->setAthenaServerNameData($params->get('preferred_server'));
	if (!array_key_exists('preferred_server', $_GET)) {
		$this->redirect($this->urlWithQs);
	}
}

if (($preferred_theme = $params->get('preferred_theme'))) {
	if (in_array($preferred_theme, Flux::$appConfig->get('ThemeName', false))) {
		$session->setThemeData($params->get('preferred_theme'));
		if (!array_key_exists('preferred_theme', $_GET)) {
			$this->redirect($this->urlWithQs);
		}
	}
}

// Preferred server.
$server = $session->getAthenaServer();

// WoE-based authorization.
$_thisModule = $params->get('module');
$_thisAction = $params->get('action');

$woeDisallowModule = $server->woeDisallow->get($_thisModule);
$woeDisallowAction = $server->woeDisallow->get("$_thisModule.$_thisAction");

if (!$auth->allowedToViewWoeDisallowed && ($woeDisallowModule || $woeDisallowAction) && $server->isWoe()) {
	$session->setMessageData(Flux::message('DisallowedDuringWoE'));
	$this->redirect();
}
?>
