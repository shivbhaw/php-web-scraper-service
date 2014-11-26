<?php
require_once("../modules/Database.php");


if(count($_GET) != 0 || count($_POST) != 0)
{
	$data_retrieved = null;
	if(count($_GET) != 0)
	{
		$data_retrieved = $_GET;
	}
	elseif(count($_POST) != 0)
	{
		$data_retrieved = $_POST;
	}
	if($data_retrieved != null)
	{
		if($data_retrieved['type'] == "scripts")
		{
			if(file_put_contents('../app_config/scripts.json', $data_retrieved['values']))
				echo 'success';
			else
				echo 'failure';
		}
		elseif($data_retrieved['type'] == "urls")
		{
			if(file_put_contents('../app_config/urls.json', $data_retrieved['values']))
				echo 'success';
			else
				echo 'failure';
		}
	}
	else
	{
		echo 'failure';
	}

}
