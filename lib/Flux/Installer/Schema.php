<?php
require_once 'Flux/Installer/SchemaPermissionError.php';

/**
 *
 */
class Flux_Installer_Schema {	
	/**
	 *
	 */
	public $mainServerName;
	
	/**
	 *
	 */
	public $charMapServerName;
	
	/**
	 *
	 */
	public $databaseName;
	
	/**
	 *
	 */
	public $connection;
	
	/**
	 *
	 */
	public $availableSchemaDir;
	
	/**
	 *
	 */
	public $installedSchemaDir;
	
	/**
	 *
	 */
	public $schemaInfo;
	
	/**
	 *
	 */
	public $latestVersion;
	
	/**
	 *
	 */
	public $latestVersionInstalled;
	
	/**
	 *
	 */
	public $versionInstalled = false;
	
	/**
	 *
	 */
	private function __construct(array $dataArray)
	{
		$this->mainServerName     = $dataArray['mainServerName'];
		$this->charMapServerName  = $dataArray['charMapServerName'];
		$this->databaseName       = $dataArray['databaseName'];
		$this->connection         = $dataArray['connection'];
		$this->availableSchemaDir = $dataArray['availableSchemaDir'];
		$this->installedSchemaDir = $dataArray['installedSchemaDir'];
		$this->schemaInfo         = $dataArray['schemaInfo'];
		
		ksort($this->schemaInfo['versions']);
		$this->determineInstalledVersions();
	}
	
	/**
	 *
	 */
	protected function determineInstalledVersions()
	{
		foreach ($this->schemaInfo['versions'] as $version => $installed) {
			if ($installed) {
				$this->versionInstalled = $version;
			}
			
			$this->latestVersion = $version;
			$this->latestVersionInstalled = $installed;
		}
	}
	
	/**
	 *
	 */
	public function install($version)
	{
		if (!array_key_exists($version, $this->schemaInfo['versions']) || !$this->schemaInfo['versions'][$version]) {
			// Switch database.
			$this->connection->useDatabase($this->databaseName);
			
			// Get schema content.
			$sql = file_get_contents($this->schemaInfo['files'][$version]);
			$sth = $this->connection->getStatement($sql);
			
			// Execute.
			$sth->execute();
			
			if ($sth->errorCode()) {
				$errorInfo = $sth->errorInfo();
				if (count($errorInfo) > 1) {
					list ($sqlstate, $errnum, $errmsg) = $errorInfo;
				} else {
					$errmsg = implode(', ', $errorInfo);
				}
				
				if (!empty($errnum) && $errnum == 1045) {
					throw new Flux_Error("Critical MySQL error in Installer/Updater: $errnum: $errmsg");
				}
				elseif (!empty($errnum) && $errnum == 1142) {
					// Bail-out.
					$message = "MySQL error: $errmsg\n\n";
					throw new Flux_Installer_SchemaPermissionError(
						$message,
						$this->schemaInfo['files'][$version],
						$this->databaseName,
						$this->mainServerName,
						$this->charMapServerName,
						$sql
					);
				}
				elseif (!empty($errmsg) && strcmp($errmsg,'00000') != 0) {
					// Only quit with an error if there is actually an error.
					throw new Flux_Error("Critical MySQL error in Installer/Updater: $errmsg");
				}
			}
			
			$this->schemaInfo['versions'][$version] = true;
			$this->determineInstalledVersions();
			
			// Create file indicating schema is installed.
			$file = "{$this->schemaInfo['name']}.$version.txt";
			fclose(fopen("{$this->installedSchemaDir}/{$file}", 'w'));
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function update()
	{
		if (!$this->isLatest()) {
			foreach ($this->schemaInfo['versions'] as $version => $installed) {
				if (!$installed) {
					$this->install($version);
				}
			}
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function versionInstalled($version)
	{
		$installed = array_key_exists($version, $this->schemaInfo['versions']) && $this->schemaInfo['versions'][$version];
		return $installed;
	}
	
	/**
	 *
	 */
	public function isLatest()
	{
		return $this->latestVersionInstalled;
	}
	
	/**
	 *
	 */
	public static function getSchemas(Flux_Installer_MainServer $mainServer, Flux_Installer_CharMapServer $charMapServer = null)
	{
		$mainServerName = $mainServer->loginAthenaGroup->serverName;
		
		if (is_null($charMapServer)) {
			$charMapServerName  = null;
			$databaseName       = $mainServer->loginAthenaGroup->loginDatabase;
			$connection         = $mainServer->loginAthenaGroup->connection;
			$availableSchemaDir = array(FLUX_DATA_DIR."/schemas/logindb");
			$installedSchemaDir = FLUX_DATA_DIR."/logs/schemas/logindb/$mainServerName";
			
			foreach (Flux::$addons as $addon) {
				$_schemaDir = "{$addon->addonDir}/schemas/logindb";
				if (file_exists($_schemaDir) && is_dir($_schemaDir)) {
					$availableSchemaDir[] = $_schemaDir;
				}
			}
		}
		else {
			$charMapServerName  = $charMapServer->athena->serverName;
			$databaseName       = $charMapServer->athena->charMapDatabase;
			$connection         = $charMapServer->athena->connection;
			$availableSchemaDir = array(FLUX_DATA_DIR."/schemas/charmapdb");
			$installedSchemaDir = FLUX_DATA_DIR."/logs/schemas/charmapdb/$mainServerName/{$charMapServer->athena->serverName}";
			
			foreach (Flux::$addons as $addon) {
				$_schemaDir = "{$addon->addonDir}/schemas/charmapdb";
				if (file_exists($_schemaDir) && is_dir($_schemaDir)) {
					$availableSchemaDir[] = $_schemaDir;
				}
			}
		}
		
		$dataArray = array(
			'mainServerName'     => $mainServerName,
			'charMapServerName'  => $charMapServerName,
			'databaseName'       => $databaseName,
			'connection'         => $connection,
			//'availableSchemaDir' => $availableSchemaDir,
			'installedSchemaDir' => $installedSchemaDir,
		);
		
		$availableSchemas = array();
		$installedSchemas = array();
		
		$directories = array();
		foreach ($availableSchemaDir as $dir) {
			$directories[] = array($dir, 'availableSchemas', 'sql');
		}
		$directories[] = array($installedSchemaDir, 'installedSchemas', 'txt');
		
		foreach ($directories as $directory) {
			list ($schemaDir, $schemaArray, $fileExt) = $directory;
			$schemas = &$$schemaArray;
			$sFiles  = glob("$schemaDir/*.$fileExt");
			
			if (!$sFiles) {
				$sFiles = array();
			}
			
			foreach ($sFiles as $schemaFilePath) {
				$schemaName = basename($schemaFilePath, ".$fileExt");
				if (preg_match('/^(\w+)\.(\d+)$/', $schemaName, $m)) {
					$schemaName    = $m[1];
					$schemaVersion = $m[2];
					
					// Dynamically set schema directory.
					$dataArray['availableSchemaDir'] = $directory;
					
					if (!array_key_exists($schemaName, $schemas)) {
						$schemas[$schemaName] = array(
							'name'     => $schemaName,
							'versions' => array(),
						);
					}
					
					$schemas[$schemaName]['versions'][$schemaFilePath] = $schemaVersion;
				}
			}
		}
		
		$objects = array();
		foreach ($availableSchemas as $schemaName => $schema) {
			$schemaInfo = array(
				'name'     => $schemaName,
				'versions' => array_flip($schema['versions']),
				'files'    => array_flip($schema['versions'])
			);
			
			foreach ($schemaInfo['versions'] as $key => $value) {
				$schemaInfo['versions'][$key] = false;
			}
			
			if (array_key_exists($schemaName, $installedSchemas)) {
				foreach ($installedSchemas[$schemaName]['versions'] as $key => $value) {
					$schemaInfo['versions'][$value] = true;
				}
			}
			
			$dataArray['schemaInfo'] = $schemaInfo;
			$objects[] = new Flux_Installer_Schema($dataArray);
		}
		
		return $objects;
	}
}
?>
