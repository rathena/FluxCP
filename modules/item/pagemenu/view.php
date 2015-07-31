<?php
$pageMenu = array();
if ($auth->actionAllowed('item', 'edit')) {
	$pageMenu['Modify Item'] = $this->url('item', 'edit', array('id' => $item->item_id));
}
if ($auth->actionAllowed('item', 'copy')) {
	$pageMenu['Duplicate Item'] = $this->url('item', 'copy', array('id' => $item->item_id));
}
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