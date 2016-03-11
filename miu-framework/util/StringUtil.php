<?php
class StringUtil
{
	public static function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	public static function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}

		$start  = $length * -1; //negative
		return (substr($haystack, $start) === $needle);
	}

	public static function printBoolean($boolean)
	{
		if($boolean)
		{
			return 'true';
		}
		else
		{
			return 'false';
		}
	}

	public static function quoteHtml($text)
	{
		$text = htmlspecialchars($text);
		$returntext = '';       // modified string to return back to caller
		$sections   = array();  // array of text sections returned by preg_split()
		$pattern1   = '%        # match: <tag attrib="xyz">contents</tag>
		^                       # tag must start on the beginning of a line
		(                       # capture whole thing in group 1
		  <                     # opening tag starts with left angle bracket
		  (\w++)                # capture tag name into group 2
		  [^>]*+                # allow any attributes in opening tag
		  >                     # opening tag ends with right angle bracket
		  .*?                   # lazily grab everything up to closing tag
		  </\2>                 # closing tag for one we just opened
		)                       # end capture group 1
		$                       # tag must end on the end of a line
		%smx';                  // s-dot matches newline, m-multiline, x-free-spacing

		$pattern2   = '%        # match: \n--untagged paragraph--\n
		(?:                     # non-capture group for first alternation. Match either...
		  \s*\n\s*+             # a newline and all surrounding whitespace (and discard)
		|                       # or...
		  ^                     # the beginning of the string
		)                       # end of first alternation group
		(.+?)                   # capture all text between newlines (or string ends)
		(?:\s+$)?               # clear out any whitespace at end of string
		(?=                     # end of paragraph is position followed by either...
		  \s*\n\s*              # a newline with optional surrounding whitespace
		|                       # or...
		  $                     # the end of the string
		)                       # end of second alternation group
		%x';                    // x-free-spacing

		// first split text into tagged portions and untagged portions
		// Note that the array returned by preg_split with PREG_SPLIT_DELIM_CAPTURE flag will get one
		// extra member for each set of capturing parentheses. In this case, we have two sets; 1 - to
		// capture the whole HTML tagged section, and 2 - to capture the tag name (which is needed to
		// match the closing tag).
		$sections = preg_split($pattern1, $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );

		// now put it back together proccessing only the untagged sections
		for ($i = 0; $i < count($sections); $i++) {
			if (preg_match($pattern1, $sections[$i]))
			{ // this is a tagged paragraph, don't modify it, just add it (and increment array ptr)
				$returntext .= "\n" . $sections[$i] . "\n";
				$i++; // need to skip over the extra array element for capture group 2
			} else
			{ // this is an untagged section. Add paragraph tags around bare paragraphs
				$returntext .= preg_replace($pattern2, "\n<p>$1</p>\n", $sections[$i]);
			}
		}
		$returntext = preg_replace('/^\s+/', '', $returntext); // clean leading whitespace
		$returntext = preg_replace('/\s+$/', '', $returntext); // clean trailing whitespace
		return $returntext;
	}

	public static function validateEmail($email, $skipDNS = false)
	{
	   $isValid = true;
	   $atIndex = strrpos($email, "@");
	   if (is_bool($atIndex) && !$atIndex)
	   {
			  $isValid = false;
	   }
	   else
	   {
			  $domain = substr($email, $atIndex+1);
			  $local = substr($email, 0, $atIndex);
			  $localLen = strlen($local);
			  $domainLen = strlen($domain);
			  if ($localLen < 1 || $localLen > 64)
			  {
					 // local part length exceeded
					 $isValid = false;
			  }
			  else if ($domainLen < 1 || $domainLen > 255)
			  {
					 // domain part length exceeded
					 $isValid = false;
			  }
			  else if ($local[0] == '.' || $local[$localLen-1] == '.')
			  {
					 // local part starts or ends with '.'
					 $isValid = false;
			  }
			  else if (preg_match('/\\.\\./', $local))
			  {
					 // local part has two consecutive dots
					 $isValid = false;
			  }
			  else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
			  {
					 // character not valid in domain part
					 $isValid = false;
			  }
			  else if (preg_match('/\\.\\./', $domain))
			  {
					 // domain part has two consecutive dots
					 $isValid = false;
			  }
			  else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)))
			  {
					 // character not valid in local part unless
					 // local part is quoted
					 if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local)))
					 {
							$isValid = false;
					 }
			  }

			  if(!$skipDNS)
			  {
					  if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
					  {
							 // domain not found in DNS
							 $isValid = false;
					  }
			  }
	   }
	   return $isValid;
	}
}
?>
