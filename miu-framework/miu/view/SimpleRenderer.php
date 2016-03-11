<?php
namespace miu\view;

/**
 * A class that simplifies the integration of renderers with non renderers, as
 * well as serving as a dummy renderer.
 *
 * @author Guillermo Borges
 */
class SimpleRenderer
{
	private $item;

	public function __construct($item = null)
	{
		$this->item = $item;
	}

	public function getDependencies(Dependency $dependency_class)
	{
		return array();
	}

	public function render($type)
	{
		return ''.$item;
	}
}
?>
