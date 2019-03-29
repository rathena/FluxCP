<?php

namespace rAthena\FluxCp;

use rAthena\FluxCp\Installer\MainServer;

/**
 *
 */
class Installer
{
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
			$this->servers[$serverName] = new MainServer($loginAthenaGroup);
		}
	}

	/**
	 *
	 */
	public static function getInstance()
	{
		if (!self::$installer) {
			self::$installer = new Installer();
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
