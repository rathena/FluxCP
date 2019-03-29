<?php

namespace rAthena\FluxCp\Installer;

use rAthena\FluxCp\Flux;
use rAthena\FluxCp\LoginAthenaGroup;

/**
 *
 */
class MainServer
{
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
	public function __construct(LoginAthenaGroup $loginAthenaGroup)
	{
		$this->loginAthenaGroup = $loginAthenaGroup;
		$this->schemas = Schema::getSchemas($this);

		if (array_key_exists($loginAthenaGroup->serverName, Flux::$athenaServerRegistry)) {
			foreach (Flux::$athenaServerRegistry[$loginAthenaGroup->serverName] as $athena) {
				$this->charMapServers[$athena->serverName] = new CharMapServer($this, $athena);
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
