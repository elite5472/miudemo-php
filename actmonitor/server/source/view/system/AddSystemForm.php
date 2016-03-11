<?php
namespace view\system;

use \miu\app\Route;

class AddSystemForm extends \view\component\composite\FormView
{
	public function __construct($error = null)
	{
		parent::__construct('system_addsystem', Route::createRoute('/user/op/systems/add'), 'post');

		if($error === null)
			$this->data['error'] = null;
		else if ($error == 'missing_refid')
			$this->data['error'] = 'System Id is required to continue.';
		else if ($error == 'missing_name')
			$this->data['error'] = 'System Name is required to continue.';
		else if ($error == 'invalid_refid')
			$this->data['error'] = 'Invalid System Id, only alphanumeric characters and underscores are allowed.';
		else if ($error == 'already_exists')
			$this->data['error'] = 'A system with the given Id already exists. Please try another one.';
		else
			$this->data['error'] = 'An unknown error "'.$error.'" occurred. Please contact an administrator.';
	}
}
?>
