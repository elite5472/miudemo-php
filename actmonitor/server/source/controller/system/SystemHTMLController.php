<?php
namespace controller\system;

use \security\UserClass;
use \security\Permission;

use \controller\security\SecurityHTMLController;

use \view\page\system\IndexView;
use \view\page\system\SystemView;
use \view\page\system\AddSystemView;

use \miu\view\HTMLView;

use \DateTime;

/**
 * @author Guillermo Borges
 */
class SystemHTMLController extends \miu\controller\Controller
{
	// ---- STATIC -----------------------------------------------------------//

	// ---- ATTRIBUTES -------------------------------------------------------//

	// ---- CONSTRUCTOR ------------------------------------------------------//

	// ---- PROPERTIES -------------------------------------------------------//

	// ---- METHODS ----------------------------------------------------------//

	// ---- CONTROLS ---------------------------------------------------------//

	public function makeIndex($context)
	{
		$security = new SecurityHTMLController();

		$context->addControl($security->makeAuthenticate, array(
			array(UserClass::get('admin'), UserClass::get('user'))
		));

		$context->addFunction(function($user) use ($context)
		{
			$db =  \db::getDatabase('sysdata');
			$systems = array();

			$display_status = \env::get('app.systems.display_status');
			$display_if_none = \env::get('app.systems.display_color.none');
			$display_if_some = \env::get('app.systems.display_color.some');
			$display_if_unknown = \env::get('app.systems.display_color.unknown');

			$query = $db->getQuery('get.system.all_with_status:user_id,status', array(
				'user_id'=>$user->getId(),
				'status'=>$display_status
			));

			try
			{
				$query->execute();
				foreach($query->fetchall() as $row)
				{
					$system['id'] = $row['id'];
					$system['refid'] = $row['refid'];
					$system['name'] = $row['name']?:$row['refid'];

					if($row['update_id'])
					{
						$timestamp = new DateTime();
						$timestamp->setTimestamp($row['update_time']);
						$now = new DateTime('now');
						$time = '';
						if($timestamp->diff($now)->days == 1)
							$time = 'Yesterday, ' . $timestamp->format(\env::get('constants.timestamp.time_only'));
						else
							$time = $timestamp->format(\env::get('constants.timestamp.short'));
						$system['update_time'] = $time;
						$system['update_id'] = $row['update_id'];
						$system['status'] = $display_status;
						$system['status_count'] = $row['status_count'];

						if($row['status_count'] > 0)
							$system['color'] = $display_if_some;
						else
							$system['color'] = $display_if_none;
					}
					else
					{
						$system['update_id'] = null;
						$system['color'] = $display_if_unknown;
					}
					array_push($systems, $system);
				}
			}
			catch (PDOException $e)
			{
				return $context->doRedirect(500);
			}

			$view = new HTMLView(new IndexView($systems, $user));
			return $context->doRender($view);
		});
	}

	public function makeSystem($context)
	{
		$security = new SecurityHTMLController();
		$user = null;
		$system_refid = $context->getEnvironment()->getRequestParameter('system');

		$context->addControl($security->makeAuthenticate, array(
			array(UserClass::get('admin'), UserClass::get('user'))
		))->saveTo($user);

		$context->addControl($security->makeGetSystemCredentials, array(
			&$user,
			$system_refid,
			Permission::get('system.read')
		));

		$context->addFunction(function() use (&$user, $system_refid, $context)
		{
			$db = \db::getDatabase('sysdata');
			$system = $db->getFirst('get.system:refid', array('refid'=>$system_refid));

			$components = array();
			$rows = $db->getAll('get.system_component.last_updates:system_id', array('system_id'=>$system['id']));

			foreach($rows as $row)
			{
				$component['id'] = $row['id'];
				$component['refid'] = $row['refid'];
				$component['name'] = $row['name'];
				$component['description'] = $row['description'];
				$component['status'] = $row['current_status']?: 'UNKNOWN';

				if(\env::get('app.components.status.color.'.$component['status']))
					$component['color'] = \env::get('app.components.status.color.'.$component['status']);
				else
					$component['color'] = null;

				$component['update_id'] = $row['update_id'];
				if($row['update_id'])
				{
					$timestamp = new DateTime();
    				$timestamp->setTimestamp($row['update_time']);
    				$now = new DateTime('now');
    				$time = '';
    				if($timestamp->diff($now)->days == 1)
						$time = 'Yesterday, ' . $timestamp->format(\env::get('constants.timestamp.time_only'));
					else
						$time = $timestamp->format(\env::get('constants.timestamp.short'));
    				$component['update_time'] = $time;
				}
				else
				{
					$component['update_time'] = null;
				}

				$components[] = $component;
			}

			$menus = array();
			if($user->hasCredential('system.write:'.$system['refid']))
				$menus[] = 'write';
			if($user->hasCredential('system.admin:'.$system['refid']))
				$menus[] = 'admin';

			$view = new HTMLView(new SystemView($system, $components, $user, $menus));
			return $context->doRender($view);
		});
	}

	public function makeAddSystem($context)
	{
		$security = new SecurityHTMLController();

		$context->addControl($security->makeAuthenticate, array(
			array(UserClass::get('admin'), UserClass::get('user'))
		));

		$context->addFunction(function($user) use($context)
		{
			$env = $context->getEnvironment();
			if($env->getInputValue('form_id') == 'system_addsystem')
			{
				$db = \db::getDatabase('sysdata');
				try
				{
					$db->beginTransaction();
					$system_refid = $env->getInputValue('system_refid');
					$system_name = $env->getInputValue('system_name');
					$system_description = $env->getInputValue('system_description');

					if(!$system_refid || $system_refid == '')
					{
						$view = new HTMLView(new AddSystemView('missing_refid', null, $user, array()));
						return $context->doRender($view);
					}
					else if(!preg_match('/^[a-zA-z0-9_]+$/', $system_refid))
					{
						$view = new HTMLView(new AddSystemView('invalid_refid', null, $user, array()));
						return $context->doRender($view);
					}

					if(!$system_name || $system_name == '')
					{
						$view = new HTMLView(new AddSystemView('missing_name', null, $user, array()));
						return $context->doRender($view);
					}

					$existing_system = $db->getFirst('get.system:refid', array('refid'=>$system_refid));
					if($existing_system)
					{
						$view = new HTMLView(new AddSystemView('already_exists', null, $user, array()));
						return $context->doRender($view);
					}

					$db->runQuery('set.item_description:name,description', array(
						'name'=>$system_name,
						'description'=>$system_description
					));

					$item_description = $db->getFirst('get.item_description.last');

					$db->runQuery('set.system:refid,item_description_id', array(
						'refid'=>$system_refid,
						'item_description_id'=>$item_description['id']
					));

					$system = $db->getFirst('get.system:refid', array('refid'=>$system_refid));

					$permissions = array(Permission::get('system.read'), Permission::get('system.write'), Permission::get('system.admin'));

					foreach($permissions as $permission)
					{
						$db->runQuery('set.user_system_permission:user_id,system_id,permission_id,setting', array(
							'user_id'=>$user->getId(),
							'system_id'=>$system['id'],
							'permission_id'=>$permission->getId(),
							'setting'=>'true'
						));
					}

					$db->commit();

					$view = new HTMLView(new AddSystemView(null, $system, $user, array()));
					return $context->doRender($view);
				}
				catch(\Exception $e)
				{
					$db->rollback();
					throw $e;
				}

			}
			else
			{
				$view = new HTMLView(new AddSystemView(null, null, $user, array()));
				return $context->doRender($view);
			}
		});
	}

	// ---- OVERRIDDEN METHODS -----------------------------------------------//
}
?>
