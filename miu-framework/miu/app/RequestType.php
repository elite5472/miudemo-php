<?php
namespace miu\app;
class RequestType
{
	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	const DELETE = 'DELETE';
	const ANY = 'ANY';
	
	public static function asArray()
	{
		return array('GET', 'POST', 'PUT', 'DELETE', 'ANY');
	}
}
?>