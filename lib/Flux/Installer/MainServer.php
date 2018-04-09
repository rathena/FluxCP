<?php
require_once 'Flux/Installer/Schema.php';
require_once 'Flux/Installer/CharMapServer.php';

/**
 *
 */
class Flux_Installer_MainServer {
	/**
	 *
	 */
	public $loginAthenaGroup;
	
	/**
	 *
	 */
	public $charMapServers = array();
	
	/**
	 *
	 */
	public $schemas;
	
	/**
	 *
	 */
	public function __construct(Flux_LoginAthenaGroup $loginAthenaGroup)
	{
		$this->loginAthenaGroup  = $loginAthenaGroup;
		$this->schemas           = Flux_Installer_Schema::getSchemas($this);
		
		if (array_key_exists($loginAthenaGroup->serverName, Flux::$athenaServerRegistry)) {
			foreach (Flux::$athenaServerRegistry[$loginAthenaGroup->serverName] as $athena) {
				$this->charMapServers[$athena->serverName] = new Flux_Installer_CharMapServer($this, $athena);
			}
		}
	}
	
	/**
	 *
	 */
	public function updateAll()
	{
		foreach ($this->schemas as $schema) {
			if (!$schema->isLatest()) {
				$schema->update();
			}
		}
		foreach ($this->charMapServers as $charMapServer) {
			foreach ($charMapServer->schemas as $schema) {
				if (!$schema->isLatest()) {
					$schema->update();
				}
			}
		}
	}
}
?>
