<?php
namespace miu\view;

abstract class CompositeRenderer extends RendererBase
{
	protected $components = array();

	public function getComponents()
	{
		return $this->components;
	}

	public function getDependencies(Dependency $dependency_class)
	{
		$dependencies = parent::getDependencies($dependency_class);
		foreach($this->components as $component)
			$dependencies = array_merge($dependencies, $component->getDependencies($dependency_class));

		return $dependencies;
	}
}
?>
