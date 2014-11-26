<?php

	require_once('app_config/app_config.php');
	require_once('bin/functions.php');
	require_once('modules/Database.php');
	if(!defined("FILE_PATH"))
	{
		define('FILE_PATH', "../");
	}
	/**********************************************
	/**********************************************
	/*********                         ************
	/*********  DO NOT EDIT FROM HERE  ************
	/*********                         ************
	/**********************************************

	//http://php.net/manual/en/function.error-log.php ERROR_LOG with message level

	//USE http://phpbeautifier.com/ TO BEAUTIFY THE OUTPUT FOR DEBUG


	/**********************************************/
	//If no changes to the config, load the functions, else build the functions from scratch and then instiate the functions.
	$reboot_app = false;


	//varify no changes to ignore files
	$ignore_files = scandir("app_config/ignore");
	foreach($ignore_files as $ignore_file)
	{
		if($ignore_file != "." && $ignore_file != ".." && $ignore_file != '.DS_Store')
		{
			if(file_get_contents(FILE_PATH."modules/dynamic/ignore/".$ignore_file.'.txt') != file_get_contents('app_config/'.$ignore_file))
			{
				$reboot_app = true;
				file_put_contents(FILE_PATH."modules/dynamic/ignore/".$ignore_file.'.txt', file_get_contents('app_config/'.$ignore_file));
			}
		}
	}
	if(file_get_contents(FILE_PATH."modules/app_builder.php") == file_get_contents(FILE_PATH."modules/dynamic/app_builder.txt"))
	 	$reboot_app = true;
	if(file_get_contents(FILE_PATH."modules/dynamic/layout_check.txt") == file_get_contents('app_config/app_config.php'))
		$reboot_app = true;


	if(!$reboot_app)
	{
		echo "from file<br>";
		require_once('dynamic/Data_scraper_addon.php');
	}
	else
	{
		file_put_contents(FILE_PATH."modules/dynamic/app_builder.txt", file_get_contents(FILE_PATH."modules/app_builder.php"));
		file_put_contents(FILE_PATH."modules/dynamic/scripts.txt", file_get_contents(FILE_PATH."app_config/scripts.json"));
		file_put_contents(FILE_PATH."modules/dynamic/urls.txt", file_get_contents(FILE_PATH."app_config/urls.json"));

		class DataScraperBuilder
		{
			//variables needed to build the functions
			private $db_code = '';
			private $create_function = "";
			private $varify_function = "";
			private $delete_function = "";
			private $variable_code = "";
			private $pre_code = "";
			private $ignore_code = "";
			private $unique_check = "";
			//validation of no doubles
			private $variable_check = array();
			private $error_message = '';


			private $editor_variables = array();



			//CONSTRUCTOR (also constructs the entire code)
			public function __construct($layout, $varify, $ignore)
			{
				global $body_var_check;
				$body_var_check = false;

				global $body_data_pre_salt;
				$body_data_pre_salt = '<!-- BODY_REQUIREMENTS_FOR_FUTURE_UPDATES!129540SALT';

				global $first_output_var;
				$first_output_var = null;
				//runs though each table creating a inset and update array for each
				//also builds the variable list, the create_data method, db_insert method, delete method, varify method etc
				foreach($layout as $table_name => $table_contents)
				{
					$ignore_field = null;
					foreach($ignore as $ignore_row)
					{
						if($ignore_row['table'] == $table_name)
						{
							$ignore_field = $ignore_row;
						}
					}
					$this->db_code .= $this->decode_table_contents($table_name, $table_contents, $ignore_field);
				}
				//$VARIFY GOES HERE

				//echo "\n\nFinalCode";
				$output_code = '';
				//init

				$output_code .= 'global $body_data_pre_salt; $body_data_pre_salt = "'.$body_data_pre_salt.'";';
				//sets the global variables required for app to run
				$output_code .= "\$GLOBALS['BODY_VARIABLE_FOR_NEW_DATA'] = array('table' => '".$GLOBALS['BODY_VARIABLE_FOR_NEW_DATA']['table']."', 'row'=> '".$GLOBALS['BODY_VARIABLE_FOR_NEW_DATA']['row']."', 'where'=> '".$GLOBALS['BODY_VARIABLE_FOR_NEW_DATA']['where']."');\n";
				$output_code .= "global \$admin_debug_app_data;\$admin_debug_app_data = array();global \$new_ingore_links; \$new_ignore_links = array();\nglobal \$data_collection_scraper_app;\$data_collection_scraper_app = array();\nglobal \$delete_data_collection_scraper_app;\$delete_data_collection_scraper_app = array();\nglobal \$admin_debug_data;\$admin_debug_app_data = array();\n";

				//varify_for_ignore function from 3 lines above
				$varify_code = '';

				//used not only for varify but also ignore
				$varify_pre = "\$varify_fail_check = false;\n";

				foreach($varify as $varify_row)
				{
						$multi = false;
						$sql = $varify_row['sql'];
						preg_match_all('/(and)?\s*([a-z0-9_-]*)\s*=\s*(\'|")(var|multi)@([a-z0-9_\-]*)(\'|")\s*(and)?/i', $varify_row['sql'], $varify_row_matches);
						$varify_where = $this->decode_where_to_sql($varify_row['check'], true);
						for($i = 0; $i < count($varify_row_matches[0]); $i++)
						{
							if($varify_row_matches[4][$i] = 'multi')
							{
								$sql = str_replace($varify_row_matches[4][$i]."@".$varify_row_matches[5][$i],'".$multi_varify_var."',$sql);
								$multi = true;
							}
							else
							{
								$sql = str_replace($varify_row_matches[4][$i]."@".$varify_row_matches[5][$i],'".$data_scraper_app["'.$varify_row_matches[5][$i].'"]."',$sql);
							}
							if($multi)
							{
								$varify_code .= "foreach(\$data_scraper_app['".$varify_row_matches[5][$i]."'] as \$multi_varify_var){\n";
								$varify_code .= "\$database_varify_return = Database::Query(\"".str_replace($varify_row_matches[4][$i]."@".$varify_row_matches[5][$i],'".$multi_varify_var."',$varify_row['sql'])."\");\n";
								$varify_code .= 'if($database_varify_return == false)$database_varify_return = array();';
								$varify_code .= "foreach(\$database_varify_return as \$database_varify_return_row){\n";
								$varify_where = $this->decode_where_to_sql($varify_row['check'], true);

								$varify_code .= "if(\$database_varify_return_row['".$varify_where[0]."'] != \$data_scraper_app['".$varify_where[2]."']){\n";
								$varify_code .= '$varify_fail_check = true;';////
								$varify_code .= "}\n}\n}\n";
							}
							else
							{

								$varify_code .= "\$database_varify_return  = Database::Query(\"$sql\");\n";
								$varify_code .= 'if($database_varify_return == false)$database_varify_return = array();';
								$varify_code .= "foreach(\$database_varify_return as \$database_varify_return_row){\n";
								$varify_code .= "if(\$database_varify_return_row['".$varify_where[0]."'] != \$data_scraper_app[\"".$varify_where[2]."\"]){\n";
								$varify_code .= '$varify_fail_check = true;';////
								$varify_code .= "}\n}\n";
							}
						}
				}
				$varify_code .= 'if($varify_fail_check == true){global $error_message; $error_message .= "this data was found to have a duplicate already available: ".json_encode($data_scraper_app)."\n";continue;}';






				//db_data_insertion
				$output_code .= "function db_data_insertion(){global \$body_data_pre_salt;\n\nglobal \$data_collection_scraper_app;\nforeach(\$data_collection_scraper_app as \$data_scraper_app){\n";
				$output_code .= $varify_pre;
				$output_code .= $this->pre_code;
				$output_code .= $this->ignore_code;
				$output_code .= "\$GLOBALS['BODY_VARIABLE_FOR_NEW_DATA']['body_data'] = array();";
				$output_code .= '$GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_data"] = Database::Query("select ".$GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["row"]." from ".$GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["table"]." where '.$this->decode_where_to_sql($GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["where"]).'");';

				$output_code .= 'if(count($GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_data"]) == 0){$GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_data"] = null;}else{'.
				'$GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_data"] = $GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_data"][0];'.
				'if(count($GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_data"]) != 0)  $GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_data"] = $GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_data"][0];'.
				"if(strpos(\$GLOBALS['BODY_VARIABLE_FOR_NEW_DATA']['body_data'],\$body_data_pre_salt) !== FALSE)".
				'$GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_data"] = explode("-->", explode($body_data_pre_salt, $GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_data"])[1])[0];'.
				"else \$GLOBALS['BODY_VARIABLE_FOR_NEW_DATA']['body_data'] = '';".
				'if($GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_data"] != "") $GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_data"] = json_decode($GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_data"], true);}'.
				'$GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_insert"] = array();';

				//varify code.
				$output_code .= $varify_code;

				$output_code .= $this->db_code;
				$output_code .= "\n";
				$output_code .= '$body_string = Database::Query("select ".$GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["row"]." from ".$GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["table"]." where '.$this->decode_where_to_sql($GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["where"]).'")[0][0];';
				$output_code .= '$body_string = explode($body_data_pre_salt, $body_string)[0];';
				$output_code .= "Database::Update('".$GLOBALS['BODY_VARIABLE_FOR_NEW_DATA']['table']."', array('".$GLOBALS['BODY_VARIABLE_FOR_NEW_DATA']['row']."'=> \$body_string . \$body_data_pre_salt.json_encode(\$GLOBALS['BODY_VARIABLE_FOR_NEW_DATA']['body_insert']).' -->'), ".$this->decode_table_where($GLOBALS['BODY_VARIABLE_FOR_NEW_DATA']['where']).");";
				$output_code .= "}}\n\n";

				//varify_data
				$output_code .= 'function varify_data($data_collection_scraper_app = null){'."\n";
				$output_code .= 'if($data_collection_scraper_app == null){global $data_collection_scraper_app;}'."\n";
				$output_code .= $this->varify_function;
				$output_code .= "return true;\n}\n\n";

				//empty_vars
				$output_code .= "function empty_vars(){\n";
				$output_code .= $this->variable_code;
				$output_code .= "}\n\n";

				//delete_data
				$output_code .= "function delete_data(){\n";
				$output_code .= 'global $delete_data_collection_scraper_app; foreach($delete_data_collection_scraper_app as $data_scraper_app){';
				$output_code .= $this->pre_code;
				$output_code .= $this->delete_function;
				$output_code .= "}\n}\n";

				//create data
				$output_code .= "function create_data(){\n";
				$output_code .= "\$data_scraper_app = array();\n";
				$output_code .= $this->create_function;
				$output_code .= "if(varify_data()){\n";
				$output_code .= "global \$delete_true_scraper_app;\n";
				$output_code .= "if(!\$delete_true_scraper_app){\n";
				$output_code .= "global \$data_collection_scraper_app; array_push(\$data_collection_scraper_app, \$data_scraper_app);\n";
				$output_code .= "} else {\n";
				$output_code .= "global \$delete_data_collection_scraper_app; array_push(\$delete_data_collection_scraper_app, \$data_scraper_app);\n";
				$output_code .= "}\n";
				$output_code .= "}else{\n";
				$output_code .= "global \$error_message; \$error_message .= 'not all required variables are filled in this dataset: '.json_encode(\$data_scraper_app).\"\\n\";\n";
				$output_code .= "}\n";
				$output_code .= "clear_vars();\n";
				$output_code .= "}\n\n";

				$output_code .= 'function body_old_data($update_value, $table_name, $table_where, $vars_required)'.
						'{'.
							'$body_old_data = "";'.
							'$vars_string = "";'.
							'foreach($vars_required as $var_name => $var_value){'.
								'$vars_string .= "$".$var_name ." = ".$var_value . ";";'.
							'}'.
							'eval($vars_string);'.

							'if($table_name == $GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["table"])'.
							'{'.
								'$body_old_data = Database::Query("select ".$GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["row"]." from ".$GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["table"]." where '.$this->decode_where_to_sql($GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["where"]).'")[0][0];'.
								'global $body_data_pre_salt;'.
								"if(strpos(\$body_old_data,\$body_data_pre_salt) !== FALSE)".
				'$body_old_data = explode($body_data_pre_salt, $body_old_data)[0];'.
				"else \$body_old_data = '';".

							'}'.


							'$db_data = Database::Query("select * from ".$table_name." where $table_where;");'.
							'if(count($db_data) != 0)'.
							'$db_data = $db_data[0];'.

							'$changed_data = array();'.
							'$ready_update_value = false;'.
							'$body_update_hidden_value = array();'.
							'if(array_key_exists($table_name,$GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_data"]))'.
							'$body_update_hidden_value = $GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_data"][$table_name];'.
							'foreach($update_value as $key => $value){'.
								'if($key == $GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["row"])'.
								'{'.
									'if($body_old_data != $value)'.
									'{'.
										'$changed_data[$key] = $value;'.
										'$ready_update_value = true;'.
									'} '.
								'}'.
								'elseif($db_data[$key] != $value)'.
								'{'.
									'$old_value = "";'.
									'if(array_key_exists($key, $body_update_hidden_value)){'.
										'if(explode("(old_value:", $body_update_hidden_value[$key])[0] != $value){'.
											'$old_value = "(old_value:".$body_update_hidden_value[$key].")";'.
											'$changed_data[$key] = $value.$old_value;'.
											'$ready_update_value = true;'.
										'}'.
										'else{'.
											'$changed_data[$key] = $body_update_hidden_value[$key];'.
											'$ready_update_value=true;'.
										'}'.
									'}'.
									'else{'.
										'$changed_data[$key] = $value;'.
										'$ready_update_value=true;'.
									'}'.
								'}'.
							'}'.
							'if($ready_update_value)'.
							'{'.
								'$GLOBALS["BODY_VARIABLE_FOR_NEW_DATA"]["body_insert"][$table_name] = $changed_data;'.
							'}'.
						'}';

				//Pre_Change Backup
				try
				{
					$date_file_name = 'backup/time='.date("H-i-s").'__date(d-m-y)='.date("d-m-y");
					mkdir($date_file_name,0777);
					foreach($layout as $table_name => $n_a)
					{
						if(array_key_exists('multi', $n_a))
						{
							if(gettype($n_a['multi']) == 'array')
							{
								foreach($n_a['multi'] as $sub_table_name => $sub_n_a)
								{
									file_put_contents($date_file_name."/".$sub_table_name.'-'.date("H-i-s").'.txt', json_encode(Database::Select($sub_table_name, "*")));
								}
							}
						}
						file_put_contents($date_file_name."/".$table_name.'-'.date("H-i-s").'.txt', json_encode(Database::Select($table_name, "*")));
					}
				}
				catch(Exception $e)
				{
					exit(1);
				}


				//function clear_vars
				$output_code .= 'function clear_vars(){'.$this->variable_code."}\n\n";
				file_put_contents(FILE_PATH."modules/dynamic/Data_scraper_addon.php", "<?php ".$output_code." ?>");
				file_put_contents(FILE_PATH."modules/dynamic/Data_scraper_variables.php", $this->variable_code);
				file_put_contents(FILE_PATH."modules/dynamic/layout_check.txt", file_get_contents('app_config/app_config.php'));

				//editor outputs
				file_put_contents(FILE_PATH."editor/script_editor_files/variables.json", json_encode($this->editor_variables));

				eval($output_code);
			}

			//extracts the metadata from each table
			private function decode_table_contents($table_name, $table_contents, $ignore = null)
			{
				$add_varifier_to_pre = true;
				if($ignore == 'sub_table_so_no_pre')
				{
					$add_varifier_to_pre = false;
					$ignore = null;
				}
				$db_code_text = '';
				//gets the table name
				$table_name = preg_replace('/{[0-9]*}$/','',$table_name);

				//gets the where segment of the sql
				$where_sql = null;
				$body_var_string = '';
				if(array_key_exists('where_sql', $table_contents))
				{
					$where_sql = $table_contents['where_sql'];
					preg_match_all('/(var)@([a-z0-9_\-]*)/i', $table_contents['where_sql'], $body_vars_required);
					foreach($body_vars_required[2] as $body_vars)
					{
						$body_var_string .= '"'.$body_vars.'" => '.'$'.$body_vars.' ,';
					}
				}

				//gets the checker for if the data is to be updated or inserted, also retrieves the id for the insertion
				$varify = null;
				if(array_key_exists('insert_output', $table_contents))
				{
					$varify = $table_contents['insert_output'];
				}

				//gets whether this table is to be deleted.
				$delete = false;
				$delete_where = null;
				if(array_key_exists('delete', $table_contents))
				{
					if(($table_contents['delete'] == true && $table_contents['delete'] != 'false') || $table_contents['delete'] == 'true')
					{
						$delete = true;
						$delete_where = false;
						if(array_key_exists('delete_where', $table_contents))
						{
							if($table_contents['delete_where'] != '' && (gettype($table_contents['delete_where']) != 'string'))
								$delete_where = $table_contents['delete_where'];
						}
					}

				}

				//checks if the data to be retrieved is a multi variable( meaning a variable with more than one value)
				$multi_check = false;
				if(array_key_exists('multi', $table_contents))
				{
					if(gettype($table_contents['multi']) == 'array')
					{
						if(count($table_contents['multi']) != 0)
						{
							$multi_check = true;
						}
					}
					elseif(gettype($table_contents['multi']) == 'string')
					{
						if($table_contents['multi'] == 'true')
						{
							$multi_check = true;
						}
					}
					elseif($table_contents['multi'] == true)
					{
						$multi_check = true;
					}
				}

				//Singular (not multi) data building
				if(!$multi_check)
				{

					//gathers the insert and update arrays, along with the id and the multi_variable if applicable
					$var_data = $this->decode_table_row($table_contents['table'], $table_name);


						if($ignore != null)
						{
							$this->ignore_code .= '$ignore_list  = json_decode(file_get_contents("'.$ignore['url'].'"));'.
								'foreach($ignore_list as $ignore_value){'.
									'if($ignore_value == $data_scraper_app["'.$ignore['var'].'"]){'.
										'$varify_fail_check = true; break;'.
									'}'.
								'}';
						}


					//get sql for check on update or insert and fix it for the code
					$sql = $table_contents['varify'];
					$sql = $this->update_id_sql($sql);

					//begin setting up the code.

					//figure out how to output the contents of the sql check and insert output, whether to use default (id) or selected var name
					//none isnt an option due to it being singular and having an id none the less.
					//only the first will be used for the name. if not unique, use output_var.
					$output_var = '';
					if(array_key_exists('output_var', $table_contents))
					{
						if($table_contents['output_var'] != '')
						{
							$output_var = '$' . $table_contents['output_var'];
						}
					}
					if($output_var == '')
					{
						$output_var = '$'.$var_data['id'];
					}
					if($GLOBALS['first_output_var'] == null)
					{

						$GLOBALS['first_output_var'] = $output_var;
					}
					if($output_var == '$')
					{
						$output_var = '$'.uniqid('random_id_');
					}


					preg_match('/(multi@)([a-z0-9-_]*)([\[\]0-9]*)/i', $sql, $sql_multi_matches);
					if(count($sql_multi_matches) != 0)
					{
						$sql = str_replace($sql_multi_matches[0], '$multi_matches_for_multi_array', $sql);
						if($add_varifier_to_pre)
						{
							$this->pre_code .= 'foreach($data_scraper_app["'.$sql_multi_matches[2].'"] as $multi_matches_for_multi_array){';
							//creates the pre code ( developed like this to ensure no duplicates or any other validity.

							$this->pre_code .= $output_var . ' = Database::Query("'.$sql.'");'."\n";

							$this->pre_code .= 'if(count('.$output_var . ')!= 0){'.$output_var.' = '.$output_var . '[0][0]; break;}'."\n";
							$this->pre_code .= '}if(count('.$output_var . ')== 0){'.$output_var . ' = null;}';

							//uses pre_code
							$db_code_text .= 'if(!'.$output_var."){\n";
						}
						else
						{
							$db_code_text .= 'if(!Database::Query("'.$sql.'")){';
						}
					}
					else
					{
						if($add_varifier_to_pre)
						{
							$this->pre_code .= $output_var . ' = Database::Query("'.$sql.'");'."\n";
							$this->pre_code .= 'if(count('.$output_var . ')== 0){'.$output_var . ' = null;}else{'.$output_var.' = '.$output_var . '[0][0];}'."\n";

							//uses pre_code
							$db_code_text .= 'if(!'.$output_var."){\n";
						}
						else
						{
							$db_code_text .= 'if(!Database::Query("'.$sql.'")){';
						}
					}
					//builds the insertion process. Uses the following steps
					//	--checks if the id exists
					//		--if it doesnt, it uses the insert function
					//		--if it does, it uses the update function by sending the data to the body_old_data function
					$db_code_text .= $var_data['insert'];
					$db_code_text .= $output_var . ' = Database::Insert("'.$table_name.'",$'.$table_name . '_insert_array);'."\n";
					if(!$add_varifier_to_pre)
					{
						$db_code_text .= '}';
					}
					else
					{
						$db_code_text .= "}else{\n";
						$db_code_text .= $var_data['update'];
						$update_vars = $this->decode_table_where($where_sql);
						preg_match_all('/\$([a-z0-9_-]*)/i', $update_vars, $update_vars_matches);
						$update_vars = 'array(';
						for($i = 0; $i < count($update_vars_matches[0]); $i++)
						{
							$update_vars .= '"'.$update_vars_matches[1][$i].'"=>'.$update_vars_matches[0][$i] . ', ';
						}
						$update_vars .= $body_var_string;
						$update_vars = rtrim($update_vars, ', ');
						$update_vars .= ')';
						$table_contents_where_sql = '"';
						if(array_key_exists('where_sql', $table_contents))
						{
							if($table_contents['where_sql'] != '')
							{
								$table_contents_where_sql .= $this->decode_where_to_sql($table_contents['where_sql']);
							}
						}
						if($table_contents_where_sql == '"')
						{
							$table_contents_where_sql = '';
						}
						else
						{
							$table_contents_where_sql .= '"';
						}
						$db_code_text .= "body_old_data($".$table_name . "_update_array,'".$table_name."', ".$table_contents_where_sql.', '.$update_vars.");";
						$db_code_text .= '}';
					}
					//Adds this table to the delete function.
					if($delete && array_key_exists("where_sql", $table_contents))
					{
						$this->delete_function .= 'if('.$output_var."){\n";
							$this->delete_function .= "Database::Delete('".$table_name."',".$this->decode_table_where($table_contents['where_sql']).");\n";
							$this->delete_function .= "}\n";
					}

				}

				//Multi Data Builder
				else
				{
					//extracts the data (in this funciton this is recursive)
					$var_data = $this->decode_table_row($table_contents['table'], $table_name);

					if($ignore != null)
					{
						$this->ignore_code .= '$ignore_list = json_decode(file_get_contents("'.$ignore['url'].'"));'.
						'foreach($ignore_list as $ignore_value){'.
								'foreach($data_scraper_app["'.$ignore['var'].'"] as $scraped_value_for_ignore){'.
									'if($ignore_value == $scraped_value_for_ignore){'.
											'$varify_fail_check = true;'.
											'break;'.
									'}'.
								'}'.
								'if($varify_fail_check == true)break;'.
							'}';
					}

					//figure out how to output the contents of the sql check and insert output, whether to use default (id) or selected var name or none.
					$output_var = '';
					$output_var_sql = '';
					if(array_key_exists('output_var', $table_contents))
					{
						if($table_contents['output_var'] != '')
						{
							$output_var = '$' . $table_contents['output_var'];
							$output_var_sql = '$' . $table_contents['output_var'].' = ';
						}
					}
					if($output_var == '')
					{
						if($var_data['id'] != '')
						{
							$output_var = '$'.$var_data['id'];
							$output_var_sql = '$'.$var_data['id'] . ' = ';
						}
					}
					if($output_var == '')
					{
						if($var_data['multi'] != '')
						{
							$output_var = '$'.$var_data['multi'];
							$output_var_sql = '$'.$var_data['multi'] . ' = ';
						}
					}
					if($output_var == '')
					{
						$output_var = '$'.uniqid('random_id_');
						$output_var_sql = $output_var . ' = ';
					}



					//extracts SQL statement for varifying if data has been used before.
					//changes any var into the default variable so as to use the for loop
					$sql = $table_contents['varify'];
					preg_match('/var@([a-z0-9_\-]*)/i', $sql, $sql_matches);
					if(count($sql_matches) != 0)
					{
						$sql = str_replace($sql_matches[0], '$multi_matches_for_multi_array', $sql);
						$multi_var = $sql_matches[0];
						$db_code_text .= 'foreach($data_scraper_app["'.$sql_matches[1]."\"] as \$multi_matches_for_multi_array){\n";

					}

					//uses the for loop to cycle through data
					//uses the cycled throug data to check if available, and if not inserts the data.
					$db_code_text .= $output_var_sql.'Database::Query("'.$sql."\");\n";
					$db_code_text .= 'if(count('.$output_var . ')== 0){'.$output_var . ' = null;}else{'.$output_var.' = '.$output_var . '[0][0];}'."\n";
					$db_code_text .= 'if(!'.$output_var."){\n";
					$db_code_text .= $var_data['insert'];
					$db_code_text .= $output_var . ' = Database::Insert("'.$table_name.'",$'.$table_name . '_insert_array);'."\n";
					$db_code_text .= "}\n";
					if(gettype($table_contents['multi']) == 'array'){
						foreach($table_contents['multi'] as $sub_table_name => $sub_table_contents)
							$db_code_text .= $this->decode_table_contents($sub_table_name, $sub_table_contents, 'sub_table_so_no_pre');
					}

					if(count($sql_matches) != 0)
					{
						$db_code_text .= "\n}\n";
					}
					//Adds this table to the delete function.
					$table_where_sql_exists = array_key_exists("where_sql", $table_contents);
					$table_delete_where_exists = array_key_exists("delete_where", $table_contents);
					if($delete && ($table_where_sql_exists || $table_delete_where_exists))
					{
						if($table_where_sql_exists  && !$table_delete_where_exists)
						{
							$this->delete_function .= 'foreach($data_scraper_app["'.$sql_matches[1]."\"] as \$multi_matches_for_multi_array){\n";
							$this->delete_function .= $output_var . ' = Database::Query("select '.$var_data['id'].' from '.$table_name . ' where '.$this->decode_where_to_sql($table_contents['where_sql'])."\")[0]) != 0){\n";
							$this->delete_function .= "Database::Delete('".$table_name."',".$this->decode_table_where($table_contents['where_sql']).");\n";
							$this->delete_function .= "}\n";
							$db_code_text .= "}\n";
						}
						elseif($table_delete_where_exists)
						{
							$this->delete_function .= $output_var . ' = Database::Select("'.$table_name.'", "*", '.$this->decode_table_where($table_contents['delete_where']).');'.
								'if(count('.$output_var.') != 0){'.
								'Database::Delete("'.$table_name.'", '.$this->decode_table_where($table_contents['delete_where']).');'.
								'}';
						}
						else
						{
							//TODO ERROR MESSAGE
						}
					}
				}
				global $body_var_check;
				if(array_key_exists('row', $GLOBALS['BODY_VARIABLE_FOR_NEW_DATA']) && $body_var_check == false)
				{
					$GLOBALS['BODY_VARIABLE_FOR_NEW_DATA']['table'] = $table_name;
					$GLOBALS['BODY_VARIABLE_FOR_NEW_DATA']['where'] = $where_sql;

				}
				if($body_var_check == false)
				{
					if(array_key_exists('row', $GLOBALS['BODY_VARIABLE_FOR_NEW_DATA']))
					{
						$body_var_check = true;
					}
				}
				return $db_code_text;
			}



			//Builds the functions using the layout of the table
			private function decode_table_row($table, $variable_prefix)
			{

				$id_var = '';
				$multi_var = '';

				//instiate the variable
				$insert_array = "\$".$variable_prefix."_insert_array = array(";
				$update_array = "\$".$variable_prefix."_update_array = array(";


				//looks through each of the columns of the table and decodes it.
				foreach($table as $column_key => $column)
				{
					//metadata of the column
					$required = false;
					$var_pre = '';
					$var_post = '';

					$type = '';
					$var_name = '';
					$var_type = 'string';
					$functions_has_variable = false;

					//decode extra functionality
					$contents = gettype($column) == 'array';

					//if the column has no extra metadata, it simply reeceives the data and deals with it further up
					//if not each of the metadata is read and decoded.
					if(!$contents)
					{
						$contents = $this->decode_var($column);
					}
					else
					{
						//gets the value of the column, even if it means it turns into a variable
						if(array_key_exists('value', $column))
						{
							$contents = $this->decode_var($column['value']);
						}

						//gets the type of the variable
						if(array_key_exists('type', $column))
						{
							$var_type = $column['type'];

						}

						//checks whether the variable is one that must be filled in.
						if(array_key_exists('required', $column))
						{
							if($column['required'] == true || $column['required'] == 'true')
							{
								$required = true;
							}
						}

						//checks whether a function must be called before insertion into the database (with .
						if(array_key_exists('functions', $column) || array_key_exists('function', $column))
						{
							$function_array = null;
							if(array_key_exists('function', $column))
							{
								$function_array = $column['function'];
							}
							else
							{
								$function_array = $column['functions'];
							}

							//if it is a singular function (string) turn it into an array with the string.
							if(gettype($function_array) == 'string')
							{
								$function_array = array($function_array);
							}

							//turn each function in the function_array into a function, where the next one encapsulates the previous.
							foreach($function_array as $_addon)
							{
								$addon = explode(',', str_replace(' ', '', $_addon));
								$var_pre .= $addon[0] . "(";
								for($i = 1; $i < count($addon); $i++)
								{
									preg_match('/^(var)@(.*)/i',$addon[$i], $addon_matches);
									if(count($addon_matches) != 0)
									{
										if($functions_has_variable == false)
										{
											$functions_has_variable = true;
										}
										$this->decode_column($addon_matches[2]);
										$var_post .= ',$data_scraper_app["'.$addon_matches[2].'"]';
									}
									else
									{
										$var_post .= ','.$addon[$i];
									}
								}
								$var_post .= ")";
							}
						}
					}

					//Finalize the metadata
					$type = $contents[1];
					if($contents[2] == '*')
					{
						$type = 'id';
					}
					$var_name = $contents[3];


					//decode the column by this apps $type
					//This means that the type is a regular string to just be inserted upon insert
					if($type == '')
					{
						if(gettype($var_name) == 'string')
						{
							$var_name = '"'.$var_name.'"';
						}
						$insert_array .= '"'.$column_key.'"=> ' . $var_pre . $var_name.$var_post.',';
						//$function_has_variable is instiated earlier in the decoding of the function
						//and means that the column is adaptable and should be updated too
						if($functions_has_variable)
						{
							$update_array .= '"'.$column_key.'"=> ' . $var_pre .$var_name.$var_post.',';
						}
					}
					//this instiates the the value is the id(or one of the id) of the table
					elseif($type == 'id')
					{
						$insert_array .= '"'.$column_key.'"=> ' . $var_pre . '$'.$var_name.$var_post.',';
						if($id_var == '')
							$id_var = $var_name;

					}
					//this means that the type is another ie:- (a variable, init[on insert only], multi[more than one value], or body[contains the body variable])
					else
					{
						//build column and insert values for the arrays for insert and update

						//All columns have an insert, as every one needs to be instiated, thus it is added to the insert array
						if($type != 'multi')
						{
							$insert_array .= '"'.$column_key.'"=> ' . $var_pre .'$data_scraper_app["' .$var_name.'"]'.$var_post.',';

							//if the value is not an ID or an insert only value, then add column to the update array
							if($type != 'init')
							{
								if($type != 'id')
								{

									$update_array .= '"'.$column_key.'"=> ' . $var_pre .'$data_scraper_app["'.$var_name.'"]'.$var_post.',';
								}
							}
						}
						else
						{
							$insert_array .= '"'.$column_key.'"=> ' . $var_pre .'$multi_matches_for_multi_array'.$var_post.',';

						}
						//if the variable hasnt been created (and instiated already, thus added to $this->variable_check) it will be created.
						if(!in_array($var_name, $this->variable_check, true))
						{
							array_push($this->variable_check, $var_name);
							//decode column takes the column value and decodes it, either turning it into the desired variable
							//it takes into consideration (as arguements):-
							//		the variable name
							//		what is the type of value created
							//		is the value required
							if($type == 'multi')
							{
								$this->decode_column($var_name, 'array', $required);
								$multi_var = $var_name;
							}
							else
							{
								if($type == 'body')
								{
									if($type == 'body')
									{
										$GLOBALS['BODY_VARIABLE_FOR_NEW_DATA'] = array('row'=> $column_key);
									}
								}
								$this->decode_column($var_name, $var_type, $required);
							}

						}
					}
				}
				return array('insert' => rtrim($insert_array, ',').");\n", 'update' => rtrim($update_array, ',').");\n", 'id' => $id_var, 'multi' => $multi_var);
			}


			//takes the column (with a prefix and post-fix) and uses it to decode how to set up the variable.
			private function decode_column($var_name, $var_type = 'string', $required = false)
			{
				//adds the row to the create function
				$this->create_function .= "global \$".$var_name."; \$data_scraper_app['".$var_name."'] = \$".$var_name. ";\n";

				//instiates the default value for the variable code
				$var_init = '';
				if($var_type == 'string')
				{
					$var_init = '""';
				}
				elseif($var_type == 'integer')
				{
					$var_init = '0';
				}
				elseif($var_type == 'array')
				{
					$var_init = 'array()';
				}
				else
				{
					return;
				}

				//when required, the proper required string is instiated
				$required_string = '';
				if($required)
				{
					if($var_type == 'string')
					{
						$required_string = ' && $'.$var_name." == ''";
					}
					elseif($var_type == 'integer')
					{
						$required_string = ' && $'.$var_name." == 0";
					}
					elseif($var_type == 'array')
					{
						$required_string = ' && count($'.$var_name.") == 0";
					}
				}

				//the column is added to the varify function
				$this->varify_function .= "global \$".$var_name.";if(gettype(\$".$var_name.") != '".$var_type."'".$required_string.")\n{return false;}\n\n";

				//the column is added to the variable code.
				$this->variable_code .= "global \$".$var_name.";"."\$".$var_name." = ".$var_init.";\n";

				//editor help
				$editor_value = '';
				if($required)
				{
					$editor_value = '<required>'.$var_name.'</required>';
				}
				else
				{
					$editor_value = $var_name;
				}
				array_push($this->editor_variables, array('name'=>$editor_value, 'type'=>$var_type));
			}


			private function update_id_sql($update_sql, $multi = null)
			{
				//THIS FUNCTION CREATES/FIXES THE UPDATE ID SQL
				//IT FIXES THE VARIFY CATEGORY TO MAKE SURE THAT IT DOESNT HAVE ANY INSTIATORS
				$pre_post = array();
				if(gettype($update_sql) != "string")
				{
					$varify = $update_sql['varify'];
					$update_sql_array = array();
					if(array_key_exists('value', $update_sql['pre']))
					{
						$update_sql_array = array($update_sql['pre']);
					}
					else
					{
						$update_sql_array = $update_sql['pre'];
					}
					foreach($update_sql_array as $update_sql)
					{
						$pp_array = array();
						foreach($update_sql as $functions_pre)
						{
							$function_pre = null;
							if(gettype($functions_pre) == 'string')
							{
								$function_pre = array($functions_pre);
							}
							else
							{
								$function_pre = $functions_pre;
							}
							$pp_array['pre'] = "";
							$pp_array['post'] = "";
							foreach($function_pre as $function_data)
							{
								$function_aux = explode(',',$function_data);
								$pp_array['pre'] = $function_aux[0]."(" . $pp_array['pre'];
								for($i = 1; $i < count($function_aux); $i++)
								{
									$pp_array['post'] .= ','.$function_aux[$i];
								}
								$pp_array['post'] .= ")";
							}
						}
						$pre_post[$update_sql['value']] = $pp_array;
					}
					$update_sql = $varify;
				}

				preg_match_all("/var@([a-zA-Z0-9_\'\"]*)(\[[a-zA-Z0-9\-\_]*\])?/i", $update_sql, $variable);
				if(count($variable[0]) != 0)
				{
					$pre = "";
					$post = "";
					if(array_key_exists($variable[0][0], $pre_post))
					{
						$pre = $pre_post[$variable[0][0]]['pre'];
						$post = $pre_post[$variable[0][0]]['post'];
					}

					for($i = 0; $i < count($variable[0]); $i++)
					{
						if($multi == null)
						{
							$update_string = "\".".$pre."\$data_scraper_app['".$variable[1][$i]."']".$post;
							if($variable[2][$i]!= "")
							{
								$update_string .= $variable[2][$i];
							}
							$update_string .= ".\"";
							$update_sql = str_replace($variable[0][$i], $update_string , $update_sql);
						}
						else
						{
							$update_sql = str_replace($variable[0][$i], "\$multi_row", $update_sql);
						}
					}
				}
				return $update_sql;
			}

			private function decode_table_where($string){
				//FIXES THE WHERE SQL IN THE TABLE METADATA.
				//TURNS IT INTO THE VALUE TO BE USED IN THE DATABASE CLASS
				preg_match_all('/(\&\&)?\s*([a-zA-Z0-9_\/-\@\$\=\>\'\"\s]*)\s*/i', $string, $where_matches);
				$return_string = "array(";
				foreach($where_matches[2] as $match)
				{
					if(strpos($match, '=') !== FALSE)
					{
						$return_string .= preg_replace(["/var@/", "/^/", "/\=/"], ["\$","'", "'=>"], str_replace(' ', '', $match)) . ",";
					}
				}
				return rtrim($return_string, ",").')';
			}

			private function decode_where_to_sql($string, $return_variables = false)
			{
				$return_string = '';
				$return_array = array();
				//DECODES THE WHERE FROM THE VARIFY SQL CODE
				foreach(explode(' and ', $string) as $str)
				{
					preg_match_all('/([a-z0-9_\-\$]*)=([a-z0-9_\-\$\@\']*)/i', str_replace(' ', '', $str), $where_matches);

					if($return_variables)
					{
						$return_array[3] = $where_matches[2][0];
					}

					$var_matches = $this->decode_var($where_matches[2][0]);
					if($var_matches[2] == '@')
					{
						if($var_matches[1] == 'multi')
						{
							if($return_variables)
							{
								$return_array[1] = '$multi_matches_for_multi_array';
							}
							else
							{
								$variable_string = '$multi_matches_for_multi_array';
							}
						}
						else
						{
							if($return_variables)
							{
								$return_array[1] = '".$data_scraper_app["'.$var_matches[3].'"]."';
								$return_array[2] = $var_matches[3];
							}
							else
							{
								$variable_string = '".$data_scraper_app["'.$var_matches[3].'"]."';
							}
						}
					}
					else
					{
						if($return_variables)
						{
							$return_array[1] = trim(trim($where_matches[2][0], '\''), '\"');
						}
						else
						{
							$variable_string = trim(trim($where_matches[2][0], '\''), '\"');
						}
					}
					if($return_variables)
					{
						$return_array[0] = $where_matches[1][0];
					}
					else
					{
						$return_string .= $where_matches[1][0]."='".$variable_string."' AND ";
					}
				}

				if($return_variables)
				{
					return $return_array;
				}
				else
				{
					return rtrim($return_string, " AND ");
				}
			}


			private function decode_var($var)
			{
				//USED IN THE DECODE_COLUMN (and others) TO SEPERATE THE PREFIX FROM THE SUFFIX
				preg_match('/(var|body|init|multi|func)?(\*|@)?([a-z0-9_\-\$]*)/i',$var, $contents);
				return $contents;
			}
		}

		//execute
		new DataScraperBuilder($database_layout, $varification_data, $ignore_fields);
	}

?>
