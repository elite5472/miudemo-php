<?php
namespace miu\view;
class StylesheetDependency extends Dependency
{
	public function getExtension()
	{
		return 'css';
	}
	
	public function render()
	{
		return '<link rel="stylesheet" href="' . $this->getRoute()->getURL() . '">';
	}
}
?>