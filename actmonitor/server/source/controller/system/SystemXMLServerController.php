<?php
namespace controller\system;

use \controller\security\SecurityXMLController;
use \SimpleXMLElement;

/**
 * @author Guillermo Borges
 */
class SystemXMLServerController extends \miu\controller\Controller
{
	// ---- STATIC -----------------------------------------------------------//

	// ---- ATTRIBUTES -------------------------------------------------------//

	// ---- CONSTRUCTOR ------------------------------------------------------//

	// ---- PROPERTIES -------------------------------------------------------//

	// ---- METHODS ----------------------------------------------------------//

	// ---- CONTROLS ---------------------------------------------------------//

	public function makeSystemUpdate($context)
	{
		$security = new SecurityXMLController();
		$context->addControl($security->makeAuthenticateServer);
		$context->addFunction(function($server) use ($context)
		{
			$env = $context->getEnvironment();
			$input = $env->getRawInput();
			if(!$input) return $context->doRedirect(400);

			try
			{
				$input = new \SimpleXMLElement($input);
				foreach(libxml_get_errors() as $error) $context->doRedirect(400);
			}
			catch(\Exception $e)
			{
				$context->doRedirect(400);
			}

			$output = new SimpleXMLElement(\env::get('constants.xml.header').'<Response />');

			$db = \db::getDatabase('sysdata');
			$db->beginTransaction();
			try
			{
				#Get the given system.
				$system_id = $server->getSystemId();

				#Register a new update.
				$query = $db->getQuery('set.system_update:system_id', array('system_id'=>$system_id));
				$query->execute();

				$query = $db->getQuery('get.system_update.last:system_id', array('system_id'=>$system_id));
				$query->execute();

				$result = $query->fetch();
				$update_id = $result['id'];

				#Prepare variables for binding.
				$component_id = 0;
				$component_refid = '';
				$status = '';
				$reason = '';
				$info = '';

				#Prepare the queries.
				$get_component = $db->getQuery('get.system_component:refid,system_id');
					$get_component->bindParam(':system_id', $system_id);
					$get_component->bindParam(':refid', $component_refid);

				$set_component = $db->getQuery('set.system_component:refid,system_id');
					$set_component->bindParam(':system_id', $system_id);
					$set_component->bindParam(':refid', $component_refid);

				$get_last_component = $db->getQuery('get.system_component.last');

				$set_component_update = $db->getQuery('set.system_component_update:system_component_id,system_update_id,status,reason,info');
					$set_component_update->bindParam(':system_component_id', $component_id);
					$set_component_update->bindParam(':system_update_id', $update_id);
					$set_component_update->bindParam(':status', $status);
					$set_component_update->bindParam(':reason', $reason);
					$set_component_update->bindParam(':info', $info);


				$components_xml = $input->Components;
				foreach($components_xml->children() as $component_xml)
				{
					$component_refid = $component_xml['refid'];
					$status = $component_xml->Update->Status;
					$reason = $component_xml->Update->Reason;
					$info = $component_xml->Update->Info;

					#Set info to null if not provided:
					$info = $info == ''? null : $indo;

					$get_component->execute();
					$result = $get_component->fetch();
					if($result)
						$component_id = $result['id'];
					else
					{
						$set_component->execute();
						$get_last_component->execute();
						$result = $get_last_component->fetch();
						$component_id = $result['id'];
					}

					$set_component_update->execute();
				}
			}
			catch(Exception $e)
			{
				$db->rollBack();
				$output->addChild('Error', $e->getMessage());
				$output->addChild('Result', 'Failute');
				echo $output->asXML();
				return;
			}
			$db->commit();
			$output->addChild('Result', 'Success');
			echo $output->asXML();
		});
	}
}
