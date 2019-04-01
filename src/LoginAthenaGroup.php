<?php

namespace rAthena\FluxCp;

/**
 * Basically acts as an uppermost container holding the LoginServer and Athena
 * instances on a top level.
 */
class LoginAthenaGroup
{
	/**
	 * Global server name, representing all Athena servers.
	 *
	 * @access public
	 * @var string
	 */
	public $serverName;

	/**
	 * Connection to the MySQL server.
	 *
	 * @access public
	 * @var Connection
	 */
	public $connection;

	/**
	 * Main login server for the contained Athena servers.
	 *
	 * @access public
	 * @var LoginServer
	 */
	public $loginServer;

	/**
	 * Database used for the login-related SQL operations.
	 *
	 * @access public
	 * @var string
	 */
	public $loginDatabase;

	/**
	 * Logs database.
	 *
	 * @access public
	 * @var string
	 */
	public $logsDatabase;

	/**
	 * Array of Athena instances.
	 *
	 * @access public
	 * @var array
	 */
	public $athenaServers = array();

	/**
	 * Construct new LoginAthenaGroup instance.
	 *
	 * @access public
	 */
	public function __construct($serverName, Connection $connection, LoginServer $loginServer, array $athenaServers = array())
	{
		$this->serverName = $serverName;
		$this->connection = $connection;
		$this->loginServer = $loginServer;
		$this->loginDatabase = $loginServer->config->getDatabase();
		$this->logsDatabase = $connection->logsDbConfig->getDatabase();

		// Assign connection to LoginServer, used mainly to enable
		// authentication feature.
		$this->loginServer->setConnection($connection);

		foreach ($athenaServers as $athenaServer) {
			$this->addAthenaServer($athenaServer);
		}
	}

	/**
	 * Add an Athena instance to the current collection.
	 *
	 * @return mixed Returns false if login servers aren't identical.
	 * @access public
	 */
	public function addAthenaServer(Athena $athenaServer)
	{
		if ($athenaServer->loginServer === $this->loginServer) {
			$athenaServer->setLoginAthenaGroup($this);
			$athenaServer->setConnection($this->connection);
			$this->athenaServers[] = $athenaServer;
			return $this->athenaServers;
		} else {
			return false;
		}
	}

	/**
	 * See LoginServer->isAuth().
	 *
	 * @param string $username
	 * @param string $password
	 * @return bool
	 * @access public
	 */
	public function isAuth($username, $password)
	{
		return $this->loginServer->isAuth($username, $password);
	}
}
