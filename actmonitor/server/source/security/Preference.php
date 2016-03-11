<?php
namespace security;
<a href="Permission.php">Permission.php</a>
/**
 * desc
 * @author Guillermo Borges
 */
class Preference implements IPreference
{
	// ---- STATIC -----------------------------------------------------------//

	private static $cache = null;

	private static function fillCache()
	{
		if(self::$cache === null)
		{
			$cache = array();
			$db = \db::getDatabase('sysdata');
			foreach($db->getAll('get.preference.all') as $result_set)
			{
				self::$cache[$result_set['refid']] = new Permission($result_set);
			}
		}
	}

	public static function get($refid)
	{
		self::fillCache();
		if(isset(self::$cache[$refid]))
			return self::$cache[$refid];
		else
			return null;
	}

	public static function getAll()
	{
		self::fillCache();
		return self::$cache;
	}

	public static function create($refid, $name = null, $description = null)
	{
		self::fillCache();
		$db = \db::getDatabase('sysdata');
		$db->beginTransaction();
		try
		{
			//Check if the record exits.
			if(isset(self::$cache[$refid]))
				throw new \miu\db\DatabaseException("Preference $refid already exists.");

			//Create item description.
			$db->runQuery('set.item_description:name,description', array(
					'name'=>$name,
					'description'=>$description
			));

			$item_description = $db->getFirst('get.item_description.last');

			//Create record.
			$db->runQuery('set.preference:refid,item_description_id', array(
				'refid'=>$refid,
				'item_description_id'=>$item_description['id']
			));

			//Finally, get the result_set from the created user class.
			$result = new Preference($db->getFirst('get.preference:refid', array(
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
	private $description;

	// ---- CONSTRUCTOR ------------------------------------------------------//

	public function __construct($result_set)
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
}
?>
