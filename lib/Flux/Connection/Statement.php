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

    /*
    * Quick PDOStatement::bindParam for array parameter
    * Array parameter must be
    * $array[':paramater'] = [ variable, PDO::PARAM_* constants ];
    */
	private function bindParams(array $params)
	{
		foreach ($params as $key => &$param) {
			if (is_array($param) && $key[0] == ":") {
				$this->stmt->bindParam($key, $param[0], $param[1]);
			} else {
				switch (true) {
					case is_bool($param):
						$type = PDO::PARAM_BOOL;
						break;
					case is_int($param):
						$type = PDO::PARAM_INT;
						break;
					case is_null($param):
						$type = PDO::PARAM_NULL;
						break;
					default:
						$type = PDO::PARAM_STR;
						break;
				}
				$this->stmt->bindParam($key+1, $param, $type);
			}
		}
	}

	public function execute(array $inputParameters = array(), $bind_param = false)
	{
		if ($bind_param) {
			$this->bindParams($inputParameters);
			$res = $this->stmt->execute();
		} else {
			$res = $this->stmt->execute($inputParameters);
		}

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
