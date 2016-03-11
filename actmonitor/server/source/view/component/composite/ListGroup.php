<?php
namespace view\component\composite;
class ListGroup extends \miu\view\ContainerTemplateRenderer
{
	public function __construct($title)
	{
		$this->data['title'] = $title;
	}
}
?>
