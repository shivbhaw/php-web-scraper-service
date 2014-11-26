<?php
	require_once '../../modules/bin/functions.php';
	if(file_exists('../../modules/dynamic/Data_scraper_addon.php'))
	{
		require_once '../../modules/dynamic/Data_scraper_addon.php';
	}
	else
	{
		echo 'Have you run the application (app.php) the first time?';
		exit;
	}
	//require_once '../../modules/app_config.php';

	/*if(!defined('metadata'))
	$metadata = array();*/

	/*global $metadata;
	Output(gettype($GLOBALS['metadata']) == 'array');
	if(!array_key_exists('metadata', $GLOBALS) || gettype($GLOBALS['metadata']) != 'array')
	$metadata = array();
	*/
	$url = null;
	$php_script = null;
	if(array_key_exists("scriptoutput_editor_url", $_POST))
		$url = $_POST["scriptoutput_editor_url"];
	if(array_key_exists("scriptoutput_editor_script", $_POST))
		$php_script = $_POST['scriptoutput_editor_script'];

	$urls = array();
	$urls_array = explode("<br>", $url);
	$valid_url = NULL;
	if($urls_array != NULL && count($urls_array) != 0)
	{
		foreach($urls_array as $_url)
		{
			if($_url != "")
			{
				array_push($urls, $_url);
			}
		}
	}
	$php_script = ltrim($php_script, "<?php");
	$php_script = ltrim($php_script, "\r\n");
	$php_script = rtrim($php_script, "?>");
	$php_script = rtrim($php_script, "\r\n");

	$vars = file_get_contents('../../modules/dynamic/Data_scraper_variables.php');
	$vars = ltrim($vars, "<?php");
	$vars = ltrim($vars, "\r\n");
	$vars = rtrim($vars, "?>");
	$vars = rtrim($vars, "\r\n");
	eval($vars);
	eval($php_script);
	preg_match_all("/(([\$][a-zA-Z0-9_\-]*)[;])/i", $vars, $var_matches);
	$output_string = "";
	foreach($var_matches[2] as $var)
	{
		if($var != "\$metadata")
			$output_string .= "echo '$var = '; var_dump($var);echo '<br>';";
	}
	$output_string .= "echo 'Final Answer:<br>\$data_collection_scraper_app = '; Output(\$data_collection_scraper_app);echo '<br>';";
	echo '<h3>Variables Outputs:</h3>';
	eval($output_string)
?>
<html>
<header>
</header>
<body>
	<div hidden>
		<form action="script_editor_deployer.php" id="php_script_form" method="post">
			<input class="input_text" id="scriptoutput_script_textbox"  name="scriptoutput_editor_script" type="text" />
			<input class="input_text" id="scriptoutput_url_textbox" name="scriptoutput_editor_url" type="text" />
			<input id="scriptoutput_script_submit" type="submit">
		</form>
	</div>
</body>
</html>
