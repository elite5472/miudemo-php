<?php
namespace miu\view;

class Tag extends CompositeRenderer
{
	private $tag;
	private $content;
	private $attributes;
	
	public function __construct($tag, $attributes = array(), $content = '')
	{
		parent::__construct();
		$this->tag = $tag;
		$this->content = $content;
		$this->attributes = $attributes;
	}
	
	public function addAttribute($key, $value)
	{
		$this->attributes[$key] = $value;
	}
	
	public function addComponent(IRenderer $component)
	{
		$this->components[] = $component;
	}
	
	public function setContent($content)
	{
		$this->content = $content;
	}
	
	public function render($type = null)
	{	
		if(!$type || $type == 'html' || $type == 'xml')
		{
			$tag = '<'.$this->tag;
			foreach($this->attributes as $k=>$v)
				$tag.= ' '.$k.'="'.$v.'"';
			
			if($this->content === null && count($this->components) == 0)
			{
				return $tag.'/>';
			}
			else
			{
				$result = '' . $this->content;
				
				foreach($this->components as $component)
				{
					$result .= $component->render();
				}
				
				return $tag.'>'.$result.'</'.$this->tag.'>';
			}
		}
		else
			throw new UnsuportedRenderingTypeException("Tag only supports 'html' and 'xml'");
	}
	
	
}
?>