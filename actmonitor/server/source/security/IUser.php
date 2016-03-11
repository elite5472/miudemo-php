<?php
namespace security;

/**
 * Defines behavior of user objets.
 * @author Guillermo
 */
interface IUser
{
	public function getId();
	public function getRefid();
	public function getEmail();

	public function getSetting($key);
	public function setSetting($key, $value);

	public function isPublic();
	public function getUserClass();

	public function getSessionKey();

	public function addCredential($credential);
	public function hasCredential($credential);
}
?>
