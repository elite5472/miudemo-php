<?php
namespace controller\security;

/**
 * A controller class that has preset controls to handle authentication process
 * for controllers that respond to HTML requests.
 *
 * @author Guillermo Borges
 */
class SecurityHTMLController extends \miu\controller\Controller
{

	// ---- STATIC -----------------------------------------------------------//

	// ---- CONTROLS ---------------------------------------------------------//

	/**
	 * Tries to authenticate the user. Upon failure, the user will be redirected
	 * to a login page.
	 *
	 * This is a control function, as such it must be added to a context.
	 *
	 * @param array $user_class The kinds of users that are allowed.
	 * @param string $redirect The place where the user will get redirected to
	 * after login.
	 *
	 * @redirect /security/login If the authentication fails.
	 *
	 * @return \security\IUser The user object provided by the authenticator.
	 */
	public function makeAuthenticate($context)
	{
		$context->addFunction(function ($user_classes = array()) use ($context)
		{
			$redirect = urlencode($context->getEnvironment()->getRequestString());
			$authenticator = \security\Authenticator::getInstance();

			$sessionkey = $context->getEnvironment()->getValue('SecurityHTMLController_sessionkey');
			if($sessionkey === null)
			{
				if(count($user_classes) == 0)
					return $authenticator->authorizePublic();
				else if($redirect)
					return $context->doRedirect('/security/login?reason=unauthorized&redirect='.$redirect);
				else
					return $context->doRedirect('/security/login?reason=unauthorized');
			}

			try
			{
				$user = $authenticator->authorizeSession($sessionkey);
			}
			catch(\security\AuthenticationException $e)
			{
				$context->getEnvironment()->expireValue('SecurityHTMLController_sessionkey');

				if($redirect)
					return $context->doRedirect('/security/login?reason=expired&redirect='.$redirect);
				else
					return $context->doRedirect('/security/login?reason=expired');
			}

			if(count($user_classes) == 0 || in_array($user->getUserClass(), $user_classes))
				return $user;
			else if($redirect)
				return $context->doRedirect('/security/login?reason=denied&redirect='.$redirect);
			else
				return $context->doRedirect('/security/login?reason=denied');
		});
	}

	public function makeGetSystemCredentials($context)
	{
		$context->addFunction(function(\security\User $user, $system_refid, \security\Permission $required_permission = null) use ($context)
		{
			$db = \db::getDatabase('sysdata');
			$redirect = urlencode($context->getEnvironment()->getRequestString());

			if($user->isPublic())
			{
				if($required_permission != null)
					return $context->doRedirect('/security/login?reason=unauthorized&redirect='.$redirect);
				else
					return $user;
			}

			if(!$system_refid)
				return $context->doRedirect(404);

			$result = $db->getFirst('get.system:refid', array('refid'=>$system_refid));
			if(!$result)
				return $context->doRedirect(404);

			$system_id = $result['id'];

			$db = \db::getDatabase('sysdata');
			$results = $db->getAll('get.user_system_permission:user_id,system_id', array(
				'user_id'=>$user->getId(),
				'system_id'=>$system_id
			));

			foreach($results as $permission)
			{
				$user->addCredential($permission['permission_refid']. ':'.$system_refid);
			}

			if( $required_permission != null && (
					!$user->hasCredential('system.read:'.$system_refid) ||
					!$user->hasCredential($required_permission->getRefid().':'.$system_refid)))
				return $context->doRedirect('/security/login?reason=denied&redirect='.$redirect);
			else
				return $user;
		});
	}

	/**
	 * Shows the user a login form and attempts to login the user. If the login
	 * attempt is successful, the user gets redirected.
	 *
	 * @param GET $reason The reason why the redirect occurred.
	 * @param GET $redirect The route that the user will be redirected to after successful login.
	 *
	 * @param POST $username
	 * @param POST $password
	 *
	 * @render \view\security\LoginForm If authentication has not yet succeeded.
	 * @redirect $redirect If authentication succeeds.
	 */
	public function doLogin()
	{
		$env = $this->getEnvironment();
		if($env->getInputValue('form_id') == 'security_login')
		{
			try
			{
				$user = \security\Authenticator::authorizeUser($env->getInputValue('username'), $env->getInputValue('password'), ($env->getInputValue('remember_me') == 'true')? 0 : 1);
				$env->setValue('SecurityHTMLController_sessionkey', $user->getSessionKey());

				$redirect = $env->getInputValue('redirect');
				if($redirect)
					return $redirect;
				else
					return '/';

			}
			catch(\security\AuthenticationException $e)
			{
				$view = new \miu\view\HTMLView(new \view\security\LoginFormView('rejected', $env->getInputValue('redirect')));
				echo $view;
				return;
			}
		}
		else
		{
			$view = new \miu\view\HTMLView(new \view\security\LoginFormView($env->getInputValue('reason'), $env->getInputValue('redirect')));
			echo $view;
			return;
		}
	}

	/**
	 * Tries to authenticate, and if the authentication is successful, then
	 * the user will be deauthorized if not a public user. Finally, logout
	 * will redirect the user to the given route.
	 *
	 * @param GET $redirect The route the user will get redirected to.
	 *
	 * @redirect $redirect
	 */
	public function makeLogout($context)
	{
		$context->addControl($this->makeAuthenticate);
		$context->addFunction(function($user) use ($context)
		{
			if(!$user->isPublic())
				\security\Authenticator::getInstance()->deauthorizeUser($user);
			return $context->doRedirect($context->getEnvironment()->getInputValue('redirect'));
		});
	}

	public function makeRegister($context)
	{
		$context->addFunction(function() use ($context)
		{
			$db = \db::getDatabase('sysdata');
			$doRegister = false;
			$key = null;
			$class = \security\UserClass::get(\env::get('security.register.default_user_class'));
			$mode = \env::get('security.register.mode');

			//Verify regkey and application settings

			if($mode == 'locked')
				return $context->doRedirect(403);
			else if ($mode == 'key')
			{
				$key = $context->getEnvironment()->getInputValue('regkey');
				if(!$key)
					return $context->doRedirect(403);

				$query = $db->getQuery('get.register_key:refid', array('refid'=>$key));
				$query->execute();
				$result = $query->fetch();
				if(!$result || $result['is_expired'] == 1)
					return $context->doRedirect(403);

				$created_time = new \DateTime();
				$created_time->setTimestamp($result['created']);

				$expire_time = $created_time->add(new \DateInterval(\env::get('security.register.key.expires')));
				$now = new \DateTime('now');

				if($now > $expire_time)
				{
					$query = $db->getQuery('update.register_key.expire:refid,user_id', array('refid'=>$key, 'user_id'=>null));
					$query->execute();
					return $context->doRedirect(403);
				}

				$class = \security\UserClass::get($result['user_class_refid']);
			}

			// Provide form and validate input.

			$form = $context->getEnvironment()->getInputValue('form_id');
			if($form == 'security_register')
			{
				$errors = array();

				$username = $_POST['username'];
				if(!preg_match('/^[a-zA-Z0-9\-_]+$/', $username))
					$errors['username.format'] = true;

				$query = $db->getQuery('get.user:refid', array('refid'=>$username));
				$query->execute();
				if($query->fetch())
					$errors['username.exists'] = true;

				$email = $_POST['email'];
				if(!\StringUtil::validateEmail($email,true))
					$errors['email.format'] = true;

				if($email != $_POST['email-repeat'])
					$errors['email.repeat'] = true;

				$password = $_POST['password'];

				if(strlen($password) < 8)
					$errors['password.length'] = true;

				if($password != $_POST['password-repeat'])
					$errors['password.repeat'] = true;

				if(count($errors) > 0)
				{
					$view = new \miu\view\HTMLView(new \view\page\security\RegisterView(null, $key, $errors));
					return $context->doRender($view);
				}
				else
				{
					$user = \security\User::create($username, $email, $password, $class);

					if($key)
					{
						$query = $db->getQuery('update.register_key.expire:refid,user_id', array('refid'=>$key, 'user_id'=>$user->getId()));
						$query->execute();
					}

					$view = new \miu\view\HTMLView(new \view\page\security\RegisterView($user, $key));
					return $context->doRender($view);
				}
			}
			else
			{
				$view = new \miu\view\HTMLView(new \view\page\security\RegisterView(null, $key));
				return $context->doRender($view);
			}
		});
	}
}
?>
