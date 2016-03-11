<?php
namespace miu\view;

abstract class View extends CompositeRenderer
{
	public function __construct(IRenderer $component)
	{
		$this->components['component'] = $component;
	}

	public function render($type = null)
	{
		if($type != null) throw new UnsuportedRenderingTypeException('Views can only render as default (null).');

		$dependencies = array();
		foreach($this->getDependencyClasses() as $dependency_class)
		{
			$dependencies = array_merge($dependencies, $this->getDependencies($dependency_class));
			$dependencies = array_merge($dependencies, $this->components['component']->getDependencies($dependency_class));
			$setting = 'dependencies.'.$dependency_class->getExtension().'.global';
			if(is_array(\env::get($setting)))
			{
				foreach(\env::get($setting) as $dependency)
					$dependencies[] = $dependency_class->getResource($dependency);
			}
		}
		$dependencies = array_unique($dependencies);
		return $this->renderWrapper($dependencies, $this->components['component']);
	}

	protected abstract function getDependencyClasses();
	protected abstract function renderWrapper($dependencies, $content);


}

?>
