<?php
namespace view\component\composite;
abstract class FormView extends \miu\view\TemplateRenderer
{
	protected $id;
	protected $target;
	protected $method;
	public function __construct($id, \miu\app\Route $target, $method)
	{
		parent::__construct();
		$this->id = $id;
		$this->target = $target->getURL();
		$this->method = $method;
	}

	public function render($type)
	{
		$result = '<form id="'.$this->id.'" name="'.$this->id.'" action="'.$this->target.'" method="'.$this->method.'">';
		$result .= parent::render($type);
		return $result . '<input type="hidden" name="form_id" value="'.$this->id.'"></form>';
	}
}
?>
