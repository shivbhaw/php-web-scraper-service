<?php
	require_once("extra_functions/array_functions.php");
	require_once("extra_functions/aux_functions.php");
	require_once("extra_functions/regex_helper.php");
	require_once("extra_functions/url_functions.php");
	require_once("mink.php");

	function get_html($url)
	{
		global $ignore_list;
		foreach($ignore_list as $ignore_url)
		{
			if($ignore_url == $url)
			{
				return;
			}
		}
		return file_get_contents($ignore_list);
	}
?>
