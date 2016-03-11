<?php
namespace view\security;
class RegisterForm extends \view\component\composite\FormView
{
	public function __construct($key, $errors = array())
	{
		parent::__construct('security_register', \miu\app\Route::createRoute('/security/register?regkey='.$key), 'post');

		$this->data['post'] = $_POST;
		$this->data['errors'] = $errors;
	}
}
?>
