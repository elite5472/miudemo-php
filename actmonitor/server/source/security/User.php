<?php
namespace security;

/**
 * User is an implementation of the IUser interface, providing behavior that
 * facilitates management of user data while still keeping it secure. The User
 * class is different from the others in the sense that it does not need to be
 * coupled to a row in the database. As such, temporary or special users may
 * exist.
 *
 * @author Guillermo Borges
 */
class User implements IUser
{

	// ---- STATIC -----------------------------------------------------------//

	public static function create($username, $email, $password, IUserClass $class)
	{
		$db = \db::getDatabase('sysdata');
		$user = $db->getFirst('get.user:refid', array('refid'=>$username));
		if($user)
			throw new SecurityException('User already exists.');

		$encrypter = new \PasswordHash(8, false);
		$hash = $encrypter->HashPassword($password);

		$db->runQuery('set.user:refid,email,password,user_class_id', array(
			'refid'=>$username,
			'email'=>$email,
			'password'=>$hash,
			'user_class_id'=>$class->getId()
		));

		$user = $db->getFirst('get.user:refid', array('refid'=>$username));

		return new User($user['id'], $username, $email, $class);
	}

	// ---- ATTRIBUTES -------------------------------------------------------//

	private $id;
	private $refid;
	private $email;
	private $user_class;
	private $sessionkey;
	private $credentials = array();

	private $settings = null;

	// ---- CONSTRUCTOR ------------------------------------------------------//

	public function __construct($id = null, $refid = null, $email = null, IUserClass $user_class = null, $sessionkey = null)
	{
		$this->id = $id;
		$this->refid = $refid;
		$this->email = $email;
		$this->user_class = $user_class;
		$this->sessionkey = $sessionkey;
	}
	// ---- OVERRIDDEN METHODS -----------------------------------------------//

	public function getId()
	{
		return $this->id;
	}

	public function getRefid()
	{
		return $this->refid;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getUserClass()
	{
		return $this->user_class;
	}

	public function isPublic()
	{
		return $this->id === null;
	}

	public function getSessionKey()
	{
		return $this->sessionkey;
	}

	public function getSetting($key)
	{
		$this->loadSettings();
		if(isset($this->settings[$key]))
			return $this->settings[$key];
		else
			return null;
	}

	public function setSetting($key, $value)
	{
		if($this->id === null)
			throw new AuthenticationException('Public users cannot change their settings.');

		$this->loadSettings();
		$db = \db::getDatabase('sysdata');
		if(isset($this->settings[$key]))
		{
			$preference_id = Preference::get($key)->getId();
			$db->runQuery('update.user_preference:user_id,preference_id,setting', array(
				'user_id'=>$this->getId(),
				'preference_id'=>$preference_id,
				'setting'=>$value
			));
		}
		else
		{
			$preference = Preference::get($key);
			if($preference === null)
				$preference = Preference::create($key);

			$db->runQuery('set.user_preference:user_id,preference_id,setting', array(
				'user_id'=>$this->getId(),
				'preference_id'=>$preference->getId(),
				'setting'=>$value
			));
		}

		$this->settings[$key] = $value;
	}

	private function loadSettings()
	{
		if($this->settings === null)
		{
			$this->settings = array();

			if($this->id === null)
				return;

			$db = \db::getDatabase('sysdata');
			$results = $db->getAll('get.user_preference:user_id', array(
				'user_id'=>$this->getId()
			));

			foreach($results as $result)
			{
				$this->settings[$result['preference_refid']] = $result['setting'];
			}
		}
	}

	public function addCredential($credential)
	{
		$this->credentials[] = $credential;
	}

	public function hasCredential($credential)
	{
		return in_array($credential, $this->credentials);
	}
}
?>
