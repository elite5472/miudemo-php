<?php
namespace miu\view;
abstract class Dependency
{
	protected $resource;

	protected static $instances = array();

	public static function getResource($resource)
	{
		$class = get_called_class();
		if(!isset(self::$instances[$class])) self::$instances[$class] = array();
		if(!isset(self::$instances[$class][$resource])) self::$instances[$class][$resource] = new $class($resource);
		return self::$instances[$class][$resource];
	}

	protected final function __construct($resource)
	{
		$this->resource = $resource;
	}

	public function getName()
	{
		return get_class($this);
	}

	public abstract function getExtension();

	public abstract function render();

	public function getRoute()
	{
		if($this->resource instanceof \miu\app\Route)
			return $this->resource;
		else
			return \miu\app\Route::createAsset('/' . $this->getExtension() . $this->resource . '.' . $this->getExtension());
	}

	public function __toString()
	{
		return $this->getName().'('.$this->resource.')';
	}
}
?>
