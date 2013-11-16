<?php
require_once 'Flux/LogFile.php';
require_once 'Flux/Error.php';

class Flux_Connection_Statement {
	public $stmt;
	private static $errorLog;
	
	public function __construct(PDOStatement $stmt)
	{
		$this->stmt = $stmt;
		
		if (!self::$errorLog) {
			self::$errorLog = new Flux_LogFile(FLUX_DATA_DIR.'/logs/mysql/errors/'.date('Ymd').'.log', 'a');
		}
	}
	
	public function execute(array $inputParameters = array())
	{
		$res = $this->stmt->execute($inputParameters);
		Flux::$numberOfQueries++;
		if ((int)$this->stmt->errorCode()) {
			$info = $this->stmt->errorInfo();
			self::$errorLog->puts('[SQLSTATE=%s] Err %s: %s', $info[0], $info[1], $info[2]);
			if (Flux::config('DebugMode')) {
				$message = sprintf('MySQL error (SQLSTATE: %s, ERROR: %s): %s', $info[0], $info[1], $info[2]);
				throw new Flux_Error($message);
			}
		}
		return $res;
	}
	
	public function __call($method, $args)
	{
		return call_user_func_array(array($this->stmt, $method), $args);
	}
}
?>