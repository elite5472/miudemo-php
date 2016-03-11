<?php
class env
{
	private static $data = array();
	
	public static function set($id, $value)
	{
		assert('!isset(self::$data[$id]) /* A global should not be modified. */');
		self::$data[$id] = $value;
	}
	
	public static function get($id)
	{
		if(!isset(self::$data[$id])) return null;
		return self::$data[$id];
	}
}
?>