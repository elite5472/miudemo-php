<?php
namespace miu\view;

/**
 * Defines a type of template renderer who displays a kind of object container,
 * such as a list or grid.
 *
 * @author Guillermo Borges
 */
class ContainerTemplateRenderer extends TemplateRenderer
{
	public function addComponent(IRenderer $component)
	{
		$this->components[] = $component;
	}
}
?>
