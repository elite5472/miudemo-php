<?php

namespace miu\app;
class Router extends \miu\type\Singleton
{

	// ---- ATTRIBUTES -------------------------------------------------------//

	private $routes = array();
	private $base_url;

	// ---- CONSTRUCTOR ------------------------------------------------------//

	protected function __construct()
	{
		parent::__construct();
		foreach(RequestType::asArray() as $method)
		{
			$this->routes[$method] = array();
		}
	}

	// ---- METHODS ----------------------------------------------------------//

	public function setRoute($request_method, $request, $controller, $method)
	{
		assert(in_array($request_method, RequestType::asArray()));
		$this->routes[$request_method][$request] = array('controller'=>$controller, 'method'=>$method);
	}

	public function route($request_method, $request)
	{
		foreach(array_merge($this->routes[$request_method], $this->routes['ANY']) as $route=>$target)
		{
			$route_s = explode('/', $route);
			$request_s = explode('/', $request);

			$route_size = count($route_s);
			$request_size = count($request_s);

			$matches = true;
			$args = array();

			if($route_size != $request_size)
				continue;

			$top = max($route_size, $request_size);
			for($i = 0; $i < $top; $i++)
			{
				if(\StringUtil::startsWith($route_s[$i], ':'))
				{
					$args[substr($route_s[$i], 1)] = $request_s[$i];
					continue;
				}

				if($route_s[$i] != $request_s[$i])
				{
					$matches = false;
					break;
				}
			}

			if($matches)
			{
				return array('controller'=>$target['controller'], 'method'=>$target['method'], 'arguments'=>$args);
			}
		}
		return null;
	}

	public function redirect($redirect)

	{
		if(is_numeric($redirect))

		{

			header($_SERVER["SERVER_PROTOCOL"].' '.$this->printCode($redirect, true));

			header("Status: $redirect");
			if(isset($this->routes['ANY'][$redirect]))
			{
				$target = $this->routes['ANY'][$redirect];
				return array('controller'=>$target['controller'], 'method'=>$target['method'], 'arguments'=>array());
			}

		}
		else if ($redirect instanceof Route)

		{

			header("Status: 200");

			header("Location: ".$redirect->getURL());

		}

		else

		{

			header ("Status: 200");

			header ("Location: ".Route::createRoute($redirect)->getURL());

		}

	}

	public function printCode($code, $include_code=false)

	{

		// Source: http://en.wikipedia.org/wiki/List_of_HTTP_status_codes



		switch( $code )

		{

			// 1xx Informational

			case 100: $string = 'Continue'; break;

			case 101: $string = 'Switching Protocols'; break;

			case 102: $string = 'Processing'; break; // WebDAV

			case 122: $string = 'Request-URI too long'; break; // Microsoft



			// 2xx Success

			case 200: $string = 'OK'; break;

			case 201: $string = 'Created'; break;

			case 202: $string = 'Accepted'; break;

			case 203: $string = 'Non-Authoritative Information'; break; // HTTP/1.1

			case 204: $string = 'No Content'; break;

			case 205: $string = 'Reset Content'; break;

			case 206: $string = 'Partial Content'; break;

			case 207: $string = 'Multi-Status'; break; // WebDAV



			// 3xx Redirection

			case 300: $string = 'Multiple Choices'; break;

			case 301: $string = 'Moved Permanently'; break;

			case 302: $string = 'Found'; break;

			case 303: $string = 'See Other'; break; //HTTP/1.1

			case 304: $string = 'Not Modified'; break;

			case 305: $string = 'Use Proxy'; break; // HTTP/1.1

			case 306: $string = 'Switch Proxy'; break; // Depreciated

			case 307: $string = 'Temporary Redirect'; break; // HTTP/1.1



			// 4xx Client Error

			case 400: $string = 'Bad Request'; break;

			case 401: $string = 'Unauthorized'; break;

			case 402: $string = 'Payment Required'; break;

			case 403: $string = 'Forbidden'; break;

			case 404: $string = 'Not Found'; break;

			case 405: $string = 'Method Not Allowed'; break;

			case 406: $string = 'Not Acceptable'; break;

			case 407: $string = 'Proxy Authentication Required'; break;

			case 408: $string = 'Request Timeout'; break;

			case 409: $string = 'Conflict'; break;

			case 410: $string = 'Gone'; break;

			case 411: $string = 'Length Required'; break;

			case 412: $string = 'Precondition Failed'; break;

			case 413: $string = 'Request Entity Too Large'; break;

			case 414: $string = 'Request-URI Too Long'; break;

			case 415: $string = 'Unsupported Media Type'; break;

			case 416: $string = 'Requested Range Not Satisfiable'; break;

			case 417: $string = 'Expectation Failed'; break;

			case 422: $string = 'Unprocessable Entity'; break; // WebDAV

			case 423: $string = 'Locked'; break; // WebDAV

			case 424: $string = 'Failed Dependency'; break; // WebDAV

			case 425: $string = 'Unordered Collection'; break; // WebDAV

			case 426: $string = 'Upgrade Required'; break;

			case 449: $string = 'Retry With'; break; // Microsoft

			case 450: $string = 'Blocked'; break; // Microsoft



			// 5xx Server Error

			case 500: $string = 'Internal Server Error'; break;

			case 501: $string = 'Not Implemented'; break;

			case 502: $string = 'Bad Gateway'; break;

			case 503: $string = 'Service Unavailable'; break;

			case 504: $string = 'Gateway Timeout'; break;

			case 505: $string = 'HTTP Version Not Supported'; break;

			case 506: $string = 'Variant Also Negotiates'; break;

			case 507: $string = 'Insufficient Storage'; break; // WebDAV

			case 509: $string = 'Bandwidth Limit Exceeded'; break; // Apache

			case 510: $string = 'Not Extended'; break;



			// Unknown code:

			default: $string = 'Unknown';  break;

		}

		if( $include_code )

			return $code . ' '.$string;

		return $string;

	}
}


?>
