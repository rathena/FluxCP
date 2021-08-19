<?php
require_once 'Flux/Config.php';

/**
 * The Athena class is used for all database interactions with each rA server,
 * hence its name.
 *
 * All methods related to creating/modifying any data in the Ragnarok databases
 * and tables shall always go into this class.
 */
class Flux_Athena {	
	/**
	 * Connection object for saving and retrieving data to the rA databases.
	 *
	 * @access public
	 * @var Flux_Connection
	 */
	public $connection;
	
	/**
	 * Server name, normally something like 'My Cool High-Rate'.
	 *
	 * @access public
	 * @var string
	 */
	public $serverName;
	
	/**
	 * Experience rates for base, job, and mvp bonus. Values in percents.
	 * For example, 100 means 100% which is 1x offical rates.
	 *
	 * @access public
	 * @var int
	 */
	public $expRates = array();
	
	/**
	 * Drop rate. Same rules as $expRates apply.
	 *
	 * @access public
	 * @var int
	 */
	public $dropRates = array();
	
	/**
	 * Database used for the login-related SQL operations.
	 *
	 * @access public
	 * @var string
	 */
	public $loginDatabase;
	
	/**
	 * Logs database. (is not set until setConnection() is called.)
	 *
	 * @access public
	 * @var string
	 */
	public $logsDatabase;
	
	/**
	 * Database used for the char/map (aka everything else) SQL operations.
	 * This does not include log-related tasks.
	 *
	 * @access public
	 * @var string
	 */
	public $charMapDatabase;
	
	/**
	 * Login server object tied to this collective rA server.
	 *
	 * @access public
	 * @var Flux_LoginServer
	 */
	public $loginServer;
	
	/**
	 * Character server object tied to this collective rA server.
	 *
	 * @access public
	 * @var Flux_CharServer
	 */
	public $charServer;
	
	/**
	 * Map server object tied to this collective rA server.
	 *
	 * @access public
	 * @var Flux_MapServer
	 */
	public $mapServer;
	
	/**
	 * Item shop cart.
	 *
	 * @access public
	 * @var Flux_ItemShop_Cart
	 */
	public $cart;
	
	/**
	 * @access public
	 * @var Flux_LoginAthenaGroup
	 */
	public $loginAthenaGroup;
	
	/**
	 * Max character slots for this char/map server.
	 *
	 * @access public
	 * @var int
	 */
	public $maxCharSlots;
	
	/**
	 * Boolean to signify if server is running a renewal environment or not.
	 *
	 * @access public
	 * @var bool
	 */
	public $isRenewal;
	
	/**
	 * Timezone of this char/map server pair.
	 *
	 * @access public
	 * @var string
	 */
	public $dateTimezone;
	
	/**
	 * Array of maps which prohibit the use of "reset position" feature.
	 *
	 * @access public
	 * @var array
	 */
	public $resetDenyMaps;
	
	/**
	 * Array of WoE times.
	 *
	 * @access public
	 * @var array
	 */
	public $woeDayTimes = array();
	
	/**
	 * Config of disallowed module/actions during WoE hours.
	 *
	 * @access public
	 * @var Flux_Config
	 */
	public $woeDisallow;
	
	/**
	 * Initialize char/map pair Flux_Athena pair.
	 *
	 * @param Flux_Connection $connection
	 * @param Flux_Config $charMapConfig
	 * @param Flux_LoginServer $loginServer
	 * @param Flux_CharServer $charServer
	 * @param Flux_MapServer $mapServer
	 * @access public
	 */
	public function __construct(Flux_Config $charMapConfig, Flux_LoginServer $loginServer, Flux_CharServer $charServer, Flux_MapServer $mapServer)
	{
		$this->loginServer     = $loginServer;
		$this->charServer      = $charServer;
		$this->mapServer       = $mapServer;
		$this->loginDatabase   = $loginServer->config->getDatabase();
		
		$this->serverName      = $charMapConfig->getServerName();
		$this->expRates        = $charMapConfig->getExpRates()->toArray();
		$this->dropRates       = $charMapConfig->getDropRates()->toArray();
		$this->isRenewal       = (boolean)$charMapConfig->getRenewal();
		$this->maxCharSlots    = (int)$charMapConfig->getMaxCharSlots();
		$this->dateTimezone    = $charMapConfig->getDateTimezone();
		$this->charMapDatabase = $charMapConfig->getDatabase();
		
		$resetDenyMaps = $charMapConfig->getResetDenyMaps();
		if (!$resetDenyMaps) {
			$this->resetDenyMaps = array('sec_pri');
		}
		elseif (!is_array($resetDenyMaps)) {
			$this->resetDenyMaps = array($resetDenyMaps);
		}
		else {
			$this->resetDenyMaps = $resetDenyMaps->toArray();
		}
		
		// Get WoE times specific in servers config.
		$woeDayTimes = $charMapConfig->getWoeDayTimes();
		if ($woeDayTimes instanceOf Flux_Config) {
			$woeDayTimes = $woeDayTimes->toArray();
			foreach ($woeDayTimes as $dayTime) {
				if (!is_array($dayTime) || count($dayTime) < 4) {
					continue;
				}
				
				list ($sDay, $sTime, $eDay, $eTime) = array_slice($dayTime, 0, 4);
				$sTime = trim($sTime);
				$eTime = trim($eTime);
				
				if ($sDay < 0 || $sDay > 6 || $eDay < 0 || $eDay > 6 ||
					!preg_match('/^\d{2}:\d{2}$/', $sTime) || !preg_match('/^\d{2}:\d{2}$/', $eTime)) {	
					continue;
				}
				
				$this->woeDayTimes[] = array(
					'startingDay'  => $sDay,
					'startingTime' => $sTime,
					'endingDay'    => $eDay,
					'endingTime'   => $eTime
				);
			}
		}
		
		// Config used for disallowing access to certain modules during WoE.
		$woeDisallow       = $charMapConfig->getWoeDisallow();
		$_tempArray        = array();
		$this->woeDisallow = new Flux_Config($_tempArray);
		
		if ($woeDisallow instanceOf Flux_Config) {
			$woeDisallow  = $woeDisallow->toArray();
			
			foreach ($woeDisallow as $disallow) {
				if (array_key_exists('module', $disallow)) {
					$module = $disallow['module'];
					if (array_key_exists('action', $disallow)) {
						$action = $disallow['action'];
						$this->woeDisallow->set("$module.$action", true);
					}
					else {
						$this->woeDisallow->set($module, true);
					}
				}
			}
		}
	}
	
	/**
	 * Set connection object.
	 *
	 * @param Flux_Connection $connection
	 * @return Flux_Connection
	 */
	public function setConnection(Flux_Connection $connection)
	{
		$this->connection   = $connection;
		$this->logsDatabase = $connection->logsDbConfig->getDatabase();
		
		return $connection;
	}
	
	/**
	 * Set cart object.
	 *
	 * @param Flux_ItemShop_Cart $cart
	 * @return Flux_ItemShop_Cart
	 */
	public function setCart(Flux_ItemShop_Cart $cart)
	{
		$this->cart = $cart;
		return $cart;
	}
	
	/**
	 * When casted to a string, the server name should be used.
	 *
	 * @return string
	 * @access public
	 */
	public function __toString()
	{
		return $this->serverName;
	}
	
	/**
	 * Transfer credits from one account to another.
	 *
	 * @param int $fromAccountID  Account ID
	 * @param string $targetCharName Character name of person receiving credits
	 * @param int $credits Amount of credits
	 */
	public function transferCredits($fromAccountID, $targetCharName, $credits)
	{
		//
		// Return values:
		// -1 = From or to account, one or the other does not exist. (likely the latter.)
		// -2 = Sender has an insufficient balance.
		// -3 = Unknown character.
		// true = Successful transfer
		// false = Error
		//
		
		$sql = "SELECT account_id, char_id, name AS char_name FROM {$this->charMapDatabase}.`char` WHERE `char`.name = ? LIMIT 1";
		$sth = $this->connection->getStatement($sql);
		
		if (!$sth->execute(array($targetCharName)) || !($char=$sth->fetch())) {
			// Unknown character.
			return -3;
		}
		
		$targetAccountID = $char->account_id;
		$targetCharID    = $char->char_id;
		
		
		$sql  = "SELECT COUNT(account_id) AS accounts FROM {$this->loginDatabase}.login WHERE ";
		$sql .= "account_id = ? OR account_id = ? LIMIT 2";
		$sth  = $this->connection->getStatement($sql);
		
		if (!$sth->execute(array($fromAccountID, $targetAccountID)) || $sth->fetch()->accounts != 2) {
			// One or the other, from or to, are non-existent accounts.
			return -1;
		}
		
		if (!$this->loginServer->hasCreditsRecord($fromAccountID)) {
			// Sender has a zero balance.
			return -2;
		}
		
		$creditsTable = Flux::config('FluxTables.CreditsTable');
		$xferTable    = Flux::config('FluxTables.CreditTransferTable');
		
		// Get balance of sender.
		$sql = "SELECT balance FROM {$this->loginDatabase}.$creditsTable WHERE account_id = ? LIMIT 1";
		$sth = $this->connection->getStatement($sql);
		
		if (!$sth->execute(array($fromAccountID))) {
			// Error.
			return false;
		}
		
		if ($sth->fetch()->balance < $credits) {
			// Insufficient balance.
			return -2;
		}
		
		// Take credits from fromAccount first.
		if ($this->loginServer->depositCredits($fromAccountID, -$credits)) {
			// Then deposit to targetAccount next.
			if (!$this->loginServer->depositCredits($targetAccountID, $credits)) {
				// Attempt to restore credits if deposit to toAccount failed.
				$this->loginServer->depositCredits($fromAccountID, $credits);
				return false;
			}
			else {
				$sql  = "INSERT INTO {$this->charMapDatabase}.$xferTable ";
				$sql .= "(from_account_id, target_account_id, target_char_id, amount, transfer_date) ";
				$sql .= "VALUES (?, ?, ?, ?, NOW())";
				$sth  = $this->connection->getStatement($sql);
				
				// Log transfer.
				$sth->execute(array($fromAccountID, $targetAccountID, $targetCharID, $credits));
				
				return true;
			}
		}
		else {
			return false;
		}
	}
	
	/**
	 * Set loginAthenaGroup object.
	 *
	 * @param Flux_LoginAthenaGroup $loginAthenaGroup
	 * @return Flux_LoginAthenaGroup
	 */
	public function setLoginAthenaGroup(Flux_LoginAthenaGroup $loginAthenaGroup)
	{
		$this->loginAthenaGroup = $loginAthenaGroup;
		return $loginAthenaGroup;
	}
	
	/**
	 * Check if a character exists with a particular char ID.
	 *
	 * @param int $charID
	 * @return bool True/false if char exists or doesn't.
	 */
	public function charExists($charID)
	{
		$sql  = "SELECT char_id FROM {$this->charMapDatabase}.`char` WHERE ";
		$sql .= "`char`.char_id = ? LIMIT 1";
		$sth  = $this->connection->getStatement($sql);
		
		if ($sth->execute(array($charID)) && ($char=$sth->fetch()) && $char->char_id) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Check if a charID belongs to an accountID.
	 *
	 * @param int $charID
	 * @param int $accountID
	 * @return bool
	 */
	public function charBelongsToAccount($charID, $accountID)
	{
		$sql  = "SELECT char_id FROM {$this->charMapDatabase}.`char` WHERE ";
		$sql .= "`char`.char_id = ? AND `char`.account_id = ? LIMIT 1";
		$sth  = $this->connection->getStatement($sql);
		
		if ($sth->execute(array($charID, $accountID)) && ($char=$sth->fetch()) && $char->char_id) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Check if char with charID is online.
	 *
	 * @param int $charID
	 * @return bool
	 */
	public function charIsOnline($charID)
	{
		$sql  = "SELECT char_id FROM {$this->charMapDatabase}.`char` WHERE `char`.online > 0 ";
		$sql .= "AND `char`.char_id = ? LIMIT 1";
		$sth  = $this->connection->getStatement($sql);
		
		if ($sth->execute(array($charID)) && ($char=$sth->fetch()) && $char->char_id) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Check if account has any online characters at the moment.
	 *
	 * @param int $accountId
	 * @return bool
	 */
	public function accountHasOnlineChars($accountID)
	{
		$sql  = "SELECT char_id FROM {$this->charMapDatabase}.`char` WHERE `char`.online > 0 ";
		$sql .= "AND `char`.account_id = ? ORDER BY `char`.online DESC LIMIT 1";
		$sth  = $this->connection->getStatement($sql);
		
		if ($sth->execute(array($accountID)) && ($char=$sth->fetch()) && $char->char_id) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Get character data of charID.
	 *
	 * @param int $charID
	 * @return mixed Returns Flux_DataObject or false.
	 */
	public function getCharacter($charID)
	{
		$sql  = "SELECT `char`.* FROM {$this->charMapDatabase}.`char` WHERE ";
		$sql .= "`char`.char_id = ? LIMIT 1";
		$sth  = $this->connection->getStatement($sql);
		
		if ($sth->execute(array($charID)) && ($char=$sth->fetch())) {
			return $char;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Get character prefs.
	 *
	 * @param int $charID Character ID
	 * @param array $prefs Only these prefs?
	 * @return mixed Flux_Config or false.
	 */
	public function getPrefs($charID, array $prefs = array())
	{
		$sql = "SELECT account_id FROM {$this->charMapDatabase}.`char` WHERE char_id = ? LIMIT 1";
		$sth = $this->connection->getStatement($sql);
		
		if ($sth->execute(array($charID)) && ($char=$sth->fetch())) {
			$charPrefsTable = Flux::config('FluxTables.CharacterPrefsTable');
			
			$pref = array();
			$bind = array($char->account_id, $charID);
			$sql  = "SELECT name, value FROM {$this->charMapDatabase}.$charPrefsTable ";
			$sql .= "WHERE account_id = ? AND char_id = ?";
			
			if ($prefs) {
				foreach ($prefs as $p) {
					$pref[] = "name = ?";
					$bind[] = $p;
				}
				$sql .= sprintf(' AND (%s)', implode(' OR ', $pref));
			}
			
			$sth = $this->connection->getStatement($sql);
			
			if ($sth->execute($bind)) {
				$prefsArray = array();
				foreach ($sth->fetchAll() as $p) {
					$prefsArray[$p->name] = $p->value;
				}
				
				return new Flux_Config($prefsArray);
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	
	/**
	 * Set character prefs.
	 *
	 * @param int $charID
	 * @param array $prefsArray pref=>value pairs.
	 * @return bool
	 */
	public function setPrefs($charID, array $prefsArray)
	{
		$sql = "SELECT account_id FROM {$this->charMapDatabase}.`char` WHERE char_id = ? LIMIT 1";
		$sth = $this->connection->getStatement($sql);
		
		if ($sth->execute(array($charID)) && ($char=$sth->fetch())) {
			$charPrefsTable = Flux::config('FluxTables.CharacterPrefsTable');
			
			$pref = array();
			$bind = array($char->account_id, $charID);
			$sql  = "SELECT id, name, value FROM {$this->charMapDatabase}.$charPrefsTable ";
			$sql .= "WHERE account_id = ? AND char_id = ?";
			
			if ($prefsArray) {
				foreach ($prefsArray as $prefName => $prefValue) {
					$pref[] = "name = ?";
					$bind[] = $prefName;
				}
				$sql .= sprintf(' AND (%s)', implode(' OR ', $pref));
			}
			
			$sth = $this->connection->getStatement($sql);
			
			if ($sth->execute($bind)) {
				$prefs  = $sth->fetchAll();
				$update = array();
				
				$usql   = "UPDATE {$this->charMapDatabase}.$charPrefsTable ";
				$usql  .= "SET value = ? WHERE id = ?";
				$usth   = $this->connection->getStatement($usql);
				       
				$isql   = "INSERT INTO {$this->charMapDatabase}.$charPrefsTable ";
				$isql  .= "(account_id, char_id, name, value, create_date) ";
				$isql  .= "VALUES (?, ?, ?, ?, NOW())";
				$isth   = $this->connection->getStatement($isql);
				
				foreach ($prefs as $p) {
					$update[$p->name] = $p->id;
				}
				
				foreach ($prefsArray as $pref => $value) {
					if (array_key_exists($pref, $update)) {
						$id = $update[$pref];
						$usth->execute(array($value, $id));
					}
					else {
						$isth->execute(array($char->account_id, $charID, $pref, $value));
					}
				}
				
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	
	/**
	 * Get a single character pref.
	 *
	 * @param int $charID
	 * @param string $pref
	 * @return mixed string or false.
	 */
	public function getPref($charID, $pref)
	{
		$prefs = $this->getPrefs($charID, array($pref));
		if ($prefs instanceOf Flux_Config) {
			return $prefs->get($pref);
		}
		else {
			return false;
		}
	}
	
	/**
	 * Set a single character pref.
	 *
	 * @param int $charID
	 * @param string $pref
	 * @param string $value
	 * @return bool
	 */
	public function setPref($charID, $pref, $value)
	{
		return $this->setPrefs($charID, array($pref => $value));
	}
	
	/**
	 * Re-set the appearance of a character.
	 *
	 * @param int $charID
	 * @return mixed
	 */
	public function resetLook($charID)
	{
		// Return values:
		// -1 = Character is online, cannot reset.
		// -2 = Unknown character.
		// false = Failed to reset.
		// true  = Successfully reset.
		
		$char = $this->getCharacter($charID);
		
		if (!$char) {
			return -2;
		}
		if ($char->online) {
			return -1;
		}
		
		$sql  = "UPDATE {$this->charMapDatabase}.inventory SET ";
		$sql .= "equip = 0 WHERE char_id = ?";
		$sth  = $this->connection->getStatement($sql);
		
		if (!$sth->execute(array($charID))) {
			return false;
		}
		
		$sql  = "UPDATE {$this->charMapDatabase}.`char` SET ";
		$sql .= "hair = 1, hair_color = 0, clothes_color = 0, weapon = 0, shield = 0, ";
		$sql .= "head_top = 0, head_mid = 0, head_bottom = 0, body = 0 ";
		$sql .= "WHERE char_id = ?";
		$sth  = $this->connection->getStatement($sql);
		
		if (!$sth->execute(array($charID))) {
			return false;
		}
		else {
			return true;
		}
	}
	
	/**
	 * Re-set the position of a character.
	 *
	 * @param int $charID
	 * @return mixed
	 */
	public function resetPosition($charID)
	{
		// Return values:
		// -1 = Character is online, cannot reset.
		// -2 = Reset cannot be done from current map.
		// -3 = Unknown character.
		// false = Failed to reset.
		// true  = Successfully reset.
		
		$char = $this->getCharacter($charID);
		
		if (!$char) {
			return -3;
		}
		if ($char->online) {
			return -1;
		}
		
		$charMap = basename($char->last_map, '.gat');
		foreach ($this->resetDenyMaps as $map) {
			$denyMap = basename($map, '.gat');
			if ($charMap == $denyMap) {
				return -2;
			}
		}
		
		$sql  = "UPDATE {$this->charMapDatabase}.`char` AS ch SET ";
		$sql .= "ch.last_map = ch.save_map, ch.last_x = ch.save_x, ch.last_y = ch.save_y ";
		$sql .= "WHERE ch.char_id = ?";
		$sth  = $this->connection->getStatement($sql);
		
		if ($sth->execute(array($charID))) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Get the current server time, based on the DateTimezone servers.php config.
	 *
	 * @param string $format Similar to that of PHP's date() function.
	 * @return string
	 */
	public function getServerTime($format = 'U')
	{
		$dateTime = date_create('now');
		if ($this->dateTimezone) {
			$dateTime->setTimeZone(new DateTimeZone($this->dateTimezone));
		}
		return $dateTime->format($format);
	}
	
	/**
	 * Check if it currently WoE according to the configured hours and timezone.
	 *
	 * @return bool
	 */
	public function isWoe()
	{
		$serverTime = (int)$this->getServerTime();
		$dayNames   = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
			
		foreach ($this->woeDayTimes as $woeDayTime) {
			$sDay  = $dayNames[$woeDayTime['startingDay']];
			$eDay  = $dayNames[$woeDayTime['endingDay']];
			$start = strtotime("$sDay {$woeDayTime['startingTime']}");
			$end   = strtotime("$eDay {$woeDayTime['endingTime']}");
			
			if ($serverTime > $start && $serverTime < $end) {
				return true;
			}
		}
		
		return false;
	}
}
?>
