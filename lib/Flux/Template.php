<?php
require_once 'Flux/Paginator.php';

/**
 * The template is mostly responsible for the presentation logic of things, but
 * currently it also carries the task of executing the action files, which are
 * responsible for the business logic of the application. Maybe this will
 * change in the future, but I'm not sure yet. As long as the developers are
 * forced to adhere to the separation of business logic and presentation logic
 * then I don't think I'll be motivated enough to change this part.
 *
 * Views are rendered within the scope of the template instance, thus $this can
 * be used to access the template instance's methods, and is also how helpers
 * are currently implemented.
 */
class Flux_Template {
	/**
	 * Default data which gets exposed as globals to the templates, and may be
	 * set with the setDefaultData() method.
	 *
	 * @access private
	 * @var array
	 */
	private $defaultData = array();
	
	/**
	 * Request parameters.
	 *
	 * @access protected
	 * @var Flux_Config
	 */
	protected $params;
	
	/**
	 * Base URI of the entire application.
	 *
	 * @access protected
	 * @var string
	 */
	protected $basePath;
	
	/**
	 * Module path.
	 *
	 * @access protected
	 * @var string
	 */
	protected $modulePath;
	
	/**
	 * Module name.
	 *
	 * @access protected
	 * @var string
	 */
	protected $moduleName;
	
	/**
	 * Theme path.
	 *
	 * @access protected
	 * @var string
	 */
	protected $themePath;
	
	/**
	 * Theme name.
	 *
	 * @access protected
	 * @var string
	 */
	protected $themeName;
	
	/**
	 * Action name. Actions exist as modulePath/moduleName/actionName.php.
	 *
	 * @access protected
	 * @var string
	 */
	protected $actionName;
	
	/**
	 * Action path, would be the path format documented in $actionName.
	 *
	 * @access protected
	 * @var string
	 */
	protected $actionPath;
	
	/**
	 * View name, this is usually the same as the actionName.
	 *
	 * @access protected
	 * @var string
	 */
	protected $viewName;
	
	/**
	 * View path, follows a similar (or rather, exact) format like actionPath,
	 * except there would be a themePath and viewName instead.
	 *
	 * @access protected
	 * @var string
	 */
	protected $viewPath;
	
	/**
	 * Header name. The header file would exist under the themePath's top level
	 * and the headerName would simply be the file's basename without the .php
	 * extension. This name is usually 'header'.
	 *
	 * @access protected
	 * @var string
	 */	
	protected $headerName;
	
	/**
	 * The actual path to the header file.
	 *
	 * @access protected
	 * @var string
	 */
	protected $headerPath;
	
	/**
	 * The footer name.
	 * Similar to headerName. This name is usually 'footer'.
	 *
	 * @access protected
	 * @var string
	 */
	protected $footerName;
	
	/**
	 * The actual path to the footer file.
	 *
	 * @access protected
	 * @var string
	 */
	protected $footerPath;
	
	/**
	 * Whether or not to use mod_rewrite-powered clean URLs or just plain old
	 * query strings.
	 *
	 * @access protected
	 * @var string
	 */
	protected $useCleanUrls;
	
	/**
	 * URL of the current module/action being viewed.
	 *
	 * @access protected
	 * @var string
	 */
	protected $url;
	
	/**
	 * URL of the current module/action being viewed. (including query string)
	 *
	 * @access protected
	 * @var string
	 */
	protected $urlWithQs;
	protected $urlWithQS; // compatibility.
	
	/**
	 * Module/action for missing action's event.
	 *
	 * @access protected
	 * @var array
	 */
	protected $missingActionModuleAction;
	
	/**
	 * Module/action for missing view's event.
	 *
	 * @access protected
	 * @var array
	 */
	protected $missingViewModuleAction;
	
	/**
	 * Inherit view / controllers from another theme ?
	 *
	 * @access public
	 * @var Flux_Template
	 */
	public $parentTemplate;
	
	/**
	 * List of themes loaded, use for avoid circular dependencies
	 *
	 * @access public
	 * @var array
	 */
	static public $themeLoaded = array();
	
	/**
	 * HTTP referer.
	 *
	 * @access public
	 * @var string
	 */
	public $referer;
	
	/**
	 * Construct new template onbject.
	 *
	 * @param Flux_Config $config
	 * @access public
	 */
	public function __construct(Flux_Config $config)
	{
		$this->params                    = $config->get('params');
		$this->basePath                  = $config->get('basePath');
		$this->modulePath                = $config->get('modulePath');
		$this->moduleName                = $config->get('moduleName');
		$this->themePath                 = $config->get('themePath');
		$this->themeName                 = $config->get('themeName');
		$this->actionName                = $config->get('actionName');
		$this->viewName                  = $config->get('viewName');
		$this->headerName                = $config->get('headerName');
		$this->footerName                = $config->get('footerName');
		$this->useCleanUrls              = $config->get('useCleanUrls');
		$this->missingActionModuleAction = $config->get('missingActionModuleAction', false);
		$this->missingViewModuleAction   = $config->get('missingViewModuleAction', false);
		$this->referer                   = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

		// Read manifest file if exists
		if (file_exists($this->themePath.'/'.$this->themeName.'/manifest.php')) {
			$manifest = include($this->themePath.'/'.$this->themeName.'/manifest.php');

			// Inherit views and controllers from another template
			if (!empty($manifest['inherit'])) {

				if (in_array($manifest['inherit'], self::$themeLoaded)) {
					throw new Flux_Error('Circular dependencies in themes : ' . implode(' -> ', self::$themeLoaded) . ' -> ' .  $manifest['inherit']);
				}

				$config->set('themeName', $manifest['inherit']);
				self::$themeLoaded[]    = $manifest['inherit'];
				$this->parentTemplate   = new Flux_Template($config);
			}
		}

	}

	/**
	 * Any data that gets set here will be available to all templates as global
	 * variables unless they are overridden by variables of the same name set
	 * in the render() method.
	 *
	 * @return array
	 * @access public
	 */
	public function setDefaultData(array &$data)
	{
		$this->defaultData = $data;
		return $data;
	}

	/**
	 * Render a template, but before doing so, call the action file and render
	 * the header->view->footer in that order.
	 *
	 * @param array $dataArr Key=>value pairs of variables to be exposed to the template as globals.
	 * @access public
	 */
	public function render(array $dataArr = array())
	{
		// GZip compression.
		if (Flux::config('GzipCompressOutput')) {
			header('Accept-Encoding: gzip');
			ini_set('zlib.output_handler', '');
			ini_set('zlib.output_compression', 'On');
			ini_set('zlib.output_compression_level', (int)Flux::config('GzipCompressionLevel'));
		}
		
		$addon = false;
		$this->actionPath = sprintf('%s/%s/%s.php', $this->modulePath, $this->moduleName, $this->actionName);
		
		if (!file_exists($this->actionPath)) {
			foreach (Flux::$addons as $_tmpAddon) {
				if ($_tmpAddon->respondsTo($this->moduleName, $this->actionName)) {
					$addon = $_tmpAddon;
					$this->actionPath = sprintf('%s/%s/%s.php', $addon->moduleDir, $this->moduleName, $this->actionName);
				}
			}
			
			if (!$addon) {
				$this->moduleName = $this->missingActionModuleAction[0];
				$this->actionName = $this->missingActionModuleAction[1];
				$this->viewName   = $this->missingActionModuleAction[1];
				$this->actionPath = sprintf('%s/%s/%s.php', $this->modulePath, $this->moduleName, $this->actionName);
			}
		}
		
		$this->viewPath = $this->themePath(sprintf('%s/%s.php', $this->moduleName, $this->actionName), true);
		
		if (!file_exists($this->viewPath) && $addon) {
			$this->viewPath = $addon->getView( $this, $this->moduleName, $this->actionName);
			
			if ( $this->viewPath === false ) {
				$this->moduleName = $this->missingViewModuleAction[0];
				$this->actionName = $this->missingViewModuleAction[1];
				$this->viewName   = $this->missingViewModuleAction[1];
				$this->actionPath = sprintf('%s/%s/%s.php', $this->modulePath, $this->moduleName, $this->actionName);
				$this->viewPath   = $this->themePath(sprintf('%s/%s.php', $this->moduleName, $this->viewName), true);
			}
		}

		$this->headerPath = $this->themePath($this->headerName.'.php', true);
		$this->footerPath = $this->themePath($this->footerName.'.php', true);
		$this->url        = $this->url($this->moduleName, $this->actionName);
		$this->urlWithQS  = $this->url;
		
		if (!empty($_SERVER['QUERY_STRING'])) {
			if ($this->useCleanUrls) {
				$this->urlWithQS .= "?{$_SERVER['QUERY_STRING']}";
			}
			else {
				foreach (explode('&', trim($_SERVER['QUERY_STRING'], '&')) as $line) {
					list ($key,$val) = explode('=', $line, 2);
					$key = urldecode($key);
					$val = urldecode($val);
					
					if ($key != 'module' && $key != 'action') {
						$this->urlWithQS .= sprintf('&%s=%s', urlencode($key), urlencode($val));
					}
				}
			}
		}
		
		// Compatibility.
		$this->urlWithQs  = $this->urlWithQS;
		
		// Tidy up!
		if (Flux::config('OutputCleanHTML')) {
			$dispatcher = Flux_Dispatcher::getInstance();
			$tidyIgnore = false;
			if (($tidyIgnores = Flux::config('TidyIgnore')) instanceOf Flux_Config) {
				foreach ($tidyIgnores->getChildrenConfigs() as $ignore) {
					$ignore = $ignore->toArray();
					if (is_array($ignore) && array_key_exists('module', $ignore)) {
						$module = $ignore['module'];
						$action = array_key_exists('action', $ignore) ? $ignore['action'] : $dispatcher->defaultAction;
						if ($this->moduleName == $module && $this->actionName == $action) {
							$tidyIgnore = true;
						}
					}
				}
			}
			if (!$tidyIgnore) {
				ob_start();
			}
		}
		
		// Merge with default data.
		$data = array_merge($this->defaultData, $dataArr);
		
		// Extract data array and make them appear as though they were global
		// variables from the template.
		extract($data, EXTR_REFS);
		
		// Files object.
		$files = new Flux_Config($_FILES);
		
		$preprocessorPath = sprintf('%s/main/preprocess.php', $this->modulePath);
		if (file_exists($preprocessorPath)) {
			include $preprocessorPath;
		}
		
		include $this->actionPath;
		
		$pageMenuFile   = FLUX_ROOT."/modules/{$this->moduleName}/pagemenu/{$this->actionName}.php";
		$pageMenuItems  = array();
		
		// Get the main menu file first (located in the actual module).
		if (file_exists($pageMenuFile)) {
			ob_start();
			$pageMenuItems = include $pageMenuFile;
			ob_end_clean();
		}
		
		$addonPageMenuFiles = glob(FLUX_ADDON_DIR."/*/modules/{$this->moduleName}/pagemenu/{$this->actionName}.php");
		if ($addonPageMenuFiles) {
			foreach ($addonPageMenuFiles as $addonPageMenuFile) {
				ob_start();
				$pageMenuItems = array_merge($pageMenuItems, include $addonPageMenuFile);
				ob_end_clean();
			}
		}
		
		if (file_exists($this->headerPath)) {
			include $this->headerPath;
		}
	
		include $this->viewPath;
	
		if (file_exists($this->footerPath)) {
			include $this->footerPath;
		}
		
		// Really, tidy up!
		if (Flux::config('OutputCleanHTML') && !$tidyIgnore && function_exists('tidy_repair_string')) {
			$content = ob_get_clean();
			$content = tidy_repair_string($content, array('indent' => true, 'wrap' => false, 'output-xhtml' => true), 'utf8');
			echo $content;
		}
	}
	
	/**
	 * Returns an array of menu items that should be diplayed from the theme.
	 * Only menu items the current user (and their group level) have access to
	 * will be returned as part of the array;
	 *
	 * @return array
	 */
	public function getMenuItems($adminMenus = false)
	{
		$auth              = Flux_Authorization::getInstance();
		$adminMenuLevel    = Flux::config('AdminMenuGroupLevel');
		$defaultAction     = Flux_Dispatcher::getInstance()->defaultAction;
		$menuItems         = Flux::config('MenuItems');
		$allowedItems      = array();
		
		if (!($menuItems instanceOf Flux_Config)) {
			return array();
		}
		
		foreach ($menuItems->toArray() as $categoryName => $menu) {
			foreach ($menu as $menuName => $menuItem) {
				$module = array_key_exists('module', $menuItem) ? $menuItem['module'] : false;
				$action = array_key_exists('action', $menuItem) ? $menuItem['action'] : $defaultAction;
				$exturl = array_key_exists('exturl', $menuItem) ? $menuItem['exturl'] : null;

				if ($adminMenus) {
					if ($auth->actionAllowed($module, $action) && $auth->config("modules.$module.$action") >= $adminMenuLevel) {
						$allowedItems[] = array(
							'name'   => $menuName,
							'exturl' => null,
							'module' => $module,
							'action' => $action,
							'url'    => $this->url($module, $action)
						);
					}
				}
				else {
					if (empty($allowedItems[$categoryName])) {
						$allowedItems[$categoryName] = array();
					}
					
					if ($exturl) {
						$allowedItems[$categoryName][] = array(
							'name'   => $menuName,
							'exturl' => $exturl,
							'module' => null,
							'action' => null,
							'url'    => $exturl
						);
					}
					elseif ($auth->actionAllowed($module, $action) && $auth->config("modules.$module.$action") < $adminMenuLevel) {
						$allowedItems[$categoryName][] = array(
							'name'   => $menuName,
							'exturl' => null,
							'module' => $module,
							'action' => $action,
							'url'    => $this->url($module, $action)
						);
					}
				}
			}
		}
		
		return $allowedItems;
	}
	
	/**
	 * @see Flux_Template::getMenuItems()
	 */
	public function getAdminMenuItems()
	{
		return $this->getMenuItems(true);
	}
	
	/**
	 * Get sub-menu items for a particular module.
	 *
	 * @param string $moduleName
	 * @return array
	 */
	public function getSubMenuItems($moduleName = null)
	{
		$auth         = Flux_Authorization::getInstance();
		$moduleName   = $moduleName ? $moduleName : $this->moduleName;
		$subMenuItems = Flux::config('SubMenuItems');
		$allowedItems = array();
		
		if (!($subMenuItems instanceOf Flux_Config) || !( ($menus = $subMenuItems->get($moduleName)) instanceOf Flux_Config )) {
			return array();
		}
		
		foreach ($menus->toArray() as $actionName => $menuName) {
			if ($auth->actionAllowed($moduleName, $actionName)) {
				$allowedItems[] = array('name' => $menuName, 'module' => $moduleName, 'action' => $actionName);
			}
		}
		
		return $allowedItems;
	}
	
	/**
	 * Get an array of login server names.
	 *
	 * @return array
	 */
	public function getServerNames()
	{
		return array_keys(Flux::$loginAthenaGroupRegistry);
	}
	
	/**
	 * Determine if more than 1 server exists.
	 *
	 * @return bool
	 */
	public function hasManyServers()
	{
		return count(Flux::$loginAthenaGroupRegistry) > 1;
	}
	
	/**
	 * Obtain the absolute web path of the specified user path. Specify the
	 * path as a relative path.
	 *
	 * @param string $path Relative path from basePath.
	 * @param boolean $included
	 * @access public
	 */
	public function path($path, $included = false)
	{
		if (is_array($path)) {
			$path = implode('/', $path);
		}

		if ($included === false) {
			$path = "{$this->basePath}/$path";
		}

		return preg_replace('&/{2,}&', '/', $path);
	}

	/**
	 * Similar to the path() method, but uses the $themePath as the path from
	 * which the user-specified path is relative.
	 *
	 * @param string $path Relative path from themePath.
	 * @access public
	 */
	public function themePath($path, $included = false)
	{
		if (is_array($path)) {
			$path = implode('/', $path);
		}

		// Remove frag for file checking.
		$frag = "";
		preg_match("/(\?|\#).*/", $path, $matches);
		if (count($matches)) {
			$frag = $matches[0];
			$path = substr($path, 0, -strlen($frag));
		}

		$uri  = $this->path("{$this->themePath}/{$this->themeName}/{$path}", $included);

		// normalized basePath.
		$base = preg_replace('/(\/+)$/', '', $this->basePath ) . '/'; 
		$base = preg_quote( $base, '/' );
		$chk  = FLUX_ROOT .'/'. preg_replace('/^('.$base.')/', '', $uri );

		// If file not found, search in parent's template.
		if (!file_exists($chk) && !empty($this->parentTemplate)) {
			$path = $this->parentTemplate->themePath($path, $included);
			$chk  = FLUX_ROOT .'/'. preg_replace('/^('.$base.')/', '', $path );

			if (file_exists($chk)) {
				$uri = $path;
			}
		}

		return $uri . $frag;
	}
	
	/**
	 * Create a URI based on the setting of $useCleanUrls. This will determine
	 * whether or not we will create a mod_rewrite-based clean URL or just a
	 * regular query string based one.
	 *
	 * @param string $moduleName
	 * @param string $actionName
	 * @access public
	 */
	public function url($moduleName, $actionName = null, $params = array())
	{
		$defaultAction  = Flux_Dispatcher::getInstance()->defaultAction;
		$serverProtocol = '';
		$serverAddress  = '';
		
		if ($params instanceOf Flux_Config) {
			$params = $params->toArray();
		}
		
		if (array_key_exists('_host', $params)) {
			$_host  = $params['_host'];
			$_https = false;
			
			if ($_host && ($addr=Flux::config('ServerAddress'))) {
				if (array_key_exists('_https', $params)) {
					$_https = $params['_https'];
				}
				elseif (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off") {
					$_https = true;
				}
				else {
					$_https = false;
				}

				$serverProtocol = $_https ? 'https://' : 'http://';
				$serverAddress  = $addr;
			}
			
			unset($params['_host']);
			
			if (array_key_exists('_https', $params)) {
				unset($params['_https']);
			}
		}
		
		$queryString = '';
		
		if (count($params)) {
			$queryString .= Flux::config('UseCleanUrls') ? '?' : '&';
			foreach ($params as $param => $value) {
				$queryString .= sprintf('%s=%s&', $param, urlencode($value));
			}
			$queryString = rtrim($queryString, '&');
		}
		
		if ($this->useCleanUrls) {
			if ($actionName && $actionName != $defaultAction) {
				$url = sprintf('%s/%s/%s/%s', $this->basePath, $moduleName, $actionName, $queryString);
			}
			else {
				$url = sprintf('%s/%s/%s', $this->basePath, $moduleName, $queryString);
			}
		}
		else {
			if ($actionName && $actionName != $defaultAction) {
				$url = sprintf('%s/?module=%s&action=%s%s', $this->basePath, $moduleName, $actionName, $queryString);
			}
			else {
				$url = sprintf('%s/?module=%s%s', $this->basePath, $moduleName, $queryString);
			}
		}
		return $serverProtocol.preg_replace('&/{2,}&', '/', "$serverAddress/$url");
	}
	
	/**
	 * Format currency strings.
	 *
	 * @param float $number Amount
	 * @return string Formatted amount
	 */
	public function formatCurrency($number)
	{
		$number = (float)$number;
		$amount = number_format(
			$number,
			Flux::config('MoneyDecimalPlaces'),
			Flux::config('MoneyDecimalSymbol'),
			Flux::config('MoneyThousandsSymbol')
		);
		return $amount;
	}
	
	/**
	 * Format a MySQL DATE column according to the DateFormat config.
	 *
	 * @param string $data
	 * @return string
	 * @access public
	 */
	public function formatDate($date = null)
	{
		$ts = $date ? strtotime($date) : time();
		return date(Flux::config('DateFormat'), $ts);
	}
	
	/**
	 * Format a MySQL DATETIME column according to the DateTimeFormat config.
	 *
	 * @param string $dataTime
	 * @return string
	 * @access public
	 */
	public function formatDateTime($dateTime = null)
	{
		$ts = $dateTime ? strtotime($dateTime) : time();
		return date(Flux::config('DateTimeFormat'), $ts);
	}
	
	/**
	 * Create a series of select fields matching a MySQL DATE format.
	 *
	 * @param string $name
	 * @param string $value DATE formatted string.
	 * @param int $fowardYears
	 * @param int $backwardYears
	 * @return string
	 */
	public function dateField($name, $value = null, $fowardYears = null, $backwardYears = null)
	{
		if(!isset($fowardYears)) {
			$fowardYears = (int)Flux::config('ForwardYears');
		}
		if(!isset($backwardYears)) {
			$backwardYears = (int)Flux::config('BackwardYears');
		}
		
		$ts    = $value && !preg_match('/^0000-00-00(?: 00:00:00)?$/', $value) ? strtotime($value) : time();
		$year  = ($year =$this->params->get("{$name}_year"))  ? $year  : date('Y', $ts);
		$month = ($month=$this->params->get("{$name}_month")) ? $month : date('m', $ts);
		$day   = ($day  =$this->params->get("{$name}_day"))   ? $day   : date('d', $ts);
		$fw    = $year + $fowardYears;
		$bw    = $year - $backwardYears;
		
		// Get years.
		$years = sprintf('<select name="%s_year">', $name);
		for ($i = $fw; $i >= $bw; --$i) {
			if ($year == $i) {
				$years .= sprintf('<option value="%04d" selected="selected">%04d</option>', $i, $i);
			}
			else {
				$years .= sprintf('<option value="%04d">%04d</option>', $i, $i);
			}
		}
		$years .= '</select>';
		
		// Get months.
		$months = sprintf('<select name="%s_month">', $name);
		for ($i = 1; $i <= 12; ++$i) {
			if ($month == $i) {
				$months .= sprintf('<option value="%02d" selected="selected">%02d</option>', $i, $i);
			}
			else {
				$months .= sprintf('<option value="%02d">%02d</option>', $i, $i);
			}
		}
		$months .= '</select>';
		
		// Get days.
		$days = sprintf('<select name="%s_day">', $name);
		for ($i = 1; $i <= 31; ++$i) {
			if ($day == $i) {
				$days .= sprintf('<option value="%02d" selected="selected">%02d</option>', $i, $i);
			}
			else {
				$days .= sprintf('<option value="%02d">%02d</option>', $i, $i);
			}
		}
		$days .= '</select>';
		
		return sprintf('<span class="date-field">%s-%s-%s</span>', $years, $months, $days);
	}
	
	/**
	 * Create a series of select fields matching a MySQL DATETIME format.
	 *
	 * @param string $name
	 * @param string $value DATETIME formatted string.
	 * @return string
	 */
	public function dateTimeField($name, $value = null)
	{
		$dateField = $this->dateField($name, $value);
		$ts        = $value ? strtotime($value) : strtotime('00:00:00');
		$hour      = date('H', $ts);
		$minute    = date('i', $ts);
		$second    = date('s', $ts);
		
		// Get hours.
		$hours = sprintf('<select name="%s_hour">', $name);
		for ($i = 0; $i <= 23; ++$i) {
			if ($hour == $i) {
				$hours .= sprintf('<option value="%02d" selected="selected">%02d</option>', $i, $i);
			}
			else {
				$hours .= sprintf('<option value="%02d">%02d</option>', $i, $i);
			}
		}
		$hours .= '</select>';
		
		// Get minutes.
		$minutes = sprintf('<select name="%s_minute">', $name);
		for ($i = 0; $i <= 59; ++$i) {
			if ($minute == $i) {
				$minutes .= sprintf('<option value="%02d" selected="selected">%02d</option>', $i, $i);
			}
			else {
				$minutes .= sprintf('<option value="%02d">%02d</option>', $i, $i);
			}
		}
		$minutes .= '</select>';
		
		// Get seconds.
		$seconds = sprintf('<select name="%s_second">', $name);
		for ($i = 0; $i <= 59; ++$i) {
			if ($second == $i) {
				$seconds .= sprintf('<option value="%02d" selected="selected">%02d</option>', $i, $i);
			}
			else {
				$seconds .= sprintf('<option value="%02d">%02d</option>', $i, $i);
			}
		}
		$seconds .= '</select>';
		
		return sprintf('<span class="date-time-field">%s @ %s:%s:%s</span>', $dateField, $hours, $minutes, $seconds);
	}
	
	/**
	 * Returns "up" or "down" in a span HTML element with either the class
	 * .up or .down, based on the value of $bool. True returns up, false
	 * returns down.
	 *
	 * @param bool $bool True/false value
	 * @return string Up/down
	 */
	public function serverUpDown($bool)
	{
		$class = $bool ? 'up' : 'down';
		return sprintf('<span class="%s">%s</span>', $class, $bool ? 'Online' : 'Offline');
	}
	
	/**
	 * Redirect client to another location. Script execution is terminated
	 * after instructing the client to redirect.
	 *
	 * @param string $location
	 */
	public function redirect($location = null)
	{
		if (is_null($location)) {
			$location = $this->basePath;
		}
		
		header("Location: $location");
		exit;
	}
	
	/**
	 * Guess the HTTP server's current full URL.
	 *
	 * @param bool $withRequest True to include REQUEST_URI, false if not.
	 * @return string URL
	 */
	public function entireUrl($withRequest = true)
	{
		$proto    = empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off" ? 'http://' : 'https://';
		$hostname = empty($_SERVER['HTTP_HOST']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
		$request  = $_SERVER['REQUEST_URI'];
		
		if ($withRequest) {
			$url = $proto.$hostname.$request;
		}
		else {
			$url = $proto.$hostname.'/'.$this->basePath;
		}
		
		$url = rtrim(preg_replace('&/{2,}&', '/', $url), '/');
		return $url;
	}
	
	/**
	 * Convenience method for retrieving a paginator instance.
	 *
	 * @param int $total Total number of records.
	 * @param array $options Paginator options.
	 * @return Flux_Paginator
	 * @access public
	 */
	public function getPaginator($total, array $options = array())
	{
		$paginator = new Flux_Paginator($total, $this->url($this->moduleName, $this->actionName, array('_host' => false)), $options);
		return $paginator;
	}
	
	/**
	 * Link to an account view page.
	 *
	 * @param int $accountID
	 * @param string $text
	 * @return mixed
	 * @access public
	 */
	public function linkToAccount($accountID, $text)
	{
		if ($accountID) {
			$url = $this->url('account', 'view', array('id' => $accountID));
			return sprintf('<a href="%s" class="link-to-account">%s</a>', $url, htmlspecialchars($text));
		}
		else {
			return false;
		}
	}
	
	/**
	 * Link to an account search.
	 *
	 * @param array $params
	 * @param string $text
	 * @return mixed
	 * @access public
	 */
	public function linkToAccountSearch($params, $text)
	{
		if (is_array($params) && count($params)) {
			$url = $this->url('account', 'index', $params);
			return sprintf('<a href="%s" class="link-to-account-search">%s</a>', $url, htmlspecialchars($text));
		}
		else {
			return false;
		}
	}
	
	/**
	 * Link to a character view page.
	 *
	 * @param int $charID
	 * @param string $text
	 * @return mixed
	 * @access public
	 */
	public function linkToCharacter($charID, $text, $server = null)
	{
		if ($charID) {
			$params = array('id' => $charID);
			if ($server) {
				$params['preferred_server'] = $server;
			}
			
			$url = $this->url('character', 'view', $params);
			return sprintf('<a href="%s" class="link-to-character">%s</a>', $url, htmlspecialchars($text));
		}
		else {
			return false;
		}
	}
	
	/**
	 * Deny entry to a page if called. This method should be used from a module
	 * script, and no where else.
	 */
	public function deny()
	{
		$location = $this->url('unauthorized');
		$this->redirect($location);
	}
	
	/**
	 * Get the full gender string from a gender letter (e.g. M for Male).
	 *
	 * @param string $gender
	 * @return string
	 * @access public
	 */
	public function genderText($gender)
	{
		switch (strtoupper($gender)) {
			case 'M':
				return Flux::message('GenderTypeMale');
				break;
			case 'F':
				return Flux::message('GenderTypeFemale');
				break;
			case 'S':
				return Flux::message('GenderTypeServer');
				break;
			default:
				return false;
				break;
		}
	}
	
	/**
	 * Get the account state name corresponding to the state number.
	 *
	 * @param int $state
	 * @return mixed State name or false.
	 * @access public
	 */
	public function accountStateText($state)
	{
		$text  = false;
		$state = (int)$state;
		
		switch ($state) {
			case 0:
				$text  = Flux::message('AccountStateNormal');
				$class = 'state-normal';
				break;
			case 5:
				$text  = Flux::message('AccountStatePermBanned');
				$class = 'state-permanently-banned';
				break;
		}
		
		if ($text) {
			$text = htmlspecialchars($text);
			return sprintf('<span class="account-state %s">%s<span>', $class, $text);
		}
		else {
			return false;
		}
	}
	
	/**
	 * Get the job class name from a job ID.
	 *
	 * @param int $id
	 * @return mixed Job class or false.
	 * @access public
	 */
	public function jobClassText($id)
	{
		return Flux::getJobClass($id);
	}
	
	/**
	 * Return hidden input fields containing module and action names based on
	 * the setting of UseCleanUrls.
	 *
	 * @param string $moduleName
	 * @param string $actionName
	 * @return string
	 * @access public
	 */
	public function moduleActionFormInputs($moduleName, $actionName = null)
	{	
		$inputs = '';
		if (!Flux::config('UseCleanUrls')) {
			if (!$actionName) {
				$dispatcher = Flux_Dispatcher::getInstance();
				$actionName = $dispatcher->defaultAction;
			}
			$inputs .= sprintf('<input type="hidden" name="module" value="%s" />', htmlspecialchars($moduleName))."\n";
			$inputs .= sprintf('<input type="hidden" name="action" value="%s" />', htmlspecialchars($actionName));
		}
		return $inputs;
	}
	
	/**
	 * Get the homun class name from a class ID.
	 *
	 * @param int $id
	 * @return mixed Job class or false.
	 * @access public
	 */
	public function homunClassText($id)
	{
		return Flux::getHomunClass($id);
	}

	/**
	 * Get the item type name from an item type.
	 *
	 * @return Item type or false.
	 * @access public
	 */
	public function itemTypeText($id)
	{
		return Flux::getItemType($id);
	}
	
	public function itemSubTypeText($id1, $id2)
	{
		if($id1 == 'Weapon' || $id1 == 'Ammo' || $id1 == 'Card')
			return Flux::getItemSubType(strtolower($id1), strtolower($id2));
		else
			return false;
	}
	
	public function itemRandOption($id, $value)
	{
		return sprintf(Flux::getRandOption($id), $value);
	}
	
	/**
	 * Get the item information from splitting a delimiter
	 * Used for renewal ATK and MATK as well as equip_level_min and equip_level_max.
	 *
	 * @param PDOStatement $object
	 * @param string $field
	 * @param string $delimiter
	 * @param array $inputs
	 * @return PDOStatement $object
	 * @access public
	 */
	public function itemFieldExplode($object, $field, $delimiter, $inputs)
	{
		$fields = explode($delimiter, $object->$field);
		foreach($inputs as $i => $input) {
			$object->$input = isset($fields[$i]) ? $fields[$i] : NULL;
		}
		return $object;
	}
	
	/**
	 * Get the equip location combination name from an equip location combination.
	 *
	 * @param int $id
	 * @return mixed Equip location combination or false.
	 * @access public
	 */
	public function equipLocationCombinationText($id)
	{
		return Flux::getEquipLocationCombination($id);
	}
	
	/**
	 *
	 *
	 */
	public function emblem($guildID, $serverName = null, $athenaServerName = null)
	{
		if (!$serverName) {
			$serverName = Flux::$sessionData->loginAthenaGroup->serverName;
		}
		
		if (!$athenaServerName) {
			$athenaServerName = Flux::$sessionData->getAthenaServer(Flux::$sessionData->athenaServerName);
		}
		
		return $this->url('guild', 'emblem',
			array('login' => $serverName, 'charmap' => $athenaServerName, 'id' => $guildID));
	}
	
	/**
	 * Redirect to login page if the user is not currently logged in.
	 */
	public function loginRequired($message = null)
	{
		$dispatcher = Flux_Dispatcher::getInstance();
		$dispatcher->loginRequired($this->basePath, $message);
	}
	
	/**
	 * Link to a item view page.
	 *
	 * @param int $itemID
	 * @param string $text
	 * @return mixed
	 * @access public
	 */
	public function linkToItem($itemID, $text, $server = null)
	{
		if ($itemID) {
			$params = array('id' => $itemID);
			if ($server) {
				$params['preferred_server'] = $server;
			}
			
			$url = $this->url('item', 'view', $params);
			return sprintf('<a href="%s" class="link-to-item">%s</a>', $url, htmlspecialchars($text));
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function displayScript($scriptText)
	{
		$lines  = preg_split('/(\r?\n)/', $scriptText, -1);
		$text   = '';
		$script = array();
		
		foreach ($lines as $num => $line) {
			$text    .= "$line\n";
			$lineNum  = sprintf('<span class="script-line-num">%d</span>', $num + 1);
			$lineCode = sprintf('<span class="script-line-code">%s</span>', htmlspecialchars($line));
			$script[] = sprintf('<p class="script-line">%s %s</p>', $lineNum, $lineCode);
		}
		
		return trim($text) == '' ? '' : implode("\n", $script);
	}
	
	/**
	 *
	 */
	public function banTypeText($banType)
	{
		$banType = (int)$banType;
		if (!$banType) {
			return Flux::message('BanTypeUnbanned');
		}
		elseif ($banType === 2) {
			return Flux::message('BanTypePermBanned');
		}
		elseif ($banType === 1) {
			return Flux::message('BanTypeTempBanned');
		}
		else {
			return Flux::message('UnknownLabel');
		}
	}
	
	/**
	 *
	 */
	public function equippableJobs($equipJob)
	{
		$jobs      = array();
		$equipJobs = Flux::getEquipJobsList();
		
		foreach ($equipJob as $name) {
				$jobs[] = $equipJobs[$name];
				if($name == 'job_all') break;
		}
		
		return $jobs;
	}
	
	/**
	 *
	 */
	public function GetJobsList($isRenewal)
	{
		$jobs = Flux::getEquipJobsList($isRenewal);
				
		return $jobs;
	}
	
	/**
	 *
	 */
	public function GetClassList($isRenewal)
	{
		$jobs = Flux::getEquipUpperList($isRenewal);
				
		return $jobs;
	}
	
	/**
	 *
	 */
	public function tradeRestrictions($list)
	{
		$restrictions = array();
		$Restrictions = Flux::getTradeRestrictionList();
		
		foreach ($list as $name) {
				$restrictions[] = $Restrictions[$name];
		}
		
		return $restrictions;
	}
	
	/**
	 *
	 */
	public function itemsFlags($list)
	{
		$flags = array();
		$Flags = Flux::getItemFlagList();
		
		foreach ($list as $name) {
				$flags[] = $Flags[$name];
		}
		
		return $flags;
	}

	/**
	 * Link to a monster view page.
	 *
	 * @param int $monsterID
	 * @param string $text
	 * @return mixed
	 * @access public
	 */
	public function linkToMonster($monsterID, $text, $server = null)
	{
		if ($monsterID) {
			$params = array('id' => $monsterID);
			if ($server) {
				$params['preferred_server'] = $server;
			}
			
			$url = $this->url('monster', 'view', $params);
			return sprintf('<a href="%s" class="link-to-monster">%s</a>', $url, htmlspecialchars($text));
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function equipLocations($equipLoc)
	{
		$locations = array();
		asort($equipLoc);
		if(count($equipLoc) > 1) {
			$equipLocs = Flux::getEquipLocationCombination();
			$equipLoc = array(htmlspecialchars(implode('/', $equipLoc)));
		} else {
			$equipLocs = Flux::getEquipLocationList();
		}
		foreach ($equipLoc as $key => $name) {
				$locations[] = $equipLocs[$name];
		}
		if(is_array($equipLoc))
			return htmlspecialchars(implode(' / ', $locations));
		else
			return false;
	}
	
	/**
	 *
	 */
	public function equipUpper($equipUpper, $isRenewal = 1)
	{
		$upper      = array();
		$table      = Flux::getEquipUpperList($isRenewal);
		
		foreach ($equipUpper as $name) {
				$upper[] = $table[$name];
				if($name == 'class_all') break;
		}
		
		return $upper;
	}

	/**
	 * Link to a guild view page.
	 *
	 * @param int $guildID
	 * @param string $text
	 * @return mixed
	 * @access public
	 */
	public function linkToGuild($guild_id, $text, $server = null)
	{
		if ($guild_id) {
			$params = array('id' => $guild_id);
			if ($server) {
				$params['preferred_server'] = $server;
			}
			
			$url = $this->url('guild', 'view', $params);
			return sprintf('<a href="%s" class="link-to-guild">%s</a>', $url, htmlspecialchars($text));
		}
		else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function donateButton($amount)
	{
		ob_start();
		include FLUX_DATA_DIR.'/paypal/button.php';
		$button = ob_get_clean();
		return $button;
	}
	
	/**
	 *
	 */
	public function shopItemImage($shopItemID, $serverName = null, $athenaServerName = null)
	{
		if (!$serverName) {
			$serverName = Flux::$sessionData->loginAthenaGroup->serverName;
		}
		
		if (!$athenaServerName) {
			$athenaServerName = Flux::$sessionData->getAthenaServer(Flux::$sessionData->athenaServerName);
		}
		
		if (!$serverName || !$athenaServerName) {
			return false;
		}
		
		$dir   = FLUX_DATA_DIR."/itemshop/$serverName/$athenaServerName";
		$exts  = implode('|', array_map('preg_quote', Flux::config('ShopImageExtensions')->toArray()));
		$imgs  = glob("$dir/$shopItemID.*");
		
		if (is_array($imgs)) {
			$files = preg_grep("/\.($exts)$/", $imgs);
		}
		else {
			$files = array();
		}
		
		if (empty($files)) {
			return false;
		}
		else {
			reset($files);
			$imageFile = current($files);
			return preg_replace('&/{2,}&', '/', "{$this->basePath}/$imageFile");
		}
	}
	
	/**
	 *
	 */
	public function iconImage($itemID)
	{
		$path = sprintf(FLUX_DATA_DIR."/items/icons/".Flux::config('ItemIconNameFormat'), $itemID);
		$link = preg_replace('&/{2,}&', '/', "{$this->basePath}/$path");
		
		if(Flux::config('DivinePrideIntegration') && !file_exists($path)) {
			$download_link = "https://static.divine-pride.net/images/items/item/$itemID.png";
			$data = get_headers($download_link, true);
			$size = isset($data['Content-Length']) ? (int)$data['Content-Length'] : 0;
			if($size != 0)
				file_put_contents(sprintf(FLUX_DATA_DIR."/items/icons/".Flux::config('ItemIconNameFormat'), $itemID), file_get_contents($download_link));
		}
        return file_exists($path) ? $link : false;
	}
	
	/**
	 *
	 */
	public function itemImage($itemID)
	{
		$path = sprintf(FLUX_DATA_DIR."/items/images/".Flux::config('ItemImageNameFormat'), $itemID);
		$link = preg_replace('&/{2,}&', '/', "{$this->basePath}/$path");
		
		if(Flux::config('DivinePrideIntegration') && !file_exists($path)) {
			$download_link = "https://static.divine-pride.net/images/items/collection/$itemID.png";
			$data = get_headers($download_link, true);
			$size = isset($data['Content-Length']) ? (int)$data['Content-Length'] : 0;
			if($size != 0)
				file_put_contents(sprintf(FLUX_DATA_DIR."/items/images/".Flux::config('ItemImageNameFormat'), $itemID), file_get_contents($download_link));
		}
        return file_exists($path) ? $link : false;
	}

 	/**
 	 *
 	 */
	public function monsterImage($monsterID)
	{
		$path = sprintf(FLUX_DATA_DIR."/monsters/".Flux::config('MonsterImageNameFormat'), $monsterID);
		$link = preg_replace('&/{2,}&', '/', "{$this->basePath}/$path");
		
		if(Flux::config('DivinePrideIntegration') && !file_exists($path)) {
			$download_link = "https://static.divine-pride.net/images/mobs/png/$monsterID.png";
			$data = get_headers($download_link, true);
			$size = isset($data['Content-Length']) ? (int)$data['Content-Length'] : 0;
			if($size != 0)
				file_put_contents(sprintf(FLUX_DATA_DIR."/monsters/".Flux::config('MonsterImageNameFormat'), $monsterID), file_get_contents($download_link));
		}
        return file_exists($path) ? $link : false;
	}
	
	/**
	 *
	 */
	public function jobImage($gender, $jobID)
	{
		$path = sprintf(FLUX_DATA_DIR."/jobs/images/%s/".Flux::config('JobImageNameFormat'), $gender, $jobID);
		$link = preg_replace('&/{2,}&', '/', "{$this->basePath}/$path");
		return file_exists($path) ? $link : false;
	}
	
	/**
	 *
	 */
	public function monsterMode($modes, $ai)
	{
		$monsterModes	= Flux::config('MonsterModes')->toArray();
		$monsterAI		= Flux::config('MonsterAI')->toArray();
		$array = array();
		if($ai)
			foreach ($monsterAI[$ai] as $mode) {
				if(isset($monsterModes[$mode]))
					$array[] = $monsterModes[$mode];
			}
		if($modes)
			foreach ($modes as $mode) {
				if(isset($monsterModes[$mode]))
					$array[] = $monsterModes[$mode];
			}
		return array_unique($array);
 	}

	/**
	 * Return the template name ("default")
	 * @access public
	 */
	public function getName()
	{
		return $this->themeName;
	}

	/**
	 * Caps values to min/max
	 * @access public
	 */
	public function cap_value($amount, $min, $max)
	{
		return ($amount >= $max) ? $max : (($amount <= $min) ? $min : $amount);
	}
}
?>
