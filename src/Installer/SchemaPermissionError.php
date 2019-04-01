<?php
namespace rAthena\FluxCp\Installer;

use rAthena\FluxCp\Error;

class SchemaPermissionError extends Error
{
	public $schemaFile;
	public $databaseName;
	public $mainServerName;
	public $charMapServerName;
	public $query;

	public function __construct($message, $schemaFile, $databaseName, $mainServerName, $charMapServerName, $query)
	{
		parent::__construct($message);

		$this->schemaFile = $schemaFile;
		$this->databaseName = $databaseName;
		$this->mainServerName = $mainServerName;
		$this->charMapServerName = $charMapServerName;
		$this->query = $query;
	}

	public function isLoginDbSchema()
	{
		return $this->mainServerName && !$this->charMapServerName;
	}

	public function isCharMapDbSchema()
	{
		return $this->mainServerName && $this->charMapServerName;
	}
}
