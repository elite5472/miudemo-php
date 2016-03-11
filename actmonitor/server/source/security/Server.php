<?php
namespace security;

/**
 * Implementation of IServer
 * @author Guillermo Borges
 */
class Server implements IServer
{
	// ---- ATTRIBUTES -------------------------------------------------------//

	private $refid;
	private $system_id;

	// ---- CONSTRUCTOR ------------------------------------------------------//

	public function __construct($refid, $system_id)
	{
		$this->refid = $refid;
		$this->system_id = $system_id;
	}

	// ---- OVERRIDDEN METHODS -----------------------------------------------//

	public function getServerKey()
	{
		return $this->refid;
	}

	public function getSystemId()
	{
		return $this->system_id;
	}
}
?>
