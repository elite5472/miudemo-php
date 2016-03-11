<?php
namespace view\component\content;
class ListItem extends \miu\view\TemplateRenderer
{
	public function __construct($title, $tag = null, $color = null)
	{
		$this->data['title'] = $title;
		$this->data['tag'] = $tag;
		$this->data['color'] = $color;
	}
}
?>
