<?php
namespace view\component\content;
class ContentPanel extends \miu\view\ContainerTemplateRenderer
{
	public function __construct($title)
	{
		parent::__construct();
		$this->data['title'] = $title;
		$this->data['items'] = array();
		$this->data['text'] = null;
	}

	public function addItem($item)
	{
		array_push($this->data['items'], $item);
	}

	public function addItems($item_array)
	{
		$this->data['items'] = array_merge($this->data['items'], $item_array);
	}

	public function setText($text)
	{
		$this->data['text'] = $text;
	}
}
?>
