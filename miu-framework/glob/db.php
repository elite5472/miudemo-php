<?php
/**
 * @author Guillermo Borges
 */
class db
{
	private static $databases = array();
	private static $queries = array();
	
	public static function setDatabase($id, $kind, PDO $pdoInstance)
	{
		#Force Exception Mode
		$pdoInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		self::$databases[$id] = new \miu\db\Database($id, $kind, $pdoInstance);
	}
	
	public static function getDatabase($id)
	{
		if(!isset(self::$databases[$id]) || self::$databases[$id] == null) throw new \miu\db\UndefinedDatabaseException();
		return self::$databases[$id];
	}
	
	public static function setQuery($db_id, $kind, $id, $query)
	{
		self::$queries[$db_id][$kind][$id] = $query;
	}
	
	public static function getQuery($db_id, $kind, $id)
	{
		if(!isset(self::$queries[$db_id][$kind][$id])) throw new \miu\db\UndefinedQueryException($id);
		return self::$queries[$db_id][$kind][$id];
	}
}
?>