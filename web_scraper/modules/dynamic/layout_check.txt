<?php

	//In this you will find the layout and structure and the way it should be built.



	//         READ BEFORE !!!

	//	if you intent to use this application, please backup your database.
	//this application does this incase of any errors during development and testing

	//but for YOUR ease, back it up in SQL yourself.






	//one variable unimplemented is $error_message
	//at the end it will contain all the errors found on this application, even with database insertion.



	require_once 'db_config.php';
	if(!defined('FILE_PATH'))
		define('FILE_PATH', "../");
	//this will be run for EVERY script run, and for each script output is stored individually
	//If the data is found to be not real, the data is voided and send to the variable error_message.
	$varification_data = array(
		array(
			'sql' => "select id from table_name where data = data",
			'check' => 'id=var@data_id'
		)
	);

	//if the field is found to have a value mentioned, then it will ignore that perticular insertion, but everything else will be inserted, even other ones of the same field in the case of multi/array
	//data presented in a simple array.
	$ignore_fields = array(
		array('table' => 'table_multiple_urls', 'row' => 'url', 'var'=>'data_url', 'url'=>'app_config/ignore/urls.json')
	);


	//THE ONLY MUST HAVE IS THE BODY VARIABLE
	//BECAUSE EXTRA DATA FROM THE NEXT
	//UPDATE
	$database_layout = array(
		///EDIT FROM HERE

		//TABLE ID BECOMES THE VARIABLE THAT HOLDS THE INSERTION AND UPDATE OUTPUT
		//THUS *id becomes $id in the app, can be used to put one value from one to the next

		"table_one" => array(
			"where_sql" => 'ID=$db_id',//the where clause for the update. used in various other methods as well
			"delete" => 'true',//allows for deletion when "remove all" is chosen in the web-editor for urls
			"varify" => "sql",//sql to get the unique identifier for application purpose to see if to update or insert the data
			"output_var"=> 'var_to_output_into',//not required as if this isnt available the ID is used. though does help with multiple table with the same ID. if no id and no output_var, a random_id will be used.
			"table" => array(
				"ID" => "*db_id",//* defines the ID of
				"column" => 'text_value', // a default value inserted upon insert and not upated on update

				//data variable to be used with scripts include prefixes with var, init, body, multi
				"date" => 'init@data_date', // a variable instiated with INIT will only be inserted and not updated while inserting into the database
				'title' => 'var@data_title', //regular variable that will be inserted upon insert into database and update within database

				'post_content' => array( //arrays allow for each of the fields to have aditional configurations. they are
					'value' => 'body@data_content',//the actual value for the field
					'required' => true, //if the app cannot go ahead without this field

					//two different ways of initialize functions, as string or array of strings
					'function' => 'split_string, $string_to_split_by',//function is the very first option, and the second and more options are the arguements for the function
					//   OR
					'functions' => array(
						'split_string, "this string will be the second arguement with the quotes"',
						'excerpt, 100', //in this case the 100 integer will be passed on
						'another_function, $function_arg_by_variable',
						'even_other_function, var@data_content_var',
						'number_of_arguements, $are, var@limitless'
						//It is good to note that the functions go down in order
						//thus this would look like
						//split_string(excerpt(another_function(even_other_function(number_of_arguements($data_content, $are, $limitless), $data_content_var),$function_arg_by_variable),100),"this string will be the second arguement with the quotes")
					),
				),
				//it is also possible to insert ONLY a function, thus making the output an call to a function or a call with functions or others.
				//it is not implemented that it is only available upon insert and not update, but should be easily implementable.
				'function_var' => array(
					'function' => 'function_call'
				)
			)
		),

		//It is also possible to make multiple insertions,
		//this means that the variable is a array, inserting more than one value
		//these fields can also have embedded fields which insert after and only after the parent field is filled out

		//EXAMPLE OF A MULTI FIELD WITHOUT EMBEDDED FIELDS
		'table_multiple_urls' => array(
			'multi' => 'true', //MAKES IT AN ARRAY/MULTI
			"delete" => 'true',
			//does not use a sql where since it never updates (multis do not update, only add or remove via the ignore list)
			//thus a delete where is needed for when deleting the field.
			//not required when never going to delete the field
			'delete_where' => 'data_id = $db_id',
			'varify' => 'sql',//multi@variable instesd of var@variable says that the field is multi and will be checked individually
			'table' => array(
				'url_id' => 'NULL',
				'data_id' => '$db_id',//db_id was the ID for the first table
				'url' => 'multi@data_url'
				//multi@variable_name MUST be mentioned, else it might not work(untested)
			)
		),

		//EXAMPLE OF MULTI WITH EMBEDDING
		//WORDPRESS EXAMPLE
		//IN THEORY IT SHOULD BE EXPONENTIALLY EMBEDDABLE, BUT UNTESTED
		WP_PREFIX.'terms' => array(
			'where_sql' => 'term_id = $term_id',
			'varify' => 'select term_id from wp_2_terms where name = var@data_tags',
			'table' => array(
				'term_id' => '*term_id',
				'name' => 'multi@data_tags',
				'slug' => array(
					'value' => 'multi@data_tags',
					'function' => 'slug'
				),
				'term_group' => '0'
			),
			'multi' => array(
				WP_PREFIX."term_taxonomy" => array(
					'output_var' => 'term_taxonomy',
					//"delete" => 'true',
					'varify' => 'select term_taxonomy_id from wp_2_term_taxonomy where term_id = $term_id and taxonomy = \'post_tag\'',
					"table" => array(
						"term_taxonomy_id" => "NULL",
						"term_id" => "\$term_id",
						"taxonomy" => "post_tag",
						"description" => "",
						"parent" => '0',
						'count' => '0'
					)
				),
				WP_PREFIX."term_relationships" => array(
					"delete" => 'true',
					"varify" => 'select * from wp_2_term_relationships where object_id = $data_id and term_taxonomy_id = $term_taxonomy',
					"table" => array(
						'object_id' => '$data_id',
						'term_taxonomy_id' => '$term_taxonomy',
						'term_order' => '0'
					)
				)
			)
		)
	);
?>
