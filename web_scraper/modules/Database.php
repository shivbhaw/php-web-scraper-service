<?php

global $output_db;
//$output_db = true;
$output_db = false;

if (!class_exists('Database'))
{
	require_once 'bin/medoo.min.php';
	if(file_exists('app_config/db_config.php'))
	{
		require_once 'app_config/db_config.php';
	}
	else
	{
		require_once '../app_config/db_config.php';
	}

	class Database {

		//Database documentation - http://medoo.in/doc
		private static $database = NULL;

		static function getDatabase()
		{
			if(self::$database == NULL)
				self::startDatabase();
			return self::$database;
		}

		function __constructor()
		{

		}

		private static function startDatabase()
		{
			self::$database = new medoo(array(
				// required
				'database_type' => 'mysql',
				'database_name' => DATABASE_NAME,
				'server' => DATABASE_SERVER_ADDRESS,
				'username' => DATABASE_USERNAME,
				'password' => mcrypt_decrypt(MCRYPT_RIJNDAEL_256, "2539998557", DATABASE_PASSWORD, MCRYPT_MODE_ECB),

				// optional
				'port' => 3306,
				'charset' => 'utf8',
				// driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
				'option' => array(
					PDO::ATTR_CASE => PDO::CASE_NATURAL
				)
			));
		}

		static function Select($tableName, $just_clause, $selectStatement = null, $where = NULL)
		{
			if(self::$database == NULL)
				self::startDatabase();
			if($just_clause)
				$data = self::$database->select($tableName, $just_clause, $selectStatement, $where);
			else
				$data = self::$database->select($tableName, $selectStatement, $where);
			global $output_db;
			if($output_db){
			Output('select');
			Output($tableName);Output($just_clause);Output($selectStatement);Output($where);
			Output($data);}
			return $data;
		}

		static function Insert($tableName, $inputArray)
		{
			if(self::$database == NULL)
				self::startDatabase();
			$data = self::$database->insert($tableName, $inputArray);
			global $output_db;
			if($output_db){
			Output('insert');
			Output($tableName);Output($inputArray);Output($data);}
			return $data;
		}

		static function Update($tableName, $updateArray, $whereArray = NULL)
		{
			if(self::$database == NULL)
				self::startDatabase();
			$data = self::$database->update($tableName, $updateArray, $whereArray);
			global $output_db;
			if($output_db){
			Output('update');
			Output($tableName);Output($updateArray);Output($whereArray);Output($data);
			}
			return $data;
		}

		static function Rollback($tableName)
		{
			if(self::$database == NULL)
				self::startDatabase();
			$data = self::$database->query("Rollback;");
		}

		static function Delete($table_name, $where = null)
		{

			if(self::$database == NULL)
				self::startDatabase();
			$data = self::$database->delete($table_name, $where);
			global $output_db;
			if($output_db){
			Output('delete');
			Output($table_name);Output($where);Output($data);
			}
			return $data;
		}

		static function Query($string)
		{
			if(self::$database == NULL)
				self::startDatabase();
			$query = self::getDatabase()->query($string);

			global $output_db;
			if($output_db){
			Output('query');
			Output($string);
			Output($query);
			}
			if($query == false)
				return false;
			else
				return $query->fetchAll();
		}
	}
}
?>
