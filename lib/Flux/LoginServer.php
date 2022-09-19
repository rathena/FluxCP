<?php
require_once 'Flux/BaseServer.php';
require_once 'Flux/RegisterError.php';

/**
 * Represents an rAthena Login Server.
 */
class Flux_LoginServer extends Flux_BaseServer {
	/**
	 * Connection to the MySQL server.
	 *
	 * @access public
	 * @var Flux_Connection
	 */
	public $connection;
	
	/**
	 * Login server database.
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
	 * Web server database. (is not set until setConnection() is called.)
	 *
	 * @access public
	 * @var string
	 */
	public $webDatabase;
	
	/**
	 * Overridden to add custom properties.
	 *
	 * @access public
	 */
	public function __construct(Flux_Config $config)
	{
		parent::__construct($config);
		$this->loginDatabase = $config->getDatabase();
	}
	
	/**
	 * Set the connection object to be used for this LoginServer instance.
	 *
	 * @param Flux_Connection $connection
	 * @return Flux_Connection
	 * @access public
	 */
	public function setConnection(Flux_Connection $connection)
	{
		$this->connection   = $connection;
		$this->logsDatabase = $connection->logsDbConfig->getDatabase();
		$this->webDatabase  = $connection->webDbConfig->getDatabase();
		
		return $connection;
	}
	
	/**
	 * Validate credentials against the login server's database information.
	 *
	 * @param string $username Ragnarok account username.
	 * @param string $password Ragnarok account password.
	 * @return bool True/false if valid or invalid.
	 * @access public
	 */
	public function isAuth($username, $password)
	{
		
		if (trim($username) == '' || trim($password) == '') {
			return false;
		}

     	if ($this->config->get('UseMD5')) {
			$password = Flux::hashPassword($password);
		}
        
		$sql  = "SELECT userid FROM {$this->loginDatabase}.login WHERE sex != 'S' AND group_id >= 0 ";
		if ($this->config->getNoCase()) {
			$sql .= 'AND LOWER(userid) = LOWER(?) ';
		}
		else {
			$sql .= 'AND CAST(userid AS BINARY) = ? ';
		}
		$sql .= "AND user_pass = ? LIMIT 1";
		$sth  = $this->connection->getStatement($sql);
		$sth->execute(array($username, $password));
		
		$res = $sth->fetch();
		if ($res) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function register($username, $password, $confirmPassword, $email,$email2, $gender, $birthdate, $securityCode)
	{
		if (preg_match('/[^' . Flux::config('UsernameAllowedChars') . ']/', $username)) {
			throw new Flux_RegisterError('Invalid character(s) used in username', Flux_RegisterError::INVALID_USERNAME);
		}
		elseif (strlen($username) < Flux::config('MinUsernameLength')) {
			throw new Flux_RegisterError('Username is too short', Flux_RegisterError::USERNAME_TOO_SHORT);
		}
		elseif (strlen($username) > Flux::config('MaxUsernameLength')) {
			throw new Flux_RegisterError('Username is too long', Flux_RegisterError::USERNAME_TOO_LONG);
		}
		elseif (!Flux::config('AllowUserInPassword') && stripos($password, $username) !== false) {
			throw new Flux_RegisterError('Password contains username', Flux_RegisterError::PASSWORD_HAS_USERNAME);
		}
		elseif (!ctype_graph($password)) {
			throw new Flux_RegisterError('Invalid character(s) used in password', Flux_RegisterError::INVALID_PASSWORD);
		}
		elseif (strlen($password) < Flux::config('MinPasswordLength')) {
			throw new Flux_RegisterError('Password is too short', Flux_RegisterError::PASSWORD_TOO_SHORT);
		}
		elseif (strlen($password) > Flux::config('MaxPasswordLength')) {
			throw new Flux_RegisterError('Password is too long', Flux_RegisterError::PASSWORD_TOO_LONG);
		}
		elseif ($password !== $confirmPassword) {
			throw new Flux_RegisterError('Passwords do not match', Flux_RegisterError::PASSWORD_MISMATCH);
		}
		elseif (Flux::config('PasswordMinUpper') > 0 && preg_match_all('/[A-Z]/', $password, $matches) < Flux::config('PasswordMinUpper')) {
			throw new Flux_RegisterError('Passwords must contain at least ' . intval(Flux::config('PasswordMinUpper')) . ' uppercase letter(s)', Flux_RegisterError::PASSWORD_NEED_UPPER);
		}
		elseif (Flux::config('PasswordMinLower') > 0 && preg_match_all('/[a-z]/', $password, $matches) < Flux::config('PasswordMinLower')) {
			throw new Flux_RegisterError('Passwords must contain at least ' . intval(Flux::config('PasswordMinLower')) . ' lowercase letter(s)', Flux_RegisterError::PASSWORD_NEED_LOWER);
		}
		elseif (Flux::config('PasswordMinNumber') > 0 && preg_match_all('/[0-9]/', $password, $matches) < Flux::config('PasswordMinNumber')) {
			throw new Flux_RegisterError('Passwords must contain at least ' . intval(Flux::config('PasswordMinNumber')) . ' number(s)', Flux_RegisterError::PASSWORD_NEED_NUMBER);
		}
		elseif (Flux::config('PasswordMinSymbol') > 0 && preg_match_all('/[^A-Za-z0-9]/', $password, $matches) < Flux::config('PasswordMinSymbol')) {
			throw new Flux_RegisterError('Passwords must contain at least ' . intval(Flux::config('PasswordMinSymbol')) . ' symbol(s)', Flux_RegisterError::PASSWORD_NEED_SYMBOL);
		}
		elseif (!preg_match('/^(.+?)@(.+?)$/', $email)) {
			throw new Flux_RegisterError('Invalid e-mail address', Flux_RegisterError::INVALID_EMAIL_ADDRESS);
		}
		elseif ($email!==$email2) {
			throw new Flux_RegisterError('Email do not match', Flux_RegisterError::INVALID_EMAIL_CONF);
		}		
		elseif (!in_array(strtoupper($gender), array('M', 'F'))) {
			throw new Flux_RegisterError('Invalid gender', Flux_RegisterError::INVALID_GENDER);
		}
		elseif (($birthdatestamp = strtotime($birthdate)) === false || date('Y-m-d', $birthdatestamp) != $birthdate) {
			throw new Flux_RegisterError('Invalid birthdate', Flux_RegisterError::INVALID_BIRTHDATE);
		}
		elseif (Flux::config('UseCaptcha')) {
			if (Flux::config('EnableReCaptcha')) {
				if(isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response'] != ""){
					$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".Flux::config('ReCaptchaPrivateKey')."&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']);
				}
				$responseKeys = json_decode($response,true);
				if(intval($responseKeys["success"]) !== 1) {
					throw new Flux_RegisterError('Invalid security code', Flux_RegisterError::INVALID_SECURITY_CODE);
				}
			}
			elseif (strtolower($securityCode) !== strtolower(Flux::$sessionData->securityCode)) {
				throw new Flux_RegisterError('Invalid security code', Flux_RegisterError::INVALID_SECURITY_CODE);
			}
		}
		
		$sql  = "SELECT userid FROM {$this->loginDatabase}.login WHERE ";
		if ($this->config->getNoCase()) {
			$sql .= 'LOWER(userid) = LOWER(?) ';
		}
		else {
			$sql .= 'BINARY userid = ? ';
		}
		$sql .= 'LIMIT 1';
		$sth  = $this->connection->getStatement($sql);
		$sth->execute(array($username));
		
		$res = $sth->fetch();
		if ($res) {
			throw new Flux_RegisterError('Username is already taken', Flux_RegisterError::USERNAME_ALREADY_TAKEN);
		}
		
		if (!Flux::config('AllowDuplicateEmails')) {
			$sql = "SELECT email FROM {$this->loginDatabase}.login WHERE email = ? LIMIT 1";
			$sth = $this->connection->getStatement($sql);
			$sth->execute(array($email));

			$res = $sth->fetch();
			if ($res) {
				throw new Flux_RegisterError('E-mail address is already in use', Flux_RegisterError::EMAIL_ADDRESS_IN_USE);
			}
		}
		
		if ($this->config->getUseMD5()) {
			$password = Flux::hashPassword($password);
		}
		
		$sql = "INSERT INTO {$this->loginDatabase}.login (userid, user_pass, email, sex, group_id, birthdate) VALUES (?, ?, ?, ?, ?, ?)";
		$sth = $this->connection->getStatement($sql);
		$res = $sth->execute(array($username, $password, $email, $gender, (int)$this->config->getGroupID(), date('Y-m-d', $birthdatestamp)));
		
		if ($res) {
			$idsth = $this->connection->getStatement("SELECT LAST_INSERT_ID() AS account_id");
			$idsth->execute();
			
			$idres = $idsth->fetch();
			$createTable = Flux::config('FluxTables.AccountCreateTable');
			
			$sql  = "INSERT INTO {$this->loginDatabase}.{$createTable} (account_id, userid, user_pass, sex, email, reg_date, reg_ip, confirmed) ";
			$sql .= "VALUES (?, ?, ?, ?, ?, NOW(), ?, 1)";
			$sth  = $this->connection->getStatement($sql);
			
			$sth->execute(array($idres->account_id, $username, $password, $gender, $email, $_SERVER['REMOTE_ADDR']));
			return $idres->account_id;
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function temporarilyBan($bannedBy, $banReason, $accountID, $until)
	{
		$table = Flux::config('FluxTables.AccountBanTable');
		
		$sql  = "INSERT INTO {$this->loginDatabase}.$table (account_id, banned_by, ban_type, ban_until, ban_date, ban_reason) ";
		$sql .= "VALUES (?, ?, 1, ?, NOW(), ?)";
		$sth  = $this->connection->getStatement($sql);
		
		if ($sth->execute(array($accountID, $bannedBy, $until, $banReason))) {
			$ts   = strtotime($until);
			$sql  = "UPDATE {$this->loginDatabase}.login SET state = 0, unban_time = '$ts' WHERE account_id = ?";
			$sth  = $this->connection->getStatement($sql);
			return $sth->execute(array($accountID));
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function permanentlyBan($bannedBy, $banReason, $accountID)
	{
		$table = Flux::config('FluxTables.AccountBanTable');
		
		$sql  = "INSERT INTO {$this->loginDatabase}.$table (account_id, banned_by, ban_type, ban_until, ban_date, ban_reason) ";
		$sql .= "VALUES (?, ?, 2, '9999-12-31 23:59:59', NOW(), ?)";
		$sth  = $this->connection->getStatement($sql);
		
		if ($sth->execute(array($accountID, $bannedBy, $banReason))) {
			$sql  = "UPDATE {$this->loginDatabase}.login SET state = 5, unban_time = 0 WHERE account_id = ?";
			$sth  = $this->connection->getStatement($sql);
			return $sth->execute(array($accountID));
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function unban($unbannedBy, $unbanReason, $accountID)
	{
		$table = Flux::config('FluxTables.AccountBanTable');
		$createTable = Flux::config('FluxTables.AccountCreateTable');
		
		$sql  = "INSERT INTO {$this->loginDatabase}.$table (account_id, banned_by, ban_type, ban_until, ban_date, ban_reason) ";
		$sql .= "VALUES (?, ?, 0, '1000-01-01 00:00:00', NOW(), ?)";
		$sth  = $this->connection->getStatement($sql);
		
		if ($sth->execute(array($accountID, $unbannedBy, $unbanReason))) {
			$sql  = "UPDATE {$this->loginDatabase}.$createTable SET confirmed = 1, confirm_expire = NULL WHERE account_id = ?";
			$sth  = $this->connection->getStatement($sql);
			$sth->execute(array($accountID));
			
			$sql  = "UPDATE {$this->loginDatabase}.login SET state = 0, unban_time = 0 WHERE account_id = ?";
			$sth  = $this->connection->getStatement($sql);
			return $sth->execute(array($accountID));
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function getBanInfo($accountID)
	{
		$table = Flux::config('FluxTables.AccountBanTable');
		$col   = "$table.id, $table.account_id, $table.banned_by, $table.ban_type, ";
		$col  .= "$table.ban_until, $table.ban_date, $table.ban_reason, login.userid";
		$sql   = "SELECT $col FROM {$this->loginDatabase}.$table ";
		$sql  .= "LEFT OUTER JOIN {$this->loginDatabase}.login ON login.account_id = $table.banned_by ";
		$sql  .= "WHERE $table.account_id = ? ORDER BY $table.ban_date DESC ";
		$sth   = $this->connection->getStatement($sql);
		$res   = $sth->execute(array($accountID));
		
		if ($res) {
			$ban = $sth->fetchAll();
			return $ban;
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function addIpBan($bannedBy, $banReason, $unbanTime, $ipAddress)
	{
		$table = Flux::config('FluxTables.IpBanTable');
		
		$sql  = "INSERT INTO {$this->loginDatabase}.$table (ip_address, banned_by, ban_type, ban_until, ban_date, ban_reason) ";
		$sql .= "VALUES (?, ?, 1, ?, NOW(), ?)";
		$sth  = $this->connection->getStatement($sql);
		
		if ($sth->execute(array($ipAddress, $bannedBy, $unbanTime, $banReason))) {
			$sql  = "INSERT INTO {$this->loginDatabase}.ipbanlist (list, reason, rtime, btime) ";
			$sql .= "VALUES (?, ?, ?, NOW())";
			$sth  = $this->connection->getStatement($sql);
			return $sth->execute(array($ipAddress, $banReason, $unbanTime));
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function removeIpBan($unbannedBy, $unbanReason, $ipAddress)
	{
		$table = Flux::config('FluxTables.IpBanTable');
		
		$sql  = "INSERT INTO {$this->loginDatabase}.$table (ip_address, banned_by, ban_type, ban_until, ban_date, ban_reason) ";
		$sql .= "VALUES (?, ?, 0, '1000-01-01 00:00:00', NOW(), ?)";
		$sth  = $this->connection->getStatement($sql);
		
		if ($sth->execute(array($ipAddress, $unbannedBy, $unbanReason))) {
			$sql  = "DELETE FROM {$this->loginDatabase}.ipbanlist WHERE list = ?";
			$sth  = $this->connection->getStatement($sql);
			return $sth->execute(array($ipAddress));
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function hasCreditsRecord($accountID)
	{
		$creditsTable = Flux::config('FluxTables.CreditsTable');
		
		$sql = "SELECT COUNT(account_id) AS hasRecord FROM {$this->loginDatabase}.$creditsTable WHERE account_id = ?";
		$sth = $this->connection->getStatement($sql);
		
		$sth->execute(array($accountID));
		
		if ($sth->fetch()->hasRecord) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function depositCredits($targetAccountID, $credits, $donationAmount = null)
	{
		$sql = "SELECT COUNT(account_id) AS accountExists FROM {$this->loginDatabase}.login WHERE account_id = ?";
		$sth = $this->connection->getStatement($sql);
		
		if (!$sth->execute(array($targetAccountID)) || !$sth->fetch()->accountExists) {
			return false; // Account doesn't exist.
		}
		
		$creditsTable = Flux::config('FluxTables.CreditsTable');
		
		if (!$this->hasCreditsRecord($targetAccountID)) {
			$fields = 'account_id, balance';
			$values = '?, ?';
			
			if (!is_null($donationAmount)) {
				$fields .= ', last_donation_date, last_donation_amount';
				$values .= ', NOW(), ?';
			}
			
			$sql  = "INSERT INTO {$this->loginDatabase}.$creditsTable ($fields) VALUES ($values)";
			$sth  = $this->connection->getStatement($sql);
			$vals = array($targetAccountID, $credits);
			
			if (!is_null($donationAmount)) {
				$vals[] = $donationAmount;
			}
			
			return $sth->execute($vals);
		}
		else {
			$vals = array();
			$sql  = "UPDATE {$this->loginDatabase}.$creditsTable SET balance = balance + ? ";

			if (!is_null($donationAmount)) {
				$sql .= ", last_donation_date = NOW(), last_donation_amount = ? ";
			}
			
			$vals[] = $credits;
			if (!is_null($donationAmount)) {
				$vals[] = $donationAmount;
			}
			$vals[] = $targetAccountID;
			
			$sql .= "WHERE account_id = ?";
			$sth  = $this->connection->getStatement($sql);
			
			return $sth->execute($vals);
		}
	}
	
	/**
	 *
	 */
	public function getPrefs($accountID, array $prefs = array())
	{
		$sql = "SELECT account_id FROM {$this->loginDatabase}.`login` WHERE account_id = ? LIMIT 1";
		$sth = $this->connection->getStatement($sql);
		
		if ($sth->execute(array($accountID)) && ($char=$sth->fetch())) {
			$accountPrefsTable = Flux::config('FluxTables.AccountPrefsTable');
			
			$pref = array();
			$bind = array($accountID);
			$sql  = "SELECT name, value FROM {$this->loginDatabase}.$accountPrefsTable ";
			$sql .= "WHERE account_id = ?";
			
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
	 *
	 */
	public function setPrefs($accountID, array $prefsArray)
	{
		$sql = "SELECT account_id FROM {$this->loginDatabase}.`login` WHERE account_id = ? LIMIT 1";
		$sth = $this->connection->getStatement($sql);
		
		if ($sth->execute(array($accountID)) && ($char=$sth->fetch())) {
			$accountPrefsTable = Flux::config('FluxTables.AccountPrefsTable');
			
			$pref = array();
			$bind = array($accountID);
			$sql  = "SELECT id, name, value FROM {$this->loginDatabase}.$accountPrefsTable ";
			$sql .= "WHERE account_id = ?";
			
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
				
				$usql   = "UPDATE {$this->loginDatabase}.$accountPrefsTable ";
				$usql  .= "SET value = ? WHERE id = ?";
				$usth   = $this->connection->getStatement($usql);
				       
				$isql   = "INSERT INTO {$this->loginDatabase}.$accountPrefsTable ";
				$isql  .= "(account_id, name, value, create_date) ";
				$isql  .= "VALUES (?, ?, ?, NOW())";
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
						$isth->execute(array($accountID, $pref, $value));
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
	 *
	 */
	public function getPref($accountID, $pref)
	{
		$prefs = $this->getPrefs($accountID, array($pref));
		if ($prefs instanceOf Flux_Config) {
			return $prefs->get($pref);
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function setPref($accountID, $pref, $value)
	{
		return $this->setPrefs($accountID, array($pref => $value));
	}
	
	/**
	 *
	 */
	public function isIpBanned($ip = null)
	{
		if (is_null($ip)) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		$ip = trim($ip);
		if (!preg_match('/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/', $ip, $m)) {
			// Invalid IP.
			return false;
		}
		
		$sql  = "SELECT list FROM {$this->loginDatabase}.ipbanlist WHERE ";
		$sql .= "rtime > NOW() AND (list = ? OR list = ? OR list = ? OR list = ?) LIMIT 1";
		$sth  = $this->connection->getStatement($sql);
		
		$list = array(
			sprintf('%u.*.*.*', $m[1]),
			sprintf('%u.%u.*.*', $m[1], $m[2]),
			sprintf('%u.%u.%u.*', $m[1], $m[2], $m[3]),
			sprintf('%u.%u.%u.%u', $m[1], $m[2], $m[3], $m[4])
		);
		
		$sth->execute($list);
		$ipban = $sth->fetch();
		
		if ($ipban) {
			return true;
		}
		else {
			return false;
		}
	}
}
?>
