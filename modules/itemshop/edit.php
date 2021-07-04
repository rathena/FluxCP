<?php
if (!defined('FLUX_ROOT')) exit; 

$this->loginRequired();

$title = 'Modify Item in the Shop';

require_once 'Flux/TemporaryTable.php';
require_once 'Flux/ItemShop.php';

$stackable   = false;
$shopItemID  = $params->get('id');
$shop        = new Flux_ItemShop($server);
$categories  = Flux::config('ShopCategories')->toArray();
$item        = $shop->getItem($shopItemID);

if ($item) {
	if($server->isRenewal) {
		$fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
	} else {
		$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
	}
	$tableName = "{$server->charMapDatabase}.items";
	$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);
	$shopTable = Flux::config('FluxTables.ItemShopTable');

	$col = "id AS item_id, name_english AS item_name, type";
	$sql = "SELECT $col FROM $tableName WHERE items.id = ?";
	$sth = $server->connection->getStatement($sql);

	$sth->execute(array($item->shop_item_nameid));
	$originalItem = $sth->fetch();

	if ($originalItem && Flux::isStackableItemType($originalItem->type)) {
		$stackable = true;
	}
	
	if (count($_POST)) {
		$maxCost     = (int)Flux::config('ItemShopMaxCost');
		$maxQty      = (int)Flux::config('ItemShopMaxQuantity');
		$category    = $params->get('category');
		$cost        = (int)$params->get('cost');
		$quantity    = (int)$params->get('qty');
		$info        = trim($params->get('info'));
		$image       = $files->get('image');
		$useExisting = (int)$params->get('use_existing');

		if (!$cost) {
			$errorMessage = 'You must input a credit cost greater than zero.';
		}
		elseif ($cost > $maxCost) {
			$errorMessage = "The credit cost must not exceed $maxCost.";
		}
		elseif (!$quantity) {
			$errorMessage = 'You must input a quantity greater than zero.';
		}
		elseif ($quantity > 1 && !$stackable) {
			$errorMessage = 'This item is not stackable. Quantity must be 1.';
		}
		elseif ($quantity > $maxQty) {
			$errorMessage = "The item quantity must not exceed $maxQty.";
		}
		elseif (!$info) {
			$errorMessage = 'You must input at least some info text.';
		}
		else {
			if ($shop->edit($shopItemID, $category, $cost, $quantity, $info, $useExisting)) {
				if ($image && $image->get('size') && !$shop->uploadShopItemImage($shopItemID, $image)) {
					$errorMessage = 'Failed to upload image.';
				}
				else {
					$session->setMessageData('Item has been successfully modified.');
					$this->redirect($this->url('purchase'));
				}
			}
			else {
				$errorMessage = 'Failed to modify the item.';
			}
		}
	}
	
	if (empty($category)) {
		$category = $item->shop_item_category;
	}
	if (empty($cost)) {
		$cost = $item->shop_item_cost;
	}
	if (empty($quantity)) {
		$quantity = $item->shop_item_qty;
	}
	if (empty($info)) {
		$info = $item->shop_item_info;
	}
}

if (!$stackable) {
	$params->set('qty', 1);
}
?>
