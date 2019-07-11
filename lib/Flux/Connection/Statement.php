<?php
require_once 'Flux/LogFile.php';
require_once 'Flux/Error.php';

class Flux_Connection_Statement {
	public $stmt;
	private $bind_param = false;
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
    public function bindParams(array $params)
    {
        foreach ($params as $key => $param) {
            $this->stmt->bindParam($key, $param[0], $param[1]);
        }

        $this->bind_param = true;
    }

	public function execute(array $inputParameters = array(), $bind_param = false)
	{
		if ($this->bind_param) {
			$res = $this->stmt->execute();
		} elseif ($bind_param) {
			foreach ($inputParameters as $i => &$param) {
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
				$this->stmt->bindParam($i+1, $param, $type);
			}
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
