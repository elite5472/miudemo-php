<?php
namespace miu\app;

class AutoLoader extends \miu\type\Singleton
{
	private $project_class_path = __DIR__;
	private $miu_class_path = __DIR__;
	private $global_class_path = __DIR__;

	private $paths = array();

	public function __construct()
	{
		$paths = &$this->paths;
		spl_autoload_register(function($class) use (&$paths)
		{
			$class_path = '/' . str_replace('\\', '/', $class) . '.php';
			foreach($paths as $location)
			{
				$file = realpath($location . $class_path);
				if(file_exists($file))
				{
					include_once $file;
					return true;
				}
			}
			return false;
		});
	}

	public function add($path)
	{
		$this->paths[] = realpath($path);
	}

	public function includeFile($relpath)
	{
		$relpath = '/' . str_replace('\\', '/', $relpath);
		foreach($this->paths as $location)
		{
			$file = $location . $relpath;
			if(file_exists($file))
			{
				include $file;
				return true;
			}
		}
		return false;
	}

	public function getFileLocation($relpath)
	{
		$relpath = '/' . str_replace('\\', '/', $relpath);
		foreach($this->paths as $location)
		{
			$file = $location . $relpath;
			if(file_exists($file))
				return $file;
		}

		return null;
	}

	public function loadConfigs($config_path)
	{
		#Load Other Configurations automatically.

		foreach(\PathUtil::directoryToArray($config_path, 'php', true, false) as $file)

		{

			include $file;

		}
	}
}

?>
