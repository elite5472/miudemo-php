<?php
namespace security;

/**
 * A server object which gives information about the authenticated server, as well
 * as what system it has access to.
 * @author Guillermo Borges
 */
interface IServer
{
	public function getServerKey();
	public function getSystemId();
}
?>
