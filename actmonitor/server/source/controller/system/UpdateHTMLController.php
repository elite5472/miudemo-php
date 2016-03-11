<?php
namespace controller\system;

use \security\UserClass;
use \security\Permission;

use \controller\security\SecurityHTMLController;

use \view\page\system\UpdateIndexView;

use \miu\app\Route;
use \miu\view\HTMLView;

use \DateTime;

/**
 * @author Guillermo Borges
 */
class UpdateHTMLController extends \miu\controller\Controller
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

			$updates = array();
			$update = null;
			$last_update = null;
			$remaining = null;
			$previous = null;

			$page = $context->getEnvironment()->getInputValue('page');
			if(!$page) $page = 0;

			$query = $db->getQuery('get.updates.all:user_id,last_system_update_id,upper_limit', array(
				'user_id'=>$user->getId(),
				'last_system_update_id'=>$page,
				'upper_limit'=>\env::get('app.updates.show_amount')
			));

			try
			{
				$query->execute();
				foreach($query->fetchall() as $row)
				{
					$array_id = 'id'.$row['update_id'];
					if(!isset($updates[$array_id]))
					{
						$update = array();
						$update['components'] = array();
						$update['system_refid'] = $row['system_refid'];
						$update['system_name'] = $row['system_name'];
						$update['update_id'] = $row['update_id'];

						$timestamp = new DateTime();
						$timestamp->setTimestamp($row['update_time']);
						$now = new DateTime();
						$time = '';

						$diff = $now->diff($timestamp);
						if($diff->days == 0)
							$time = 'Today, ' . $timestamp->format(\env::get('constants.timestamp.time_only'));
						else if($diff->days == 1)
							$time = 'Yesterday, ' . $timestamp->format(\env::get('constants.timestamp.time_only'));
						else
							$time = $timestamp->format(\env::get('constants.timestamp.long'));

						$update['update_time'] = $time;

						$updates[$array_id] = $update;
					}

					$component = array();
					$component['refid'] = $row['component_refid'];
					$component['name'] = $row['component_name'];
					$component['status'] = $row['current_status'];

					if(\env::get('app.components.status.color.'.$component['status']))
						$component['color'] = \env::get('app.components.status.color.'.$component['status']);
					else
						$component['color'] = null;

					$updates[$array_id]['components'][] = $component;
				}

				if($update)
					$last_update = $update['update_id'];
			}
			catch (PDOException $e)
			{
				return $context->doRedirect(500);
			}

			if($last_update)
			{
				$remaining_row = $db->getFirst('get.updates.all.remaining:user_id,last_system_update_id', array(
					'user_id'=>$user->getId(),
					'last_system_update_id'=>(int)$last_update
				));
				if($remaining_row['remaining'] > 0)
					$remaining = Route::createRoute('/updates?page='.$last_update);
			}

			if($page > 0)
			{
				$previous_row = $db->getFirst('get.updates.all.previous:user_id,previous_system_update_id,upper_limit', array(
					'user_id'=>$user->getId(),
					'previous_system_update_id'=>$page,
					'upper_limit'=>\env::get('app.updates.show_amount')
				));
				if($previous_row && $previous_row['previous'] != $page)
					$previous = Route::createRoute('/updates?page='.($previous_row['previous']));
			}

			$view = new HTMLView(new UpdateIndexView($updates, $previous, $remaining, 'All', $user));
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

		});
	}

	// ---- OVERRIDDEN METHODS -----------------------------------------------//
}
?>
