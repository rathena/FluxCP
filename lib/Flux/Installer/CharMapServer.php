<?php
require_once 'Flux/Installer/Schema.php';

/**
 *
 */
class Flux_Installer_CharMapServer {
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
	public function __construct(Flux_Installer_MainServer $mainServer, Flux_Athena $athena)
	{
		$this->mainServer = $mainServer;
		$this->athena     = $athena;
		$this->schemas    = Flux_Installer_Schema::getSchemas($mainServer, $this);
	}
}
?>
