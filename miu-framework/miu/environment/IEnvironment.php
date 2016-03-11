<?php

namespace miu\environment;

/**
 * IEnvironment defines a series of methods that expose input and output
 * functionality. This way, it becomes easier to handle different media types such as
 * html and xml.
 *
 * Additionally, this means that the Inputs that are handled by controllers are obtained
 * by proxy, so testing its behavior is much easier.
 */
interface IEnvironment
{

	// --- Cookies and Input -----------------------------------------------------------//

	/**
	 * Returns a value stored by the user-agent, such as COOKIE.
	 * @param String $id
	 */
	public function getValue($id);

	/**
	 * Gives a value to the user-agent.
	 * @param String $id
	 * @param String $value
	 */
	public function setValue($id, $value);

	/**
	 * Tells the user-agent to expire an assigned value.
	 *
	 * @param String $id
	 */
	public function expireValue($id);

	/**
	 * Returns a value given by the user-agent, such as GET or POST.
	 * @param String $id
	 */
	public function getInputValue($id);

	/**
	 * Returns the exact input given by the user-agent, in case a file is expected.
	 */
	public function getRawInput();

	// ---- Output ---------------------------------------------------------------------//

	/**
	 * Changes the format of the response to the user-agent.
	 * @param String $type
	 */
	public function setResponseType($type);

	/**
	 * Writes an object to the response buffer.
	 * @param mixed $object
	 */
	public function write($object);

	/**
	 * Writes an object to the response buffer, and adds a newline. What a newline
	 * is may vary depending on the implementation.
	 * @param unknown_type $object
	 */
	public function writeLine($object);

	/**
	 * Tells the environment to start buffering the output.
	 */
	public function startBuffer();

	/**
	 * Returns the rendered output.
	 */
	public function getOutput();

	/**
	 * Tells the environment to stop buffering, possibly sending the result as
	 * a response, saving to a file or something else depending on the implementation.
	 */
	public function stopBuffer();

	// ---- Request Info ---------------------------------------------------------------//

	/**
	 * Returns the url where the application is hosted, if available.
	 */
	public function getBaseURL();

	/**
	 * Returns the user agent's request method.
	 */
	public function getRequestMethod();

	/**
	 * Returns the path to the resource the user agent is requesting.
	 */
	public function getRequestString();

	/**
	 * Returns the address of the user-agent, if available.
	 */
	public function getRequestAddress();

	/**
	 * Returns the parameters obtained from the request url_stat
	 */
	 public function getRequestParameter($id);
}

?>
