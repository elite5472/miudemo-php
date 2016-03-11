<?php
namespace view\page;

use \miu\MiuApp;
use \miu\app\Route;

abstract class MainTemplate extends \miu\view\TemplateRenderer
{
	public function __construct()
	{
		parent::__construct();

		$this->components['content'] = new \miu\view\ContainerRenderer();
		$this->components['panel'] = new \miu\view\ContainerRenderer();
		$this->data['menubar.items'] = array();
		$this->data['menubar.items.custom'] = array();
		$this->data['menubar.title'] = Route::createRoute('/', \env::get('settings.name'));

		$this->setUser(null);
	}


	public function getContentPanel()
	{
		return $this->components['content'];
	}

	protected function setContentPanel(\miu\view\IRenderer $panel)
	{
		$this->components['content'] = $panel;
	}

	public function getLeftPanel()
	{
		return $this->components['panel'];
	}

	protected function setLeftPanel($panel)
	{
		$this->components['panel'] = $panel;
	}

	public function setUser(\security\User $user = null)
	{
		$this->data['menubar.items'] = array();

		$menu_id = 'main.header_menu';
		$menu_group = \Routes::getGroup($menu_id);
		if($menu_group)
			$this->data['menubar.items'] = array_merge($this->data['menubar.items'], $menu_group->getRoutes());

		if($user != null && !$user->isPublic())
		{
			$logout_redirect = urlencode(MiuApp::getInstance()->getEnvironment()->getRequestString());
			$this->data['userbar.items'] = array(
				\miu\app\Route::createRoute('/security/logout?redirect='.$logout_redirect, 'Log Out'),
				\miu\app\Route::createRoute('/security/users/'.$user->getRefid().'/settings', 'Settings'),
				\miu\app\Route::createRoute('/security/users/'.$user->getRefid(), $user->getRefid())
			);
			$menu_id .= '.' . $user->getUserClass()->getRefid();
			$menu_group = \Routes::getGroup($menu_id);
			if($menu_group)
				$this->data['menubar.items'] = array_merge($this->data['menubar.items'], $menu_group->getRoutes());
		}
		else
		{
			$this->data['userbar.items'] = array(
				\miu\app\Route::createRoute('/security/login', 'Log In')
			);
		}
	}

	public function addMenu(\miu\app\Route $menu)
	{
		array_push($this->data['menubar.items.custom'], $menu);
	}

	public function getTemplateClass()
	{
		return get_class();
	}

}
?>
