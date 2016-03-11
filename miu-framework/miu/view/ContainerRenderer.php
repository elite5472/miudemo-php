<?php
namespace miu\view;

/**
 * A simple collection of renderers.
 *
 * @author Guillermo Borges
 */
class ContainerRenderer extends CompositeRenderer
{
	public function addComponent(IRenderer $component)
	{
		$this->components[] = $component;
	}

	public function render($type)
	{
		$result = '';
		foreach($this->components as $component)
		{
			$result .= $component->render($type);
		}

		return $result;
	}
}
?>
