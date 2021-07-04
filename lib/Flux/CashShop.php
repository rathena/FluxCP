<?php
require_once 'Flux/TemporaryTable.php';
require_once 'Flux/ItemExistsError.php';

class Flux_CashShop {
	/**
	 * @access public
	 * @var Flux_Athena
	 */
	public $server;
	
	public function __construct(Flux_Athena $server) {
		$this->server = $server;
	}
	
	/**
	 * Add an item to the cash shop.
	 */
	public function add($tab, $itemID, $price) {
		$db    = $this->server->charMapDatabase;
		$sql   = "INSERT INTO $db.`item_cash_db` (tab, item_id, price) VALUES (?, ?, ?)";
		$sth   = $this->server->connection->getStatement($sql);
		$res   = $sth->execute(array($tab, $itemID, $price));
		
		if ($res) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Modify item info in the shop.
	 */
	public function edit($shopItemID, $tab = null, $price = null) {
		$tabQ = '';
		$priceQ = '';
		$bind = array();
		
		if (!is_null($tab)) {
			$tabQ   = "tab = ? ";
			$bind[] = (int)$tab;
		}
		
		if (!is_null($price)) {
			if ($tabQ) {
				$priceQ   = ", price = ? ";
			} else {
				$priceQ   = "price = ? ";
			}
			$bind[] = (int)$price;
		}
		
		if (empty($bind)) { return false; }
		
		$db    = $this->server->charMapDatabase;
		$sql   = "UPDATE $db.`item_cash_db` SET $tabQ $priceQ WHERE item_id = ?";
		$sth   = $this->server->connection->getStatement($sql);
		
		$bind[] = $shopItemID;
		return $sth->execute($bind);
	}
	
	/**
	 *
	 */
	public function delete($ItemID) {
		$db    = $this->server->charMapDatabase;
		$sql   = "DELETE FROM $db.`item_cash_db` WHERE item_id = ?";
		$sth   = $this->server->connection->getStatement($sql);
		
		return $sth->execute(array($ItemID));
	}
	
	/**
	 *
	 */
	public function getItem($shopItemID) {
		$db = $this->server->charMapDatabase;
		
		if($this->server->isRenewal) {
			$fromTables = array("$db.item_db_re", "$db.item_db2_re");
		} else {
			$fromTables = array("$db.item_db", "$db.item_db2");
		}
		
		$temp  = new Flux_TemporaryTable($this->server->connection, "$db.items", $fromTables);
		$shop  = 'item_cash_db';
		$col   = "$shop.item_id AS shop_item_id, $shop.tab AS shop_item_tab, $shop.price AS shop_item_price, ";
		$col  .= "items.name_english AS shop_item_name";
		$sql   = "SELECT $col FROM $db.$shop LEFT OUTER JOIN $db.items ON items.id = $shop.item_id WHERE $shop.item_id = ?";
		$sth   = $this->server->connection->getStatement($sql);
		
		if ($sth->execute(array($shopItemID))) {
			return $sth->fetch();
		} else {
			return false;
		}
	}

}
?>
