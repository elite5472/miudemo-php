<?php
namespace view\component\composite;

class ListPanel extends \miu\view\ContainerTemplateRenderer
{
	public function __construct()
	{
		$this->data['is_multipage'] = false;
	}

	public function setMultiPage($previous_route, $next_route)
	{
		$this->data['is_multipage'] = true;
		$this->data['previous'] = $previous_route?:'';
		$this->data['next'] = $next_route?:'';
	}
}
?>
