<?php
$pageMenu = array();
if ($auth->actionAllowed('itemshop', 'add') && $auth->allowedToAddShopItem) {
	if ($item->cost) {
		$pageMenu['Add to Item Shop (Again)'] = $this->url('itemshop', 'add', array('id' => $item->item_id));
	}
	else {
		$pageMenu['Add to Item Shop'] = $this->url('itemshop', 'add', array('id' => $item->item_id));
	}
}
return $pageMenu;
?>
