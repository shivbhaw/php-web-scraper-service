<?php

function run()
{
	$debug = '';
	//$debug = 'delete';//only
	$debug = 'insert';//only
	$urls_array = array(
		array(
			"datascraper_url_id"=>"1",
			"name"=>"Scraper Script 1",
			"url"=>"http:\/\/data.eindhoven.nl\/browse",
			"scraper_script_id"=>"fb10c4aa",
			"schedule_time"=>"d",
			"remove_all"=>"0",
			"mink_required"=>"0"
		)
	);

	$scripts_array = array(
		array("scraper_script_id"=>"-1",
		"name"=>"Initialization",
		"description"=>"Runs On App Initialization, mainly used for functions required and maybe initializing default vars",
		"script"=>"function body_excerpt(\$string){\r\nglobal \$body_data_pre_salt;\r\n\$string = explode(\$body_data_pre_salt, \$string)[0];\r\nreturn substr(\$string, 0, 200);\r\n}\r\n\r\nfunction slug(\$string)\r\n{\r\nreturn str_replace(' ', '_', strtolower(\$string));\r\n}\r\n\r\nfunction guid()\r\n{\r\nreturn uniqid();\r\n}"
	),
	array(
		"scraper_script_id"=>"-2",
		"name"=>"Init",
		"description"=>"",
		"script"=>""
	),
	array(
		"scraper_script_id"=>"fb10c4aa",
		"name"=>"Test Script",
		"script"=>"\$dataset_date = \"0\";\r\n\$dataset_content = \"This is test content\";\r\n\$dataset_title = \"This is the test dataset title\";\r\n\$dataset_size = \"1\";\r\n\$dataset_record_size = \"1\";\r\n\$dataset_protocol = \"1\";\r\n\$dataset_organization_name = \"1\";\r\n\$dataset_organization_website = \"1\";".
		"\r\n\$dataset_organization_description = \"1\";\r\n\$dataset_organization_contact_person = \"1\";\r\n\$dataset_organization_email = \"1\";\r\n\$dataset_organization_phonenumber = \"1\";\r\n\$dataset_update_frequency = \"1\";\r\n\$dataset_extra_information = \"1\";\r\n\$dataset_type = \"1\";\r\n\$dataset_demo_url = \"1\";\r\n\$dataset_screenshot = array(1);\r\n\$dataset_url = array(1);\r\n\$dataset_tags = array(1);\r\n\$dataset_categories = array(1);\r\n\$dataset_url_id = 1;\r\ncreate_data();"
	)
);

execute($urls_array, $scripts_array);

//DEBUG OPTIONS
global $output_db; $output_db = false;
//$output_db = false;


//TEST AREA
echo 'Test Data ------------------------------------------------------------------------<br>';
echo 'Test Data ------------------------------------------------------------------------<br>';
echo 'Test Data ------------------------------------------------------------------------<br>';
echo 'Test Data ------------------------------------------------------------------------<br>';

if($debug != 'delete')
{
	Output(Database::Select('wp_2_posts', '*'));
	Output(Database::Select('wp_2_dataset', '*'));
	Output(Database::Select('wp_2_dataset_url', '*'));
	Output(Database::Select('wp_2_dataset_screenshot', '*'));
	Output(Database::Select('wp_2_terms', '*'));
	Output(Database::Select('wp_2_term_taxonomy', '*'));
	Output(Database::Select('wp_2_term_relationships', '*'));
}

if($debug != 'insert')
{
	Database::Delete('wp_2_dataset');
	Database::Delete('wp_2_dataset_url');
	Database::Delete('wp_2_dataset_screenshot');
	Database::Delete('wp_2_posts');
	Database::Delete('wp_2_terms');
	Database::Delete('wp_2_term_relationships');
	Database::Delete('wp_2_term_taxonomy');
}

}
require_once('app.php');

?>
