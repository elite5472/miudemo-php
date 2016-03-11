<?php
namespace miu\view;
class ScriptDependency extends Dependency
{
	public function getExtension()
	{
		return 'js';
	}
	
	public function render()
	{
		return '<script src="' . $this->getRoute()->getURL() . '"></script>';
	}
}
?>