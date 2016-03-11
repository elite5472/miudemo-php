<?php
namespace controller\security;

/**
 * A controller class that has preset controls to handle authentication process
 * for controllers that respond to XML requests.
 *
 * @author Guillermo Borges
 */
class SecurityXMLController extends \miu\controller\Controller
{
	public function makeAuthenticateServer($context)
	{
		$context->addFunction(function() use ($context)
		{
			$authenticator = \security\Authenticator::getInstance();

			$sessionkey = $context->getEnvironment()->getInputValue('serverkey');
			if($sessionkey === null)
			{
				return $context->doRedirect(403);
			}

			try
			{
				$server = $authenticator->authorizeServer($sessionkey);
				return $server;
			}
			catch(\security\AuthenticationException $e)
			{
				return $context->doRedirect(403);
			}
		});
	}

	public function makeAuthenticate($context)
	{
		$context->addFunction(function ($user_classes = array()) use ($context)
		{
			$authenticator = \security\Authenticator::getInstance();

			$sessionkey = $context->getEnvironment()->getInputValue('sessionkey');
			if($sessionkey === null)
			{
				if(count($user_classes) == 0)
					return $authenticator->authorizePublic();
				else
					return $context->doRedirect(403);
			}
			try
			{
				$user = $authenticator->authorizeSession($sessionkey);
			}
			catch(\security\AuthenticationException $e)
			{
				return $context->doRedirect(403);
			}

			if(count($user_classes) == 0 || in_array($user->getUserClass(), $user_classes))
				return $user;
			else
				return $context->doRedirect(403);
		});
	}

	public function doLogin()
	{
		$env = $this->getEnvironment();
		$env->setResponseType('application/xml');


		try
		{
			$username = $env->getInputValue('username');
			$password = $env->getInputValue('password');

			if(!$username || !$password)
				return 400;

			$user = \security\Authenticator::authorizeUser($username, $password, true);
			echo \env::get('constants.xml.header').'<Response><Status>Success</Status><SessionKey>'.$user->getSessionKey().'</SessionKey></Response>';
		}
		catch(\security\AuthenticationException $e)
		{
			echo \env::get('constants.xml.header').'<Response><Status>Denied</Status></Response>';
		}
	}

	public function makeLogout($context)
	{
		$context->addControl($this->makeAuthenticate);
		$context->addFunction(function($user) use ($context)
		{
			$env = $context->getEnvironment();
			$env->setResponseType('application/xml');

			if(!$user->isPublic())
				\security\Authenticator::getInstance()->deauthorizeUser($user);
			echo \env::get('constants.xml.header').'<Response><Status>Success</Status></Response>';
		});
	}
}
