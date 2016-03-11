<?php
namespace miu\environment;

/**
 * HTTPEnvironment implements the ControllerEnvironmentAdapter interface, and
 * handles all interactions between controllers, request, input and output.
 *
 * This class is mostly configuration-free, although if your application not
 * hosted as the root of the url, then the folder must be specified in the
 * constructor.
 *
 * @author Guillermo Borges
 */
class HTTPEnvironment implements IEnvironment
{
	private $folder = '';
	private $url;
	private $request;
	private $request_params = array();
	private $hasIndex = false;

	// ---- CONSTRUCTOR ------------------------------------------------------//
	/**
	 * Creates a new HTTPEnvironment.
	 *
	 * Specifying $folder will set a sub-folder of the URL as part of the root.
	 *
	 * Usage:
	 *
	 * If the address to your application is:
	 *
	 * http://www.mydomain.com/path/to/app
	 *
	 * The application folder is:
	 *
	 * /path/to/app
	 *
	 * If the address is the root of the url, as in:
	 *
	 * http://www.mydomain.com
	 *
	 * Then the folder should be left blank (not given or '').
	 *
	 * @param String $folder
	 */
	public function __construct($folder = '')
	{

		$this->folder = $folder;
		$this->url = 'http://'.$_SERVER['HTTP_HOST'].$this->folder;
		$path = $this->folder;
		$requestedUri = $_SERVER['REQUEST_URI'];

		//Remove the get string.
		if (false !== ($getPosition = strpos($requestedUri, '?')))
			$requestedUri = substr($requestedUri, 0, $getPosition);

		//Remove the application folder.
		if(strlen($path) > 0)
			$requestedUri = substr($requestedUri, strlen($path));

		// Remove index.php from URL if it exists
		if (0 === strpos($requestedUri, '/index.php')) {
			$this->hasIndex = true;
			$requestedUri = substr($requestedUri, 10);
			if ($requestedUri == '') {
				$requestedUri = '/';
			}
		}

		//Remove any trailing slashes from Uri except the first / of a uri (Root)
		//Strip out the additional slashes found at the end. If first character is / then leaves it alone
		$end = strlen($requestedUri) - 1;
		while( $end > 0 && $requestedUri[$end] === '/' ){
			$end--;
		}

		$requestedUri = substr($requestedUri, 0, $end+1);
		$this->request = $requestedUri;
	}

	// ---- Request Info -----------------------------------------------------//

	public function getBaseURL()
	{
		return $this->url;
	}

	public function getScriptURL()
	{
		if($this->hasIndex)
			return $this->url . '/index.php';
		else
			return $this->url;
	}

	public function getRequestString()
	{
		return $this->request;
	}

	public function getRequestMethod()
	{
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}

	public function getRequestAddress()
	{
		return $_SERVER['REMOTE_ADDR'];
	}

	public function getRequestParameter($id)
	{
		if(!isset($this->request_params[$id])) return null;
		return $this->request_params[$id];
	}

	public function setRequestParameters($params)
	{
		$this->request_params = $params;
	}

	// --- Cookies and Input -------------------------------------------------//

	public function getValue($key)
	{
		if(isset($_COOKIE[$key]) && $_COOKIE[$key] != '__MIU_EXPIRED_VALUE__')
			return $_COOKIE[$key];
		else if(isset($this->fresh_cookies[$key]))
			return $this->fresh_cookies[$key];
		else
			return null;
	}

	public function setValue($key, $value)
	{
		setcookie($key, $value, 2114388000, '/');
		$this->fresh_cookies[$key] = $value;
	}

	public function expireValue($key)
	{
		setcookie($key, '__MIU_EXPIRED_VALUE__', 1);
	}

	public function getInputValue($key)
	{
		if(isset($_GET[$key])) return $_GET[$key];
		if(isset($_POST[$key])) return $_POST[$key];
		return null;
	}

	public function getRawInput()
	{
		return file_get_contents('php://input');
	}

	// ---- Output ---------------------------------------------------------------------//

	public function setResponseType($type)
	{
		header("Content-Type: {$type}; charset=".\env::get('settings.encoding'));
	}

	public function write($object)
	{
		echo $object;
	}

	public function writeLine($object)
	{
		echo "$object <br>\n";
	}

	public function startBuffer()
	{
		\ob_start();
	}

	public function getOutput()
	{
		return \ob_get_contents();
	}

	public function stopBuffer()
	{
		\ob_end_flush();
	}
}
?>
