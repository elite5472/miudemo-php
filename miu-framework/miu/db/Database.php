<?php
namespace miu\db;
class Database
{
	private $id;
	private $kind;
	private $instance;

	public function __construct($id, $kind, \PDO $instance)
	{
		$this->id = $id;
		$this->kind = $kind;
		$this->instance = $instance;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getKind()
	{
		return $this->kind;
	}

	public function getInstance()
	{
		return $this->instance;
	}

	public function getQuery($queryId, $args = array())
	{
		$queryString = \db::getQuery($this->id, $this->kind, $queryId);
		$query = $this->instance->prepare($queryString);
		foreach($args as $key=>$value)
		{
			if(is_int($value))
				$query->bindValue(':'.$key, $value, \PDO::PARAM_INT);
			else
				$query->bindValue(':'.$key, $value);
		}
		return $query;
	}

	public function runQuery($queryId, $args = array())
	{
		$query = $this->getQuery($queryId, $args);
		$query->execute();
		return $query;
	}

	public function getFirst($queryId, $args = array())
	{
		$query = $this->getQuery($queryId, $args);
		$query->execute();
		return $query->fetch();
	}

	public function getAll($queryId, $args = array())
	{
		$query = $this->getQuery($queryId, $args);

		$query->execute();

		return $query->fetchAll();
	}

	public function getStatement($id)
	{
		try { return \db::getQuery($this->id, $this->kind, $id); }
		catch (UndefinedQueryException $e) { return null; }
	}

	public function setStatement($id, $value)
	{
		\db::setQuery($this->id, $this->kind, $id, $value);
	}

	public function beginTransaction()
	{
		$this->instance->beginTransaction();
	}

	public function commit()
	{
		$this->instance->commit();
	}

	public function rollBack()
	{
		$this->instance->rollBack();
	}
}
?>
