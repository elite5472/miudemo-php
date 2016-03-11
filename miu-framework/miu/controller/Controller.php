<?php
namespace miu\controller;
/**
 * Controller is the base class for all application controllers, and it contains
 * all the necesary methods to allow flexible, yet powerful applications.
 *
 *
 *
 *
 * @author Guillermo Borges
 */
abstract class Controller
{

	// ---- ATTRIBUTES -------------------------------------------------------//

	private $has_to_render = false;
	private $render_view = null;

	private $has_to_redirect = false;
	private $redirect_url = null;

	private $has_to_return = false;

	private $fresh_cookies = array();
	private $saved_functions = array();

	private $environment;

	// ---- CONSTRUCTOR ------------------------------------------------------//

	public function __construct()
	{
		$this->environment = \miu\MiuApp::getInstance()->getEnvironment();
	}

	// ---- METHODS ----------------------------------------------------------//

	public final function getEnvironment()
	{
		return $this->environment;
	}

	public final function _promptRender($view)
	{
		$this->has_to_render = true;
		$this->render_view = $view;
	}

	public final function _promptRedirect($url)
	{
		$this->has_to_redirect = true;
		$this->redirect_url = $url;
	}

	public final function _promptReturn($url)
	{
		$this->has_to_return = true;
	}

	public final function callMethod($method, $arguments)
	{
		return call_user_func_array(array($this, $method), $arguments);
	}

	public final function getMethod($method)
	{
		if(method_exists($this, $method))
		{
			if(!isset($this->saved_functions[$method]))
			{
				$object = $this;
				$this->saved_functions[$method] = function() use ($method, $object)
				{
					return call_user_func_array(array($object, $method), func_get_args());
				};
			}
			return $this->saved_functions[$method];
		}
		return null;
	}

	public final function __call($method, $arguments)
	{
		$context = new Context($this);
		$do = 'do'.$method;
		$make = 'make'.$method;

		$this->has_to_render = false;
		$this->has_to_redirect = false;
		$this->has_to_return = false;

		try
		{
			if(method_exists($this, 'setup'))
				$this->setup($context);

			if (method_exists($this, $make))
			{
				$context->addFunction($arguments);
				$this->$make($context);
			}
			else if(method_exists($this, $do))
			{
				$context->addFunction($arguments);
				$context->addFunction($this->getMethod($do));
			}
			else throw new \Exception("Method '".$method."' is undefined in '" . get_class($this) . "'");

			if(method_exists($this, 'teardown'))
				$this->teardown($context);

			foreach($context as $function)
			{
				if($this->has_to_render)
				{
					$this->environment->write($this->render_view);
					return;
				}
				else if ($this->has_to_redirect)
				{
					return $this->redirect_url;
				}
				else if ($this->has_to_return)
				{
					return;
				}

				$return = $context->returned();
			}
			return $return;
		}
		catch(\Exception $e)
		{
			return \miu\app\Debugger::getInstance()->handleControllerException($e);
		}
	}

	public final function __get($name)
	{
		$result = $this->getMethod($name);
		if(!$result) throw new \Exception("Property '".$name."' is undefined in '" . get_class($this) . "'");
		return $result;
	}
}
?>
