<?php
namespace view\page\security;

use \security\IUser;
use \miu\app\Route;
use \miu\view\ContainerRenderer;
use \view\component\content\ContentPanel;
use \view\security\RegisterForm;

class RegisterView extends ContainerRenderer
{
	public function __construct($user, $key, $errors = array())
	{
		if($user)
		{
			$dialog = new ContentPanel('Registration Successful');
			$dialog->addItem(Route::createRoute('/security/login', 'Click here to log in.'));
			$this->addComponent($dialog);
		}
		else
		{
			$this->addComponent(new RegisterForm($key, $errors));
		}
	}
}
?>
