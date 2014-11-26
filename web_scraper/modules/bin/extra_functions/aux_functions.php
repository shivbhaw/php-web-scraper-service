<?php	
	function unixToDateTime($unixTimestamp)
	{
		return date("Y-m-d H:m:s", $unixTimestamp);
	}
	
	function noErrors()
	{
		error_reporting(E_ERROR | E_PARSE);
	}

	function showErrors()
	{
		error_reporting(E_ALL);
	}
	
	if(!function_exists('Output'))
	{
		function Output($var)
		{
			?>
			<pre><?= var_dump($var) ?></pre>
			<?php
		}
	}
	function createGUID(){
	    if (function_exists('com_create_guid')){
	        return com_create_guid();
	    }else{
	        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
	        $charid = strtoupper(md5(uniqid(rand(), true)));
	        $hyphen = chr(45);// "-"
	        $uuid = chr(123)// "{"
	            .substr($charid, 0, 8).$hyphen
	            .substr($charid, 8, 4).$hyphen
	            .substr($charid,12, 4).$hyphen
	            .substr($charid,16, 4).$hyphen
	            .substr($charid,20,12)
	            .chr(125);// "}"
	        return $uuid;
	    }
	}

	function arrayToString($array)
	{
		//Extracts strings from the array and puts them in one.
		$string = "";
		foreach($array as $row)
		{
			if(gettype($row) == "array")
			{
				$string .= arrayToString($row);
			}
			else
			{
				$string .= $row." ";
			}
		}
		return $string;
	}
	
	function removeLastChar($string, $num)
	{
		return substr($string, 0, strlen($string) - $num);
	}
		
	/*function array_get($needle, $haystack) {
		return (in_array($needle, array_keys($haystack)) ? $haystack[$needle] : NULL);
	}*/
	
	/*PROBABLY UNNEEDED
	function realtrim($string, $side = 0)
	{
		if($side != 1)
		{
			$var = str_split($string);
			//Output($var);
			for($i = count($var)-1; $i >= 0; $i--)
			{	
				if($var[$i] != "\n" && $var[$i] != " " && $var[$i] != "\r" && $var[$i] != "\t")
				{
					return substr($string, 0, $i+1);
				}
			}
		}
		if($side != 2)
		{
			$var = str_split($string);
			//Output($var);
			for($i = 0; $i < count($var)-1; $i++)
			{	
				if($var[$i] != "\n" && $var[$i] != " " && $var[$i] != "\r" && $var[$i] != "\t")
				{
					return substr($string, $i+1, count($string));
				}
			}
		}
	}*/
?>
