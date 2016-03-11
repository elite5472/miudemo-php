<?php
namespace security;

class UserClass implements IUserClass
{

	// ---- STATIC -----------------------------------------------------------//

	private static $cache = null;

	private static function fillCache()
	{
		if(self::$cache === null)
		{
			self::$cache = array();
			$db = \db::getDatabase('sysdata');
			foreach($db->getAll('get.user_class.all') as $result_set)
			{
				self::$cache[$result_set['refid']] = new UserClass($result_set);
			}
		}
	}

	/**
	 * Returns an user class object with the given refid.
	 *
	 * @param string $refid
	 *
	 * @return \security\UserClass
	 *
	 * @throws \PDOException if some unexpected error occurs.
	 */
	public static function get($refid)
	{
		self::fillCache();
		if(isset(self::$cache[$refid]))
			return self::$cache[$refid];
		else
			return null;
	}

	/**
	 * Returns all user classes.
	 *
	 * @return array(\secuirty\UserClass)
	 *
	 * @throws \PDOException if some unexpected error occurs.
	 */
	public static function getAll()
	{
		self::fillCache();
		return self::$cache;
	}

	/**
	 * Creates a new UserClass in the database.
	 *
	 * @param string $refid
	 * @param string $name
	 * @param string $description
	 *
	 * @return \security\UserClass
	 *
	 * @throws \PDOException if some unexpected error occurs.
	 * @throws \miu\db\DatabaseException if the record already exists.
	 */
	public static function create($refid, $name = null, $description = null)
	{
		self::fillCache();
		$db = \db::getDatabase('sysdata');
		$db->beginTransaction();
		try
		{

			//Check if the record exits.
			if(isset(self::$cache[$refid]))
				throw new \miu\db\DatabaseException("UserClass $refid already exists.");

			//Create item description.
			$db->runQuery('set.item_description:name,description', array(
					'name'=>$name,
					'description'=>$description
			));

			$item_description = $db->getFirst('get.item_description.last');

			//Create record.
			$db->runQuery('set.user_class:refid,item_description_id', array(
				'refid'=>$refid,
				'item_description_id'=>$item_description['id']
			));

			//Finally, get the result_set from the created user class.
			$result = new UserClass($db->getFirst('get.user_class:refid', array(
					'refid'=>$refid
			)));

			$db->commit();
			self::$cache[$refid] = $result;
			return $result;

		}
		catch(\PDOException $e)
		{
			$db->rollback();
			throw $e;
		}
	}

	// ---- ATTRIBUTES -------------------------------------------------------//

	private $id;
	private $refid;
	private $name;
	private $description = null;
	private $permissions;

	// ---- CONSTRUCTOR ------------------------------------------------------//

	private function __construct($result_set)
	{

		$this->id = $result_set['id'];
		$this->refid = $result_set['refid'];
		$this->name = $result_set['name'];
		$this->description = $result_set['description'];
	}

	// ---- PROPERTIES -------------------------------------------------------//

	public function getId()
	{
		return $this->id;
	}

	public function getRefid()
	{
		return $this->refid;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function getPermission($key)
	{
		$this->loadPermissions();
		if(isset($this->permissions[$key]))
			return $this->permissions[$key];
		else
			return null;
	}

	public function setPermission($key, $value)
	{
		$this->loadPermissions();
		$db = \db::getDatabase('sysdata');
		if(isset($this->permissions[$key]))
		{
			$permission_id = Permission::get($key)->getId();
			$db->runQuery('update.user_class_permission:user_class_id,permission_id,setting', array(
				'user_class_id'=>$this->getId(),
				'permission_id'=>$permission_id,
				'setting'=>$value
			));
		}
		else
		{
			$permission = Permission::get($key);
			if($permission === null)
				$permission = Permission::create($key);

			$db->runQuery('set.user_class_permission:user_class_id,permission_id,setting', array(
				'user_class_id'=>$this->getId(),
				'permission_id'=>$permission->getId(),
				'setting'=>$value
			));
		}

		$this->permissions[$key] = $value;
	}

	// ---- METHODS ----------------------------------------------------------//

	private function loadPermissions()
	{
		if($this->permissions === null)
		{
			$this->permissions = array();
			$db = \db::getDatabase('sysdata');
			$results = $db->getAll('get.user_class_permission:user_class_id', array(
				'user_class_id'=>$this->getId()
			));

			foreach($results as $result)
			{
				$this->permissions[$result['permission_refid']] = $result['setting'];
			}
		}
	}
}
?>
