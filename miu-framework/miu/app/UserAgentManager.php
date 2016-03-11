<?php
namespace miu\app;

class UserAgentManager extends \miu\type\Singleton
{
	public function getUserAgent()
	{
		#Detect user agent.
		$ua = $_SERVER['HTTP_USER_AGENT'];
		$detector = new \Mobile_Detect();
		
		//IE 6, then phone and then tablet. Otherwise default (desktop).
		if(preg_match('/\bmsie 6/i', $ua) && !preg_match('/\bopera/i', $ua))
		{
			return 'ie6';
		}
		else if($detector->isMobile())
		{
			return 'phone';
		}
		else if($detector->isTablet())
		{
			return 'tablet';
		}
		else
		{
			return 'default';
		}
	}
}
?>