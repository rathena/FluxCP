<?php
require_once 'Flux/Config.php';
require_once 'Flux/Error.php';

/**
 * Objectifies a given object.
 */
class Flux_DataObject {
	/**
	 * Storage object.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 *
	 */
	protected $_dbConfig;
	
	/**
	 *
	 */
	protected $_encFrom;
	
	/**
	 *
	 */
	protected $_encTo;
	
	/**
	 * Create new DataObject.
	 *
	 * @param StdClass $object
	 * @param array $default Default values
	 * @access public
	 */ 
	public function __construct(array $data = null, $defaults = array())
	{
		if (array_key_exists('dbconfig', $defaults) && $defaults['dbconfig'] instanceOf Flux_Config) {
			$this->_dbConfig = $defaults['dbconfig'];
			unset($defaults['dbconfig']);
		}
		else {
			$tmpArr = array();
			$this->_dbConfig = new Flux_Config($tmpArr);
		}
		
		$this->_encFrom = $this->_dbConfig->getEncoding();
		$this->_encTo   = $this->_encFrom ? $this->_dbConfig->get('Convert') : false;

		if (!is_null($data)) {
			$this->_data = $data;
		}
		
		foreach ($defaults as $prop => $value) {
			if (!isset($this->_data[$prop])) {
				$this->_data[$prop] = $value;
			}
		}
		
		if ($this->_encTo) {
			foreach ($this->_data as $prop => $value) {
				$this->_data[$prop] = iconv($this->_encFrom, $this->_encTo, $value);
			}
		}
	}
	
	public function __set($prop, $value)
	{
		$this->_data[$prop] = $value;
		return $value;
	}
	
	public function __get($prop)
	{
		if (isset($this->_data[$prop])) {
			return $this->_data[$prop];
		}
		else {
			return null;
		}
	}
}
?>
