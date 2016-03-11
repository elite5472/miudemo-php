<?php
namespace view\page\system;

use \miu\app\Route;

use \view\component\content\ListItem;
use \view\component\content\ContentPanel;
use \view\component\composite\ListPanel;
use \view\component\composite\ListGroup;
use \view\page\MainTemplate;

use \security\User;

class UpdateIndexView extends MainTemplate
{
	public function __construct($updates, $previous, $remaining, $title, User $user = null, $menus = array())
	{
		parent::__construct();

		$leftPanel = $this->getLeftPanel();
		$list  = new ListPanel();
		$this->setContentPanel($list);
		$this->setUser($user);

		foreach($updates as $update)
		{
			$sys_name = $update['system_name']?: $update['system_refid'];
			$list_group = new ListGroup(Route::createRoute(
				'/updates/update/'.$update['update_id'],
				$sys_name . ': ' . $update['update_time']
			));

			foreach($update['components'] as $component)
			{
				$list_item = new ListItem(
					Route::createRoute(
						'/systems/'.$update['system_refid'].'/components/'.$component['refid'],
						($component['name']?:$component['refid'])
					),
					Route::createRoute(
						'/systems/' . $update['system_refid'] . '/filter?status=' . $component['status'], $component['status']
					),
					$component['color']
				);

				$list_group->addComponent($list_item);
			}

			$list->addComponent($list_group);
		}

		if($previous || $remaining)
		{
			if($previous)
				$previous = new Route($previous->getURL(), '<< Previous');
			if($remaining)
				$remaining = new Route($remaining->getURL(), 'Next >>');

			$list->setMultiPage($previous,$remaining);
		}

		$title = 'System Logs: '.$title;

		$panel = new ContentPanel($title);

		$menu_group = array();
		$menu_id = 'updates.index.left_menu';

		$menu_group = \Routes::parseGroup(\Routes::getGroup($menu_id), array());

		$panel->addItems($menu_group->getRoutes());

		foreach($menus as $menu)
		{
			$custom_menu_id = $menu_id . '.' . $menu;
			$menu_group = \Routes::parseGroup(\Routes::getGroup($custom_menu_id), array());

			$panel->addItems($menu_group->getRoutes());
		}

		$this->getLeftPanel()->addComponent($panel);
	}
}
?>
