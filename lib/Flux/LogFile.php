<?php
/**
 * Convenient way to log information to files.
 */
class Flux_LogFile {
	/**
	 * File handler for open log file.
	 *
	 * @access public
	 * @var resource
	 */
	private $fp;

	/**
	 * Log file name.
	 *
	 * @access public
	 * @var string
	 */
	public $filename;

	/**
	 * Date format used to indicate when the action was logged.
	 *
	 * @access public
	 * @var string
	 */
	public $dateFormat = '[Y-m-d H:i:s] ';

	/**
	 * Create new LogFile instance.
	 *
	 * @param string $filename
	 * @param string $mode (see: http://www.php.net/fopen)
	 * @access public
	 */
	public function __construct($filename, $mode = 'a')
	{
		$this->filename  = "$filename.php";
		$isNewFile       = !file_exists($this->filename);

		if ($isNewFile) {
			touch($this->filename);
			chmod($this->filename, 0600);
		}

		$this->fp = fopen($this->filename, 'a');
		if ($isNewFile) {
			fputs($this->fp, "<?php exit('Forbidden'); ?>\n");
		}
	}

	/**
	 * Close file handle.
	 */
	public function __destruct()
	{
		if ($this->fp) {
			fclose($this->fp);
		}
	}

	/**
	 * Write a line to the log file.
	 *
	 * @param string $format
	 * @param string $var, ...
	 * @access public
	 */
	public function puts()
	{
		$args = func_get_args();
		if (count($args) > 0) {
			$args[0]   = sprintf("%s%s\n", date($this->dateFormat), $args[0]);
			$arguments = array_merge(array($this->fp), $args);
			return call_user_func_array('fprintf', $arguments);
		}
		else {
			return false;
		}
	}
}
?>
