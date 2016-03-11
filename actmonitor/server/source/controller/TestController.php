<?php
namespace controller;

/**
 * A controller made to test functionality
 * @author Guillermo Borges
 */
class TestController extends \miu\controller\Controller
{
	public function doIndex()
	{
		$array[5] = 5;
		$array[4] = 4;
		$array[3] = 3;

		foreach($array as $number)
			echo $number."<br>\n";
	}

	public function makeCreateRegkey($context)
	{
		$context->addFunction(function() use ($context)
		{
			$db = \db::getDatabase('sysdata');
			$regkey = uniqid();
			$class = \security\UserClass::get('admin');
			$query = $db->getQuery('set.register_key:refid,user_class_id', array('refid'=>$regkey, 'user_class_id'=>$class->getId()));
			$query->execute();

			$context->doRedirect('/security/register?regkey='.$regkey);
		});
	}
}
?>
