<?php
use \miu\app\Route;

/**
 * Serves as a global container for routes.
 * @author Guillermo Borges
 */
class Routes
{
	// ---- STATIC -----------------------------------------------------------//

	private static $routes = array();
	private static $groups = array();

	public static function addRoute($id, Route $route)
	{
		self::$routes[$id] = $route;
		return $route;
	}

	public static function createRoute($id, $route, $name = '', $mark = null)
	{
		self::$routes[$id] = Route::createRoute($route, $name, $mark);
		return $route;
	}

	public function createGroup($id)
	{
		$group = new RouteGroup();
		self::$groups[$id] = $group;
		return $group;
	}

	public static function getRoute($id)
	{
		if(!isset(self::$routes[$id])) return null;
		return self::$routes[$id];
	}

	public static function getGroup($id)
	{
		if(!isset(self::$groups[$id])) return null;
		return self::$groups[$id];
	}

	public static function parseRoute(Route $route, $parse_data)
	{
		$route_name = $route->getName();
		$route_url = $route->getURL();
		$route_mark = $route->getMark();

		foreach($parse_data as $k=>$v)
		{
			if($route_name != null) $route_name = str_replace($k, $v, $route_name);
			if($route_url != null) $route_url = str_replace($k, $v, $route_url);
			if($route_mark != null) $route_mark = str_replace($k, $v, $route_mark);
		}

		return new Route($route_url, $route_name, $route_mark);
	}

	public static function parseGroup($group, $parse_data)
	{
		$new_group = new RouteGroup();

		if(!$group) return $new_group;

		$group_routes = $group->getRoutes();
		foreach($group_routes as $route)
			$new_group->addRoute(self::parseRoute($route, $parse_data));

		return $new_group;
	}
}

class RouteGroup
{
	private $routes = array();

	public function addRoute(Route $route)
	{
		$this->routes[] = $route;
		return $this;
	}

	public function createRoute($route, $name = '', $mark = null)
	{
		$this->routes[] = Route::createRoute($route, $name, $mark);
		return $this;
	}

	public function getRoutes()
	{
		return $this->routes;
	}

	public function getRoute($id)
	{
		if(!isset($this->routes[$id])) return null;
		return $this->routes[$id];
	}
}
?>
