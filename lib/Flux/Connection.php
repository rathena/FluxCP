<?php
require_once 'Flux/Connection/Statement.php';
require_once 'Flux/DataObject.php';

/**
 * The connection class acts more like a container, or connection manager and
 * anything else, really. It's true that it does establish connections to the
 * database, but it exists for the purpose of containing and separating the
 * connections to TWO databases, the logs database from which all the rA logs
 * are stored, and the main database where everything else is stored.
 */
class Flux_Connection {
	/**
	 * Main database configuration object.
	 *
	 * @access public
	 * @var Flux_Config
	 */
	public $dbConfig;
	
	/**
	 * Logs database configuration object.
	 *
	 * @access public
	 * @var Flux_Config
	 */
	public $logsDbConfig;
	
	/**
	 * Logs database configuration object.
	 *
	 * @access public
	 * @var Flux_Config
	 */
	public $webDbConfig;
	
	/**
	 * @access private
	 * @var PDO
	 */
	private $pdoMain;
	
	/**
	 * @access private
	 * @var PDO
	 */
	private $pdoLogs;
	
	/**
	 * @access private
	 * @var PDO
	 */
	private $pdoWeb;
	
	/**
	 * @param Flux_Config $dbConfig
	 * @param Flux_Config $logsDbConfig
	 * @param Flux_Config $webDbConfig
	 * @access public
	 */
	public function __construct(Flux_Config $dbConfig, Flux_Config $logsDbConfig, Flux_Config $webDbConfig)
	{
		$this->dbConfig     = $dbConfig;
		$this->logsDbConfig = $logsDbConfig;
		$this->webDbConfig  = $webDbConfig;
	}
	
	/**
	 * Establish connection to server based on config.
	 *
	 * @param Flux_Config $dbConfig
	 * @return PDO
	 * @access private
	 */
	private function connect(Flux_Config $dbConfig)
	{
		$dsn = 'mysql:';
		
		// Differentiate between a socket-type connection or an ip:port
		// connection.
		if ($sock=$dbConfig->getSocket()) {
			$dsn .= "unix_socket=$sock";
		}
		else {
			$dsn .= 'host='.$dbConfig->getHostname();
			if ($port=$dbConfig->getPort()) {
				$dsn .= ";port=$port";
			}
		}
		
		// May or may not have a database name specified.
		if ($dbName=$dbConfig->getDatabase()) {
			$dsn .= ";dbname=$dbName";
		}
		
		$persistent = array(PDO::ATTR_PERSISTENT => (bool)$dbConfig->getPersistent());
		return new PDO($dsn, $dbConfig->getUsername(), $dbConfig->getPassword(), $persistent);
	}
	
	/**
	 * Get the PDO instance for the main database server connection.
	 *
	 * @return PDO
	 * @access private
	 */
	private function getConnection()
	{
		if (!$this->pdoMain) {
			// Establish connection for main databases.
			$pdoMain       = $this->connect($this->dbConfig);
			$this->pdoMain = $pdoMain;
			
			if ($encoding=$this->dbConfig->getEncoding()) {
				$sth = $this->getStatement("SET NAMES ?");
				$sth->execute(array($encoding));
			}
			if ($timezone=$this->dbConfig->getTimezone()) {
				$sth = $this->getStatement("SET time_zone = ?");
				$sth->execute(array($timezone));
			}
		}
		return $this->pdoMain;
	}
	
	/**
	 * Get the PDO instance for the logs database server connection.
	 *
	 * @return PDO
	 * @access private
	 */
	private function getLogsConnection()
	{
		if (!$this->pdoLogs) {
			// Establish separate connection just for the log database.
			$pdoLogs       = $this->connect($this->logsDbConfig);
			$this->pdoLogs = $pdoLogs;
			
			if ($encoding=$this->logsDbConfig->getEncoding()) {
				$sth = $this->getStatementForLogs("SET NAMES ?");
				$sth->execute(array($encoding));
			}
			if ($timezone=$this->logsDbConfig->getTimezone()) {
				$sth = $this->getStatementForLogs("SET time_zone = ?");
				$sth->execute(array($timezone));
			}
		}
		return $this->pdoLogs;
	}
	
	/**
	 * Get the PDO instance for the web server database server connection.
	 *
	 * @return PDO
	 * @access private
	 */
	private function getWebConnection()
	{
		if (!$this->pdoWeb) {
			// Establish separate connection just for the web server database.
			$pdoWeb       = $this->connect($this->webDbConfig);
			$this->pdoWeb = $pdoWeb;
			
			if ($encoding=$this->webDbConfig->getEncoding()) {
				$sth = $this->getStatementForWeb("SET NAMES ?");
				$sth->execute(array($encoding));
			}
			if ($timezone=$this->webDbConfig->getTimezone()) {
				$sth = $this->getStatementForWeb("SET time_zone = ?");
				$sth->execute(array($timezone));
			}
		}
		return $this->pdoWeb;
	}
	
	/**
	 * Select database to use.
	 *
	 * @param string $dbName
	 * @return mixed
	 * @access public
	 */
	public function useDatabase($dbName)
	{
		if ($this->pdoMain) {
			return $this->getStatement("USE $dbName")->execute();
		}
		else {
			return false;
		}
	}
	
	/**
	 * Instanciate a PDOStatement without obtaining a PDO handler before-hand.
	 *
	 * @return PDOStatement
	 * @access public
	 */
	public function getStatement($statement, $options = array())
	{
		$dbh = $this->getConnection();
		$sth = $dbh->prepare($statement, $options);
		@$sth->setFetchMode(PDO::FETCH_CLASS, 'Flux_DataObject', array(null, array('dbconfig' => $this->dbConfig)));
		
		if ($sth) {
			return new Flux_Connection_Statement($sth);
		}
		else {
			return false;
		}
	}
	
	/**
	 * Instanciate a PDOStatement without obtaining a PDO handler before-hand.
	 *
	 * @return PDOStatement
	 * @access public
	 */
	public function getStatementForLogs($statement, $options = array())
	{
		$dbh = $this->getLogsConnection();
		$sth = $dbh->prepare($statement, $options);
		@$sth->setFetchMode(PDO::FETCH_CLASS, 'Flux_DataObject', array(null, array('dbconfig' => $this->logsDbConfig)));
		
		if ($sth) {
			return new Flux_Connection_Statement($sth);
		}
		else {
			return false;
		}
	}
	
	/**
	 * Instanciate a PDOStatement without obtaining a PDO handler before-hand.
	 *
	 * @return PDOStatement
	 * @access public
	 */
	public function getStatementForWeb($statement, $options = array())
	{
		$dbh = $this->getWebConnection();
		$sth = $dbh->prepare($statement, $options);
		@$sth->setFetchMode(PDO::FETCH_CLASS, 'Flux_DataObject', array(null, array('dbconfig' => $this->webDbConfig)));
		
		if ($sth) {
			return new Flux_Connection_Statement($sth);
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function reconnectAs($username, $password)
	{
		if ($this->pdoMain) {
			$this->pdoMain = null;
		}
		
		$this->dbConfig->setPersistent(false);
		$this->dbConfig->setUsername($username);
		$this->dbConfig->setPassword($password);

		return true;
	}
	
	/**
	 *
	 */
	public function isCaseSensitive($database, $table, $column, $useLogsConnection = false)
	{
		$stm = $useLogsConnection ? 'getStatementForLogs' : 'getStatement';
		$sql = 'SELECT COLLATION_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?';
		$sth = $this->$stm($sql);
		$sth->execute(array($database, $table, $column));
		
		$row = $sth->fetch();
		if (preg_match('/_ci$/', $row->COLLATION_NAME)) {
			return false;
		}
		else {
			return true;
		}
	}
}
?>
