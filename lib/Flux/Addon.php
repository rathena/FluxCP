<?php
require_once 'Flux/Config.php';

class Flux_Addon {
	public $name;
	public $addonDir;
	public $configDir;
	public $moduleDir;
	public $themeDir;
	public $addonConfig;
	public $accessConfig;
	public $messagesConfig;
	
	public function __construct($name, $addonDir = null)
	{
		$this->name       = $name;
		$this->addonDir   = is_null($addonDir) ? FLUX_ADDON_DIR."/$name" : $addonDir;
		$this->configDir  = "{$this->addonDir}/config";
		$this->moduleDir  = "{$this->addonDir}/modules";
		$this->themeDir   = "{$this->addonDir}/themes";
		
		$files = array(
			'addonConfig'    => "{$this->configDir}/addon.php",
			'accessConfig'   => "{$this->configDir}/access.php",
			//'messagesConfig' => "{$this->configDir}/messages.php" // Deprecated.
		);
		
		foreach ($files as $configName => $filename) {
			if (file_exists($filename)) {
				$this->{$configName} = Flux::parseConfigFile($filename);
			}
			
			if (!($this->{$configName} instanceOf Flux_Config)) {
				$tempArr = array();
				$this->{$configName} = new Flux_Config($tempArr);
			}
		}
		
		// Use new language system for messages (also supports addons).
		$this->messagesConfig = Flux::parseLanguageConfigFile($name);
	}
	
	public function respondsTo($module, $action = null)
	{
		$path = is_null($action) ? "{$this->moduleDir}/$module" : "{$this->moduleDir}/$module/$action.php";
		if ((is_null($action) && is_dir($path)) || file_exists($path)) {
			return true;
		}
		else {
			return false;
		}
	}

	public function getView(Flux_Template $template, $module, $action)
	{
		$path = "{$this->themeDir}/". $template->getName() . "/{$module}/{$action}.php";

		if (file_exists($path)) {
			return $path;
		}

		if (!empty($template->parentTemplate)) {
			return $this->getView( $template->parentTemplate, $module, $action);
		}

		return false;
	}

}
?>
