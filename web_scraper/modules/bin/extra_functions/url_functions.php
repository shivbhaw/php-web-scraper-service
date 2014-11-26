<?php
	function get_protocol($url)
	{
		$url_split = explode(":/", $url);
		return $url_split[0] . ":";
	}
	
	function split_url($url)
	{
		preg_match("/(http|https|ftp)\:\/\/([a-zA-Z\.]*)\/([a-zA-Z0-9\-\._\~\:\?\#\@\!\$\&\'\*\+\,\;\=\%]*)/", $url, $matches);
		return array($matches[1],$matches[2],$matches[3]);
	}
	
	function get_extension($file)
	{
		$file = trim($file);
		preg_match("/\.([a-zA-Z]*)$/", $file, $ext);
		if(count($ext) == 0)
		{
			return null;
		}
		else
		{
			return $ext[2];
		}
	}

	function remove_html_tags($string)
	{
		$clear = strip_tags($string);
		// Clean up things like &amp;
		$clear = html_entity_decode($clear);
		// Strip out any url-encoded stuff
		$clear = urldecode($clear);
		// Replace non-AlNum characters with space
		$clear = trim($clear);
		return $clear;
	}
	
	function get_file_size( $url ) {
		if(!is_valid_url($url))
			return 0;
		$data = get_headers($url, true);
		if (isset($data['Content-Length']))
			return (int) $data['Content-Length'];
		else
			return 0;
	}
	
	function getHeader( $url ) {
		return get_headers($url, true);
	}
	
	function is_valid_url($url)
	{
		//error_reporting(E_ERROR | E_PARSE);
		//file_get_contents($url);
		//error_reporting(E_ALL);
		noErrors();
		$url_headers = get_headers($url, true);
		if($url_headers == false)
		{
			return false;
		}
		showErrors();
		if(isset($url_headers)){
			if(preg_match("/404|400/", json_encode($url_headers)))
				return false;
		}
		else {return false;}
		return true;
	}
	
	function is_valid_script($code) {
		$code = escapeshellarg('<?php ' . $code . ' ?>');
		
		$lint = `echo $code | php -l`; // command-line PHP
		// maybe there are other messages for good code?
		if(preg_match('/No syntax errors detected in -/', $lint))
			return true;
		else
		{
			echo $lint;
			return false;
		}
	}	
	
	function get_query($url, $startingRegex)
	{
		$url = preg_replace("/[?&]page=[0-9]*/","",  $url);
		preg_match("/".$startingRegex."([".AllRegex()."]*)/", $url, $matches);
		if(count($matches) != 0)
			return $matches[1];
		else
			return null;
	}

?>