<?php
namespace miu\db;

/**
 *	Thrown when database connection fails, in place of a less-secure PDO exception.
 */
class QueryNotFoundException extends DatabaseException { }
?>