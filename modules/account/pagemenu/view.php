<?php
// Module variables are available in page menus.
// However, access group_id checking must be done directly from the page menu.
// Minimal access checking such as $auth->actionAllowed('moduleName', 'actionName') should be performed.
$groups  = AccountLevel::getArray();

$pageMenu = array();
if ((AccountLevel::getGroupLevel($account->group_id) <= $session->account->group_level || $auth->allowedToEditHigherPower) && $auth->actionAllowed('account', 'edit')) {
	$pageMenu[Flux::message('ModifyAccountLink')] = $this->url('account', 'edit', array('id' => $account->account_id));
}
return $pageMenu;
?>
