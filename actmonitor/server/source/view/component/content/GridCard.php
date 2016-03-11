<?php
namespace view\component\content;
class GridCard extends \miu\view\TemplateRenderer
{
	public function __construct($id, $name, $time, $tag, $color='Gray')
	{
		$this->data['id'] = $id;
		$this->data['name'] = $name;
		$this->data['time'] = $time;
		$this->data['tag'] = $tag;
		$this->data['color'] = $color;
	}
}
?>
