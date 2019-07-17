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

	private function getParamType($param)
	{
		switch (true) {
			case is_bool($param):
				return PDO::PARAM_BOOL;
			case is_int($param):
				return PDO::PARAM_INT;
			case is_null($param):
				return PDO::PARAM_NULL;
		}
		return PDO::PARAM_STR;
	}

	public function execute(array $inputParameters = array(), $bind_param = false)
	{
		if ($bind_param) {
			foreach ($inputParameters as $key => &$param) {
				if ($key[0] == ":") {
					if (is_array($param)) {
						// $params = [ :param => [ val, PDO::PARAM_ ], ... ];
						$this->stmt->bindParam($key, $param[0], $param[1]);
					} else {
						// $params = [ :param => val, ... ];
						$this->stmt->bindParam($key, $param, $this->getParamType($param));
					}
				} else {
					// $params = [ val, ... ];
					$this->stmt->bindParam($key+1, $param, $this->getParamType($param));
				}
			}
			$res = $this->stmt->execute();
		} else {
			$res = $this->stmt->execute(empty($inputParameters) ? null : $inputParameters);
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
