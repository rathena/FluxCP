<?php
namespace rAthena\Fluxcp\Installer;

use rAthena\FluxCp\Athena;

/**
 *
 */
class CharMapServer
{
	/**
	 *
	 */
	public $mainServer;

	/**
	 *
	 */
	public $athena;

	/**
	 *
	 */
	public $schemas;

	/**
	 *
	 */
	public function __construct(MainServer $mainServer, Athena $athena)
	{
		$this->mainServer = $mainServer;
		$this->athena = $athena;
		$this->schemas = Schema::getSchemas($mainServer, $this);
	}
}
