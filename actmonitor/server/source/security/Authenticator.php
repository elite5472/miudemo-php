<?php

namespace security;

/**
 * Authenticator is a singleton class that handles the entire authentication
 * process for both servers and users, and ensures that the application remains
 * secure.
 *
 */
class Authenticator extends \miu\type\Singleton implements IAuthenticator
{
	public function authorizePublic()
	{
		return new User();
	}

	public function authorizeUser($user, $password, $expires)
	{
		$db = \db::getDatabase('sysdata');
		$db->beginTransaction();
		try
		{
			$query = $db->getQuery('get.user:refid', array('refid'=>$user));
			$query->execute();
			$result = $query->fetch();

			if(!$result)
				throw new AuthenticationException();

			$class = UserClass::get($result['user_class_refid']);

			if(self::checkPassword($password, $result['password']))
			{
				$sessionkey = uniqid();
				$user = new User($result['id'], $result['refid'], $result['email'], $class, $sessionkey);

				$query = $db->getQuery('update.session.expire:user_id', array('user_id' => $user->getId()));
				$query->execute();

				$query = $db->getQuery('set.session:refid,user_id,expires', array('refid'=>$sessionkey, 'user_id' => $user->getId(), 'expires'=>$expires? 1 : 0));
				$query->execute();

				$db->commit();
				return $user;
			}
			throw new AuthenticationException();
		}
		catch(\PDOException $e)
		{
			$db->rollback();
			throw $e;
		}
	}

	public function authorizeServer($serverkey)
	{
		$db = \db::getDatabase('sysdata');
		$session = $db->getFirst('get.server_session:refid', array('refid'=>$serverkey));

		if(!$session || $session['is_expired'] == '1')
			throw new AuthenticationException();

		$db->runQuery('update.server_session:refid', array('refid'=>$serverkey));

		return new Server($session['refid'], $session['system_id']);
	}

	public function authorizeSession($sessionkey)
	{
		$db = \db::getDatabase('sysdata');
		$db->beginTransaction();
		try
		{
			if(!$sessionkey)
				throw new AuthenticationException('No session key given');

			$query = $db->getQuery('get.session:refid', array('refid'=>$sessionkey));
			$query->execute();
			$result = $query->fetch();

			if(!$result || $result['is_expired'] == 1)
				throw new AuthenticationException('Session expired');

			$update_time = new \DateTime();
			if($result['updated'] != null)
				$update_time->setTimestamp($result['updated']);
			else
				$update_time->setTimestamp($result['created']);
			$expire_time = $update_time->add(new \DateInterval(\env::get('security.session.length')));
			$now = new \DateTime('now');

			if($result['expires'] == 1  && $now > $expire_time)
			{
				$query = $db->getQuery('update.session.expire:user_id', array('user_id' => $result['user_id']));
				$query->execute();

				$db->commit();
				throw new AuthenticationException('Session just got expired');
			}
			else
			{
				$query = $db->getQuery('update.session.refresh:refid', array('refid' => $sessionkey));
				$query->execute();
			}

			$query = $db->getQuery('get.user:id', array('id' => $result['user_id']));
			$query->execute();
			$result = $query->fetch();

			if(!$result)
				throw new AuthenticationException('User not found');

			$class = UserClass::get($result['user_class_refid']);
			$user = new User($result['id'], $result['refid'], $result['email'], $class, $sessionkey);
			$db->commit();

			return $user;
		}
		catch(\PDOException $e)
		{
			$db->rollback();
			throw $e;
		}
	}

	public function deauthorizeUser(IUser $user)
	{
		if(!$user->isPublic())
		{
			$db = \db::getDatabase('sysdata');
			$query = $db->getQuery('update.session.expire:user_id', array('user_id' => $user->getId()));
			$query->execute();
		}
	}

	public function deauthorizeServer(IServer $server)
	{
		throw new \Exception("Feature not implemented");
	}

	private function checkPassword($password, $expected)
	{
		$encrypter = new \PasswordHash(8, false);
		return $encrypter->CheckPassword($password, $expected);
	}
}
?>
