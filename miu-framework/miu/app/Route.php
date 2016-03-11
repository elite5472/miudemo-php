<?php
namespace miu\app;

/**
	Routes are objects that represent URLs and paths. They are used thorough the application to keep
	track of paths and they can be seamlessly converted to anchors or other formats. Routes
	may have names (content of the anchor) and marks (id of the anchor), which makes communication
	between views and controllers easier.
*/
class Route
{
	// ---- ATTRIBUTES -----------------------------------------------------------------//
	private $name;
	private $url;
	private $mark;


	// ---- STATIC METHODS -------------------------------------------------------------//

	/**
		Creates a new route using the applications root url as the base path.
	*/
	public static function createRoute($route='', $name='default', $mark=null)
	{
		return new Route(\miu\MiuApp::getInstance()->getEnvironment()->getScriptURL() . $route, $name, $mark);
	}

	public static function createAsset($route='', $name='default', $mark=null)
	{
		return new Route(\miu\MiuApp::getInstance()->getEnvironment()->getBaseUrl() . \miu\MiuApp::getInstance()->getAssetsFolder(). $route, $name, $mark);
	}


	// ---- CONSTRUCTOR ----------------------------------------------------------------//

	public function __construct($url=null, $name = 'default', $mark = null)
	{
		$this->url = $url;
		$this->name = $name;
		$this->mark = $mark;
	}


	// ---- PROPERTIES -----------------------------------------------------------------//

	public function getURL()
	{
		return $this->url;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getMark()
	{
		return $this->mark;
	}


	// ---- METHODS --------------------------------------------------------------------//

	/**
		Returns an anchor representation of $this object.
	*/
	public function toAnchor()
	{
		$result = '<a';
		if($this->mark != null)
			$result .= ' id="'.$this->mark.'"';
		$result .= ' href="'.$this->url.'">'.$this->name.'</a>';
		return $result;
	}

	public function __toString()
	{
		if($this->url == null)
		{
			if($this->mark == null)
				return $this->name;
			else
				return '<span id="'.$this->mark.'">'.$this->name.'</span>';
		}
		else
		{
			return $this->toAnchor();
		}
	}
}
?>
