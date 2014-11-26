<?php
	define("FILE_PATH", "");

	require_once FILE_PATH."app_config/db_config.php";

	$firebug_debugging = false;
	if($firebug_debugging)
	{
		require 'lib/FirePHPCore/fb.php';
		function Output($var)
		{
			fb($var);
		}
	}

	require_once FILE_PATH."modules/app_builder.php";
	require_once FILE_PATH."modules/Database.php";
	require_once FILE_PATH."modules/bin/functions.php";


	if(!function_exists('run'))
	{
		function run()
		{
			execute();
		}
	}

	function execute($urls_array = null, $scripts_array = null){
		//return;
		if($urls_array == null || $scripts_array == null)
		{
			if($urls_array == null)
			{
				global $urls_array;
				$urls_array = json_decode(file_get_contents(FILE_PATH.'app_config/urls.json'));
			}
			if($scripts_array == null)
			{
				global $scripts_array;
				$scripts_array = json_decode(file_get_contents(FILE_PATH.'app_config/scripts.json'));
			}
		}
		if(!isset($argv))
		{
			$argv = "unset";
		}
		if(!(count($argv) == 1 || $argv == "unset"))
		{
			$urls_array = array_filter($urls_array, function($url){
				if($url['schedule_time'] == $argv[1])
					return true;
			});
		}

		$scripts = array();
		global $init_script;

		foreach($scripts_array as $script_key => $row)
		{
			if(gettype($script_key) != 'string')
			if($row['scraper_script_id'] == '-1')
			{
				eval($row['script']);
			}
			if($row['scraper_script_id'] == '-2')
			{
				eval($init_script = $row['script']);
			}
			$scripts[$row["scraper_script_id"]] = $row["script"];
		}

		eval(file_get_contents(FILE_PATH."modules/dynamic/Data_scraper_variables.php"));
		global $delete_true_scraper_app;
		global $url;

		foreach($urls_array as $url_value)
		{
			$delete_true_scraper_app = $url_value['remove_all' ] == 1;
			$url = $url_value['url'];
			eval($scripts[$url_value['scraper_script_id']]);
		}

		db_data_insertion();
		delete_data();
	}
	run();


?>
