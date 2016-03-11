<?php
namespace view\page\system;

use \miu\app\Route;

use \view\component\content\GridCard;
use \view\component\content\ContentPanel;
use \view\component\composite\FlowPanel;
use \view\page\MainTemplate;

use \security\User;

class IndexView extends MainTemplate
{
	public function __construct($systems, User $user = null, $menus = array())
	{
		parent::__construct();

		$leftPanel = $this->getLeftPanel();
		$grid  = new FlowPanel();
		$this->setContentPanel($grid);
		$this->setUser($user);

		foreach($systems as $system)
		{
			if($system['update_id'])
				$grid->addComponent( new GridCard(
					$system['refid'],
					Route::createRoute('/systems/' . $system['refid'], $system['name']),
					Route::createRoute('/updates/' . $system['update_id'], $system['update_time']),
					Route::createRoute('/systems/'.$system['refid'].'/filter?status=' . $system['status'], $system['status_count'] . ' ' . $system['status']),
					$system['color']
				));
			else
				$grid->addComponent( new GridCard(
					$system['refid'],
					Route::createRoute('/systems/' . $system['refid'], $system['name']),
					new Route(null, 'Not Reported'),
					new Route(null, 'UNKNOWN'),
					$system['color']
				));

		}

		$title = 'Systems';

		$panel = new ContentPanel($title);

		$menu_group = array();
		$menu_id = 'systems.index.left_menu';

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
}
?>
