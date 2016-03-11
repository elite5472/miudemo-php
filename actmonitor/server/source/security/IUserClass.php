<?php
namespace security;

/**
 * Defines behavior of user objets.
 * @author Guillermo
 */
interface IUserClass
{
	public function getId();
	public function getRefid();
	public function getName();
	public function getDescription();
	
	public function getPermission($key);
	public function setPermission($key, $value);
}
?>