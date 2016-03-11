<?php

try
{
	\db::setDatabase('sysdata', 'MYSQL', new PDO('mysql:host=localhost;dbname=dev_sysdata', 'dev_php', 'password'));
}
catch(PDOException $e)
{
	throw new \miu\db\DatabaseException('Invalid database connection string.');
}

?>