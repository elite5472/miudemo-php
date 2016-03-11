<?php
namespace miu\controller;
class Context implements \Iterator
{
	private $position = 0;
	private $functions = array();

	private $returned = null;
	private $controller = null;

	public function __construct(Controller $controller)
	{
		$this->controller = $controller;
	}

	public function addFunction($function, $args = null)
	{
		if(is_callable($function) && $function instanceof \Closure)
		{
			if($args != null)
				$this->functions[] = $args;
			$this->functions[] = $function;
			return $this;
		}
		else
		{
			$this->functions[] = $function;
		}
	}

	public function addControl($control, $args = array())
	{
		if(is_callable($control) && $control instanceof \Closure)
		{
			$this->functions[] = $args;
			$control($this);
			return $this;
		}
		else
			throw new \Exception('$control is not callable');
	}

	public function saveTo(&$var)
	{
		$this->addFunction(function() use (&$var)
		{
			$args_count = func_num_args();
			if($args_count == 0)
				$var = null;
			else if ($args_count == 1)
				$var = func_get_arg(0);
			else
				$var = func_get_args();

			return $var;
		});
	}

	public function returned()
	{
		return $this->returned;
	}

	public function doRender($view=null)
	{
		$this->controller->_promptRender($view);
		return null;
	}

	public function doRedirect($url=null)
	{
		$this->controller->_promptRedirect($url);
		return null;
	}

	public function doReturn()
	{
		$this->controller->_promptReturn();
		return null;
	}

	public function current()
	{
		if(!isset($this->functions[$this->position])) return null;
		return $this->functions[$this->position];
	}

	public function key()
	{
		return $this->position;
	}

	public function next()
	{
		$this->position++;
		$function = $this->current();
		if(is_callable($function) && $function instanceof \Closure)
		{
			if($this->returned == null)
				$this->returned = $function();
			else if(is_array($this->returned))
				$this->returned = call_user_func_array($function, $this->returned);
			else
				$this->returned = $function($this->returned);
		}
		else
			$this->returned = $function;
	}

	public function rewind()
	{
		$this->position = 0;
		$this->returned = null;

		$function = $this->current();

		if(is_callable($function) && $function instanceof \Closure)
		{
			if($this->returned == null)
				$this->returned = $function();
			else if(is_array($this->returned))
				$this->returned = call_user_func_array($function, $this->returned);
			else
				$this->returned = $function($this->returned);
		}
		else
			$this->returned = $function;
	}

	public function valid()
	{
		return isset($this->functions[$this->position]);
	}

	public function __get($name)
	{
		return $this->controller->$name;
	}

	public function __set($name, $value)
	{
		$this->controller->$name = $value;
	}

	public function __call($method, $arguments)
	{
		return $this->controller->callMethod($method, $arguments);
	}
}
?>
