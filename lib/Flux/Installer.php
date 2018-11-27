<?php
require_once 'Flux/Installer/MainServer.php';

/**
 *
 */
class Flux_Installer {
	/**
	 *
	 */
	private static $installer;
	
	/**
	 *
	 */
	public $servers = array();
	
	/**
	 *
	 */
	private function __construct()
	{
		foreach (Flux::$loginAthenaGroupRegistry as $serverName => $loginAthenaGroup) {
			$this->servers[$serverName] = new Flux_Installer_MainServer($loginAthenaGroup);
		}
	}
	
	/**
	 *
	 */
	public static function getInstance()
	{
		if (!self::$installer) {
			self::$installer = new Flux_Installer();
		}
		return self::$installer;
	}
	
	/**
	 *
	 */
	public function updateNeeded()
	{
		foreach ($this->servers as $mainServer) {
			foreach ($mainServer->schemas as $schema) {
				if (!$schema->isLatest()) {
					return true;
				}
			}
			foreach ($mainServer->charMapServers as $charMapServer) {
				foreach ($charMapServer->schemas as $schema) {
					if (!$schema->isLatest()) {
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/**
	 *
	 */
	public function updateAll()
	{
		foreach ($this->servers as $mainServer) {
			foreach ($mainServer->schemas as $schema) {
				if (!$schema->isLatest()) {
					$schema->update();
				}
			}
			foreach ($mainServer->charMapServers as $charMapServer) {
				foreach ($charMapServer->schemas as $schema) {
					if (!$schema->isLatest()) {
						$schema->update();
					}
				}
			}
		}
	}
}
?>
