<?php
namespace view\page\system;

use \miu\app\Route;

use \view\component\content\GridCard;
use \view\component\content\ContentPanel;
use \view\component\composite\FlowPanel;
use \view\page\MainTemplate;

use \security\User;

class SystemView extends MainTemplate
{
	private $leftContentPanel;
	public function __construct($system, $components, User $user = null, $menus = array())
	{
		parent::__construct();

		$leftPanel = $this->getLeftPanel();
		$grid = new FlowPanel();
		$this->setContentPanel($grid);
		$this->setUser($user);

		foreach($components as $component)
		{
			$grid->addComponent($this->getComponentCard(
				$component['refid'],
				$system['refid'],
				$component['name'],
				$component['update_id'],
				$component['update_time'],
				$component['status'],
				$component['color']
			));
		}

		$title = $system['name']?:$system['refid'];
		$description = $system['description']?: 'No description available.';

		$panel = new ContentPanel($title);
		$panel->setText($description);

		$menu_group = array();
		$menu_id = 'systems.system.left_menu';

		$menu_group = \Routes::parseGroup(\Routes::getGroup($menu_id), array(
			':system'=>$system['refid']
		));

		$panel->addItems($menu_group->getRoutes());

		foreach($menus as $menu)
		{
			$custom_menu_id = $menu_id . '.' . $menu;
			$menu_group = \Routes::parseGroup(\Routes::getGroup($custom_menu_id), array(
				':system'=>$system['refid']
			));

			$panel->addItems($menu_group->getRoutes());
		}

		$this->getLeftPanel()->addComponent($panel);
	}

	public function getComponentCard($refid, $system_refid, $name=null, $update_id=null, $update_time='Not Reported', $status='UNKOWN', $color='Gray')
	{
		if($update_id)
			return new GridCard(
				$refid,
				Route::createRoute('/systems/' . $system_refid . '/components/' . $refid, $name?:$refid),
				Route::createRoute('/updates/' . $update_id, $update_time),
				Route::createRoute('/systems/' . $system_refid . '/filter?status=' . $status, $status),
				$color);
		else
			return new GridCard(
				$refid,
				Route::createRoute('/systems/' . $system_refid . '/components/' . $refid, $name?:$refid),
				Route::createRoute(null, 'Not Reported'),
				Route::createRoute(null, 'UNKNOWN'),
				$color);
	}
}
?>
