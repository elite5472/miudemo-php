<?php
namespace security;

/**
 * Defines the behavior of an authentication schema.
 * 
 * @author Guillermo Carvalho Borges
 */

interface IAuthenticator
{
	/**
	 * Creates an user object set for public usage. No settings are saved and
	 * most properties are not set.
	 * 
	 * @returns \security\IUser
	 * @throws AuthenticationException if public users are disabled.
	 */
	public function authorizePublic();
	
	/**
	 * Checks the given credentials against the database, and authenticates the
	 * user if login succeeds. A session is created for the user, and any
	 * previous sessions are expired.
	 * 
	 * @param string $user
	 * @param string $password
	 * @param boolean $expires
	 * 
	 * @returns \security\IUser
	 * @throws AuthenticationException
	 */
	public function authorizeUser($user, $password, $expires);
	
	/**
	 * Checks the given server key against the database, and authenticates the
	 * server if login succeeds. Servers don't have sessions, so they have to
	 * provide credentials with every request.
	 * 
	 * @param string $serverkey
	 * 
	 * @returns \security\IServer
	 * @throws AuthenticationException
	 */
	public function authorizeServer($serverkey);
	
	/**
	 * Checks the given session key against the database. If a record that hasn't
	 * expired is found, then the updated date is checked. If the record doesn't
	 * expire when checked, or if it's not expireable to begin with, the session
	 * will be authrotized.
	 * 
	 * @param string $sessionkey
	 * 
	 * @returns \security\IUser
	 * @throws AuthenticationException
	 */
	public function authorizeSession($sessionkey);
	
	/**
	 * Expires every unexpired session that the user currently has. This
	 * effectively "logs out" the user as credentials will have to be provided
	 * once more.
	 * 
	 * @param \security\IUser $user
	 */
	public function deauthorizeUser(IUser $user);
	
	/**
	 * Expires the server key. The key becomes unusable afterwards, so an user
	 * will have to emit another.
	 *  
	 * @param \security\IServer $server
	 */
	public function deauthorizeServer(IServer $server);
	
}
?>