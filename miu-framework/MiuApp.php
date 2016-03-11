<?php
namespace miu;
include_once __DIR__.'/miu/type/Singleton.php';

class MiuApp extends \miu\type\Singleton
{
	// ---- ATTRIBUTES -----------------------------------------------------------------//

	private $miu_path;
	private $project_path;
	private $assets_path;
	private $config_path;
	private $tests_path;
	private $url_folder = '';
	private $assets_folder = '/assets';

	private $environment;
	private $buffer;

	private $autoloader;
	private $debugger;
	private $router;

	// ---- CONSTRUCTOR ----------------------------------------------------------------//

	protected function __construct()
	{
		include_once 'miu/app/AutoLoader.php';
		$this->autoloader = \miu\app\AutoLoader::getInstance();
		$this->autoloader->add(__DIR__);
		$this->autoloader->add(__DIR__.'/glob');
		$this->autoloader->add(__DIR__.'/util');
		$this->debugger = \miu\app\Debugger::getInstance();

		$this->router = \miu\app\Router::getInstance();
	}

	// ---- PROPERTIES -----------------------------------------------------------------//

	public function addIncludePath($path)
	{
		$this->autoloader->add($path);
	}


	public function setAssetsPath($path)
	{
		$this->assets_path = $path;
	}

	public function getAssetsPath()
	{
		return $this->assets_path;
	}


	public function setConfigPath($path)
	{
		$this->config_path = $path;
	}

	public function setUnitTestPath($path)
	{
		$this->test_path = $path;
	}

	public function setURLFolder($folder)
	{
		$this->url_folder = $folder;
	}


	public function setAssetsFolder($folder)
	{
		$this->assets_folder = $folder;
	}

	public function getAssetsFolder()
	{
		return $this->assets_folder;
	}


	public function getEnvironment()
	{
		return $this->environment;
	}

	// ---- METHODS --------------------------------------------------------------------//

	public function callController($controller_class, $method)
	{
			$controller = new $controller_class();
			$redirect = $controller->$method();
			if($redirect)
			{
				$this->router->redirect($redirect);
			}
	}


	public function start()
	{
		$this->start_base();
		$this->debugger->setErrorHandlers(false);
		$result = $this->router->route($this->getRequestType(), $this->getRequest());
		if(!$result)
			$this->router->redirect(404);
	}

	public function startDebug()
	{
		$this->environment = new \miu\environment\HTTPEnvironment($this->url_folder);
		$this->debugger->setErrorHandlers(true);

		$this->debugger->useAssertions();

		$this->environment->startBuffer();

			$this->autoloader->loadConfigs($this->config_path);


			$result = $this->router->route($this->environment->getRequestMethod(), $this->environment->getRequestString());


			if(!$result)

				$result = $this->router->redirect(404);

			if($result)
			{
				$this->environment->setRequestParameters($result['arguments']);
				$this->callController($result['controller'], $result['method']);
			}

		$this->environment->stopBuffer();
	}
	public function startTest()
	{

	}
}
?>
