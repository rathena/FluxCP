<?php
require_once 'Flux/Error.php';

/**
 * This library provides a means of creating a temporary table in MySQL and
 * populating it with the rows from various other tables.
 *
 * This is particularly useful when you need to merge the data in a
 * destructive manner allowing you to view a result set that has been
 * overridden by following tables.
 *
 * Use-case in Flux would be combining item_db/item_db_re/item_db2, mob_db/mob_db2, 
 * and mob_skill_db/mob_skill_db2.
 */
class Flux_TemporaryTable {
	/**
	 * Connection object used to create table.
	 *
	 * @access public
	 * @var Flux_Connection
	 */
	public $connection;
	
	/**
	 * Temporary table name.
	 *
	 * @access public
	 * @var string
	 */
	public $tableName;
	
	/**
	 * Array of table names to select from and re-populate the temporary table
	 * with, overriding each duplicate record.
	 *
	 * @access public
	 * @var array
	 */
	public $fromTables;
	
	/**
	 * Exception class to raise when an error occurs.
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public static $exceptionClass = 'Flux_Error';
	
	/**
	 * Create new temporary table.
	 *
	 * @param Flux_Connection $connection
	 * @param string $tableName
	 * @param array $fromTables
	 * @access public
	 */
	public function __construct(Flux_Connection $connection, $tableName, array $fromTables)
	{
		$this->connection = $connection;
		$this->tableName  = $tableName;
		$this->fromTables = $fromTables;
		
		if (empty($fromTables)) {
			self::raise("One or more tables must be specified to import into the temporary table '$tableName'");
		}
		
		// Find the first table.
		reset($this->fromTables);
		$firstTable = $this->fromTables[0];
		$secondTable = $this->fromTables[1];
		
		if ($this->create($secondTable)) {
			// Insert initial row set.
			// Rows imported from the following tables should overwrite these rows.
			if (!$this->import($firstTable, false)) {
				self::raise("Failed to import rows from initial table '$firstTable'");
			}
			
			foreach (array_slice($this->fromTables, 1) as $table) {
				if (!$this->import($table)) {
					self::raise("Failed to import/replace rows from table '$table'");
				}
			}
		}
	}
	
	/**
	 * Create actual temporary table in the database.
	 *
	 * @param string $firstTable
	 * @return bool
	 * @access private
	 */
	private function create($firstTable)
	{
		// Drop temporary table before hand.
		$this->drop();
		
		$sth = $this->connection->getStatement("DESCRIBE $firstTable");
		$res = $sth->execute();
		
		if (!$res) {
			return false;
		}

		$cols    = $sth->fetchAll();
		$bind    = array();
		$sql     = "CREATE TEMPORARY TABLE {$this->tableName} (";
		$primary = false;
		$uniques = array();
		$indices = array();

		// Origin column, indicates which table the record came from.
		$varcharLength   = $this->findVarcharLength();
		$origin          = new Flux_DataObject();
		$origin->Field   = 'origin_table';
		$origin->Type    = "varchar($varcharLength)";
		$origin->Null    = 'YES';
		$origin->Key     = '';
		$origin->Default = null;
		$origin->Extra   = '';

		// Add origin column.
		$cols[] = $origin;

		foreach ($cols as $col) {
			// Determine default value.	
			if ($col->Default) {
				$default = 'DEFAULT ?';
				$bind[]  = $col->Default;
			}
			else {
				$default = '';
			}
			// Find primary key.
			if ($col->Key == 'PRI') {
				$primary = $col->Field;
			}
			// Find any unique keys.
			elseif ($col->Key == 'UNI') {
				$uniques[] = $col->Field;
			}
			// Find any indexed keys.
			elseif ($col->Key == 'MUL') {
				$indices[] = $col->Field;
			}
			$null = $col->Null == 'YES' ? 'NULL' : 'NOT NULL'; // Determine NULL status.
			$sql .= rtrim("\n\t`{$col->Field}` {$col->Type} $null $default {$col->Extra},");
		}
		// Add primary key.
		if ($primary) {
			$sql .= "\n\tPRIMARY KEY( `$primary` ),";
		}
		// Add unique keys.
		if ($uniques) {
			foreach ($uniques as $unique) {
				$sql .= "\n\tUNIQUE KEY `$unique` ( `$unique` ),";
			}
		}
		// Add index keys.
		if ($indices) {
			foreach ($indices as $index) {
				$sql .= "\n\tKEY `$index` (`$index`),";
			}
		}

		$sql  = rtrim($sql, ', ');
		$sql .= "\n);";
		
		$sth = $this->connection->getStatement($sql);
		$res = $sth->execute($bind);
		
		if (!$res) {
			$message  = "Failed to create temporary table '{$this->tableName}'.\n";
			$message .= sprintf('Error info: %s', print_r($sth->errorInfo(), true));
			self::raise($message);
		}
		
		return true;
	}
	
	/**
	 * Import rows from a specified table into the temporary table, optionally
	 * overwriting duplicate primay key rows.
	 *
	 * @param string $table
	 * @param bool $overwrite
	 * @return bool
	 * @access private
	 */
	private function import($table, $overwrite = true)
	{
		$act = $overwrite ? 'REPLACE' : 'INSERT';
		$sql = "$act INTO $this->tableName SELECT $table.*, '$table' FROM $table";
		$sth = $this->connection->getStatement($sql);
		
		return $sth->execute();
	}
	
	/**
	 * Find the length of the longest table name, which should be used to
	 * determine the length of the VARCHAR field in the temporary table.
	 *
	 * @return int
	 * @access private
	 */
	private function findVarcharLength()
	{
		$length = 0;
		foreach ($this->fromTables as $table) {
			if (($strlen=strlen($table)) > $length) {
				$length = $strlen;
			}
		}
		return $length;
	}
	
	/**
	 * Throw an exception.
	 *
	 * @param string $message
	 * @throws Flux_Error
	 * @access private
	 * @static
	 */
	private static function raise($message = '')
	{
		$class = self::$exceptionClass;
		throw new $class($message);
	}
	
	/**
	 * Drop temporary table.
	 *
	 * @return bool
	 * @access public
	 */
	public function drop()
	{
		$sql = "DROP TEMPORARY TABLE IF EXISTS {$this->tableName}";
		$sth = $this->connection->getStatement($sql);
		
		return $sth->execute();
	}
	
	public function __destruct()
	{
		$this->drop();
	}
}
?>
