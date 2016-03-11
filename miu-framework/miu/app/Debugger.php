<?php
namespace miu\app;
class Debugger extends \miu\type\Singleton
{
	
	private $debug;
	
	public function useAssertions()
	{
		// Active assert and make it quiet
		assert_options(ASSERT_ACTIVE, 1);
		assert_options(ASSERT_WARNING, 0);
		assert_options(ASSERT_QUIET_EVAL, 1);
		
		// Set up the callback
		assert_options(ASSERT_CALLBACK, function($file, $line, $code)
		{
			throw new MiuException("Assertion Failed: <br>\nFile '$file' <br/>\nLine '$line' <br/>\nCode '$code' <br/>\n");
		});
	}
	
	public function setErrorHandlers($debug)
	{
		$this->debug = $debug;
		$error_handler = function($exception) use ($debug)
		{
			// these are our templates
		    $traceline = "#%s %s(%s): %s(%s)";
		    $msg = "<h1>PHP Fatal error:</h1>  Uncaught exception '%s' with message '%s' <br>\nIn <b>%s</b> on line <b>%s</b> <br>\n<h2>Stack trace: </h2><ul>\n<li>%s</li>\n</ul>\n";
		
		    $result = array();
		    if(!$debug)
		    {
			    // alter your trace as you please, here
			    $trace = $exception->getTrace();
			    foreach ($trace as $key => $stackPoint) {
			        // I'm converting arguments to their type
			        // (prevents passwords from ever getting logged as anything other than 'string')
			        $trace[$key]['args'] = array_map('gettype', $trace[$key]['args']);
			    }
			
			    // build your tracelines
			    foreach ($trace as $key => $stackPoint) 
			    {
			    	if(!isset($stackPoint['file'])) $stackPoint['file'] = 'unknown';
			    	if(!isset($stackPoint['line'])) $stackPoint['line'] = 'unknown';
			    	if(!isset($stackPoint['description'])) $stackPoint['description'] = 'unknown';
			        $result[] = sprintf(
			            $traceline,
			            $key,
			            $stackPoint['file'],
			            $stackPoint['line'],
			            $stackPoint['function'],
			            implode(', ', $stackPoint['args'])
			        );
			    }
			    // trace always ends with {main}
			    $result[] = '#' . ++$key . ' {main}';
			    
			    // write tracelines into main template
			    $msg = sprintf(
			    		$msg,
			    		get_class($exception),
			    		$exception->getMessage(),
			    		$exception->getFile(),
			    		$exception->getLine(),
			    		implode("</li>\n<li>", $result)
			    );
		    }
		    else
		    {
		    	$msg = sprintf(
		    		$msg,
		    		get_class($exception),
		    		$exception->getMessage(),
		    		$exception->getFile(),
		    		$exception->getLine(),
		    		str_replace("\n", " </li>\n<li>", $exception->__toString())	
		    	);
		    }
		
		    // log or echo as you please
		    error_log($msg);
		    
		    if($debug)
		    	echo $msg;
		};
		
		set_exception_handler($error_handler);
	}
	
	public function handleControllerException($e)
	{
		if($this->debug)
		{
			throw $e;
		}
		else
		{
			return 500;
		}
	}
}

?>