<?php
require_once 'Flux/TemporaryTable.php';
require_once 'Flux/ItemExistsError.php';

class Flux_ItemShop {
	/**
	 * @access public
	 * @var Flux_Athena
	 */
	public $server;
	
	public function __construct(Flux_Athena $server)
	{
		$this->server = $server;
	}
	
	/**
	 * Add an item to the shop.
	 */
	public function add($itemID, $categoryID, $cost, $quantity, $info, $useExisting = 0)
	{
		$db    = $this->server->charMapDatabase;
		$table = Flux::config('FluxTables.ItemShopTable');
		$sql   = "INSERT INTO $db.$table (nameid, category, quantity, cost, info, use_existing, create_date) VALUES (?, ?, ?, ?, ?, ?, NOW())";
		$sth   = $this->server->connection->getStatement($sql);
		$res   = $sth->execute(array($itemID, $categoryID, $quantity, $cost, $info, $useExisting));
		$sth2  = $this->server->connection->getStatement('SELECT LAST_INSERT_ID() AS insID');
		$res2  = $sth2->execute();
		
		if ($res && $res2 && ($insertID=$sth2->fetch()->insID)) {
			return $insertID;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Modify item info in the shop.
	 */
	public function edit($shopItemID, $categoryID = null, $cost = null, $quantity = null, $info = null, $useExisting = null)
	{
		$catQ = '';
		$crdQ = '';
		$qtyQ = '';
		$infQ = '';
		$imgQ = '';
		$bind = array();
		
		if (!is_null($categoryID)) {
			$catQ   = "category = ? ";
			$bind[] = (int)$categoryID;
		}
		
		if (!is_null($cost)) {
			if ($catQ) {
				$crdQ   = ", cost = ? ";
			}
			else {
				$crdQ   = "cost = ? ";
			}
			
			$bind[] = (int)$cost;
		}
		
		if (!is_null($quantity)) {
			if ($crdQ) {
				$qtyQ = ', quantity = ? ';
			}
			else {
				$qtyQ = "quantity = ? ";
			}
			
			$bind[] = (int)$quantity;
		}
		
		if (!is_null($info)) {
			if ($qtyQ) {
				$infQ = ', info = ? ';
			}
			else {
				$infQ = "info = ? ";
			}
			
			$bind[] = trim($info);
		}
		
		if (!is_null($useExisting)) {
			if ($infQ) {
				$imgQ = ', use_existing = ? ';
			}
			else {
				$imgQ = "use_existing = ? ";
			}
			
			$bind[] = (int)$useExisting;
		}
		
		if (empty($bind)) {
			return false;
		}
		
		$db    = $this->server->charMapDatabase;
		$table = Flux::config('FluxTables.ItemShopTable');
		$sql   = "UPDATE $db.$table SET $catQ $crdQ $qtyQ $infQ $imgQ WHERE id = ?";
		$sth   = $this->server->connection->getStatement($sql);
		
		$bind[] = $shopItemID;
		return $sth->execute($bind);
	}
	
	/**
	 *
	 */
	public function delete($shopItemID)
	{
		$db    = $this->server->charMapDatabase;
		$table = Flux::config('FluxTables.ItemShopTable');
		$sql   = "DELETE FROM $db.$table WHERE id = ?";
		$sth   = $this->server->connection->getStatement($sql);
		
		return $sth->execute(array($shopItemID));
	}
	
	/**
	 *
	 */
	public function buy(Flux_DataObject $account, $shopItemID)
	{
		
	}
	
	/**
	 *
	 */
	public function getItem($shopItemID)
	{
		$db = $this->server->charMapDatabase;
		
		if($this->server->isRenewal) {
			$fromTables = array("$db.item_db_re", "$db.item_db2_re");
		} else {
			$fromTables = array("$db.item_db", "$db.item_db2");
		}
		
		$temp  = new Flux_TemporaryTable($this->server->connection, "$db.items", $fromTables);
		$shop  = Flux::config('FluxTables.ItemShopTable');
		$col   = "$shop.id AS shop_item_id, $shop.category AS shop_item_category, $shop.cost AS shop_item_cost, $shop.quantity AS shop_item_qty, $shop.use_existing AS shop_item_use_existing, ";
		$col  .= "$shop.nameid AS shop_item_nameid, $shop.info AS shop_item_info, items.name_english AS shop_item_name";
		$sql   = "SELECT $col FROM $db.$shop LEFT OUTER JOIN $db.items ON items.id = $shop.nameid WHERE $shop.id = ?";
		$sth   = $this->server->connection->getStatement($sql);
		
		if ($sth->execute(array($shopItemID))) {
			return $sth->fetch();
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function getItems($paginator, $categoryID = null)
	{
		$sqlpartial = "";
		$bind = array();
		$db   = $this->server->charMapDatabase;
		
		if($this->server->isRenewal) {
			$fromTables = array("$db.item_db_re", "$db.item_db2_re");
		} else {
			$fromTables = array("$db.item_db", "$db.item_db2");
		}
		
		$temp  = new Flux_TemporaryTable($this->server->connection, "$db.items", $fromTables);
		$shop  = Flux::config('FluxTables.ItemShopTable');
		$col   = "$shop.id AS shop_item_id, $shop.cost AS shop_item_cost, $shop.quantity AS shop_item_qty, $shop.use_existing AS shop_item_use_existing, ";
		$col  .= "$shop.nameid AS shop_item_nameid, $shop.info AS shop_item_info, items.name_english AS shop_item_name";
		if (!is_null($categoryID)) {
			$sqlpartial = " WHERE $shop.category = ?";
			$bind[]     = $categoryID;
		}
		$sql   = $paginator->getSQL("SELECT $col FROM $db.$shop LEFT OUTER JOIN $db.items ON items.id = $shop.nameid $sqlpartial");
		$sth   = $this->server->connection->getStatement($sql);
		
		if ($sth->execute($bind)) {
			return $sth->fetchAll();
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function deleteShopItemImage($shopItemID)
	{
		$serverName       = $this->server->loginAthenaGroup->serverName;
		$athenaServerName = $this->server->serverName;
		$dir              = FLUX_DATA_DIR."/itemshop/$serverName/$athenaServerName";
		$files            = glob("$dir/$shopItemID.*");
		
		foreach ($files as $file) {
			unlink($file);
		}
		
		return true;
	}
	
	/**
	 *
	 */
	public function uploadShopItemImage($shopItemID, Flux_Config $file)
	{
		if ($file->get('error')) {
			return false;
		}
		
		$validexts = array_map('strtolower', Flux::config('ShopImageExtensions')->toArray());
		$extension = strtolower(pathinfo($file->get('name'), PATHINFO_EXTENSION));
		
		if (!in_array($extension, $validexts)) {
			return false;
		}
		
		$serverName       = $this->server->loginAthenaGroup->serverName;
		$athenaServerName = $this->server->serverName;
		$dir              = FLUX_DATA_DIR."/itemshop/$serverName/$athenaServerName";
		
		if (!is_dir(FLUX_DATA_DIR."/itemshop/$serverName")) {
			mkdir(FLUX_DATA_DIR."/itemshop/$serverName");
		}
		
		if (!is_dir($dir)) {
			mkdir($dir);
		}
		
		$this->deleteShopItemImage($shopItemID);
		
		if (move_uploaded_file($file->get('tmp_name'), "$dir/$shopItemID.$extension")) {
			return true;
		}
		else {
			return false;
		}
	}
}
?>
