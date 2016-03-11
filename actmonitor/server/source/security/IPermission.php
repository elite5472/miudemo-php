<?php
namespace security;

/**
 * @author Guillermo Borges
 */
interface IPermission
{
	public function getId();
	public function getRefid();
	public function getName();
	public function getDescription();
}
?>