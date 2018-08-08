<?php
// You don't need this line if you decided to specify a custom $exceptionClass
// property in your version of Flux_Config.
require_once 'Flux/Error.php';

/**
 * Flux_Config acts as a convenient way to access arrays which are used as a
 * means of storing and retrieving configuration values to the application.
 */
class Flux_Config {
	/**
	 * Configuration array.
	 *
	 * @access private
	 * @var array
	 */
	private $configArr;
	
	/**
	 * Default options for setter.
	 *
	 * @access private
	 * @var array
	 */
	private $defaultSetOptions = array('overwrite' => true, 'force' => true);
	
	/**
	 * This is here for any developer's convenience, just in case he/she would
	 * like to re-use this library without having to depend on the Flux_Error
	 * class to do so. This will cause Flux_Config to raise the exception class
	 * of your choice.
	 *
	 * It's preferable that the developer change the value directly from the
	 * class, instead of finding ways to do it from code.
	 *
	 * @access private
	 * @var string
	 */
	private $exceptionClass = 'Flux_Error';
	
	/**
	 * Construct a Flux_Config instance which acts as a more convenient
	 * accessor to the specified configuration array.
	 *
	 * @param array $configArray Configuration array.
	 * @access public
	 */
	public function __construct(array &$configArr)
	{
		$this->configArr = &$configArr;
	}
	
	/**
	 * This is here... for no real GOOD reason, but should the need arise, at
	 * least you aren't deprived of access to it.
	 *
	 * @return array Configuration array.
	 * @access public
	 */
	public function &toArray()
	{
		return $this->configArr;
	}
	
	/**
	 * Goes through each child in the array which is also an array, and returns
	 * them collectively as an array of Flux_Config instances.
	 *
	 * @return array Array of Flux_Config instances of all children arrays.
	 * @access public
	 */
	public function &getChildrenConfigs()
	{
		$children = array();
		foreach ($this->configArr as $key => &$child) {
			if (is_array($child)) {
				$children[$key] = new Flux_Config($child);
			}
		}
		return $children;
	}
	
	/**
	 * Get the value held by the specified key. If the value is an array it
	 * will be returned as an instance of Flux_Config by default, unless
	 * $configObjectIfArray is set to false.
	 *
	 * Keys are specified in an object-like format, such as: 'Foo.Bar.Baz'
	 * where each dot would denote the difference in depth from key-to-key.
	 *
	 * @param string $key Key sequence.
	 * @param bool $configObjectIfArray True/false whether or not to return Flux_Config instances for values that are an array.
	 * @access public
	 */
	public function get($key, $configObjectIfArray = true)
	{
		$keys = explode('.', $key);
		$base = &$this->configArr;
		$size = count($keys) - 1;
		
		for ($i = 0; $i < $size; ++$i) {
			$currentKey = $keys[$i];
			if (is_array($base) && array_key_exists($currentKey, $base)) {
				$base = &$base[$currentKey];
			}
			else {
				// Short-circuit and return null.
				return null;
			}
		}
		
		$currentKey = $keys[$size];
		if (array_key_exists($currentKey, $base)) {
			$value = &$base[$currentKey];
			if (is_array($value) && $configObjectIfArray) {
				$configClassName = get_class($this);
				return new $configClassName($value);
			}
			elseif ($value instanceOf Flux_Config && !$configObjectIfArray) {
				return $value->toArray();
			}
			else {
				return $value;
			}
		}
		else {
			// We want to avoid a traditional PHP error when referencing
			// non-existent keys, so we'll silently return null as an
			// alternative ;)
			return null;
		}
	}
	
	/**
	 * Set a key to hold the specified value. The format for specifying a key
	 * is 100% identical to Flux_Config::get().
	 *
	 * Options outline:
	 *   overwrite - true/false to overwrite existing keys.
	 *     force   - true/false to force the creation of the key hierarchy.
	 *
	 * @param string $key Key sequence.
	 * @param mixed $value Value to set in the key.
	 * @param array $options Array of options.
	 * @access public
	 */
	public function set($key, $value, $options = array())
	{
		$opts = array_merge($this->defaultSetOptions, $options);
		$keys = explode('.', $key);
		$base = &$this->configArr;
		$size = count($keys) - 1;
		
		for ($i = 0; $i < $size; ++$i) {
			$currentKey = $keys[$i];
			if (is_array($base) && array_key_exists($currentKey, $base)) {
				$base = &$base[$currentKey];
			}
			elseif ($opts['force']) {
				$base[$currentKey] = array();
				$base = &$base[$currentKey];
			}
			else {
				// Short-circuit and return false.
				return false;
			}
		}
		
		$currentKey = $keys[$size];
		if (array_key_exists($currentKey, $base) && !$opts['overwrite']) {
			return false;
		}
		
		$base[$currentKey] = $value;
		return $value;
	}
	
	/**
	 * Convenience method for raising an internal exception.
	 *
	 * @param string $message Error message.
	 * @access public
	 */
	public function raise($message)
	{
		$exceptionClass = $this->exceptionClass;
		throw new $exceptionClass($message);
	}
	
	/**
	 * Adds the ability to call set<ConfigDirective>(<Value>) as native methods.
	 *
	 * @param string $method
	 * @param arary $args
	 * @access public
	 */
	public function __call($method, $args = array())
	{
		if (preg_match('/^get(\S+)$/', $method, $m)) {
			return $this->get($m[1]);
		}
		elseif (preg_match('/^set(\S+)$/', $method, $m)) {
			$options = array();
			$argc    = count($args);
			if ($argc > 1) {
				$options = $args[1];
			}
			elseif ($argc < 1) {
				$class = get_class($this);
				$this->raise("Missing value argument in $class::$method()");
			}
			return $this->set($m[1], $args[0], $options);
		}
	}
	
	/**
	 *
	 */
	public function merge(Flux_Config $config, $recursive = true)
	{
		$mergeMethod     = $recursive ? 'array_merge_recursive' : 'array_merge';
		$this->configArr = $mergeMethod($this->configArr, $config->toArray());
	}
}
?>
