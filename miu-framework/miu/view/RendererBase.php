<?php
namespace miu\view;

abstract class RendererBase implements IRenderer
{
	private $dependencies = array();
	private $default_dependencies_loaded = array();

	public function __construct()
	{

	}

	protected function addDependency(Dependency $dependency)
	{
		$this->dependencies[$dependency->getName()][] = $dependency;
	}

	public function getDependencies(Dependency $dependency_class)
	{

		if(\env::get('settings.view.dependencies.autoload') && !isset($this->default_dependencies_loaded[$dependency_class->getName()]))
		{
			$this->getDefaultDependencies($dependency_class);
			$this->default_dependencies_loaded[$dependency_class->getName()] = true;
		}

		if(!isset($this->dependencies[$dependency_class->getName()])) return array();
		return $this->dependencies[$dependency_class->getName()];
	}

	private function getDefaultDependencies($dependency_class)
	{
		$class = new \ReflectionClass(get_class($this));

		while ($parent = $class->getParentClass())

		{
			$class_name = $class->getName();
			$class = $parent;


			$base = str_replace("\\", "/", $class_name);

			$base_path = \miu\MiuApp::getInstance()->getAssetsPath() . '/' . $dependency_class->getExtension() . '/' . $base;


			$preferred = '.' . \miu\app\UserAgentManager::getInstance()->getUserAgent();

			$default = '.default';
			$type = $dependency_class->getExtension();
			$dependency_class_name = $dependency_class->getName();


			if(!file_exists($base_path.$preferred.'.'.$type))

			{

				$preferred = $default;

				if(!file_exists($base_path.$default.'.'.$type))

					continue;

			}

			$this->addDependency($dependency_class->getResource('/'.$base.$preferred));


		}
	}

	public function __toString()
	{
		return $this->render(null);
	}
}
?>
