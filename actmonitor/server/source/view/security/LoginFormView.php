<?php
namespace view\security;
class LoginFormView extends \view\component\composite\FormView
{
	public function __construct($error = null, $redirect=null)
	{
		parent::__construct('security_login', \miu\app\Route::createRoute('/security/login'.($redirect? '?redirect='.$redirect : '')), 'post');

		if($error === null)
			$this->data['error'] = null;
		else if ($error == 'unauthorized')
			$this->data['error'] = 'You need to login to access this page.';
		else if ($error == 'expired')
			$this->data['error'] = 'Your session has expired. Please login once again.';
		else if ($error == 'denied')
			$this->data['error'] = 'You do not have permission to access this page. Please login as another user.';
		else if ($error == 'rejected')
			$this->data['error'] = 'Invalid user name and password.';
		else
			$this->data['error'] = 'An unknown error "'.$error.'" occurred. Please contact an administrator.';
	}
}
?>
