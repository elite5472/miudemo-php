<?php
namespace view\page\system;

use \miu\app\Route;

use \view\component\content\GridCard;
use \view\component\content\ContentPanel;
use \view\component\composite\FlowPanel;
use \view\page\MainTemplate;

use \security\User;
use \view\system\AddSystemForm;

class AddSystemView extends MainTemplate
{
	public function __construct($error = null, $system = null, User $user = null, $menus = array())
	{
		parent::__construct();

		$leftPanel = $this->getLeftPanel();
		$contentPanel  = $this->getContentPanel();
		$this->setUser($user);

		if(!$system)
			$contentPanel->addComponent(new AddSystemForm($error));
		else
		{
			$dialog = new ContentPanel('System "'.$system['name'].'" created successfully');
			$dialog->addItem(Route::createRoute('/systems/'.$system['refid'], 'Click here to go to your system.'));
			$contentPanel->addComponent($dialog);
		}

		$title = 'Add A New System';

		$panel = new ContentPanel($title);

		$menu_group = array();
		$menu_id = 'systems.add_system.left_menu';

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
