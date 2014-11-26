<?php
	
	require_once "mink/vendor/autoload.php";
	
	global $session;
	$session = null;
	global $mink_has_started;
	$mink_has_started = false;
	
	class Mink
	{
		private static $session;
	
		public static function newSession(){
			$session = self::$session;
			if($session === null)
			{
				try {
					$driver = new \Behat\Mink\Driver\Selenium2Driver(
						'firefox', 'base_url'
					);
					// init session:
					$session = new \Behat\Mink\Session($driver);
				
					// start session:
					$session->start();
				}
				catch(Exception $e)
				{
					$session = "Mink server unavailable";
				}
			}
			return $session;
		}
		
		public static function get()
		{
			return self::newSession();
		}
		
		public static function stop()
		{
			$session = self::newSession();
			if($session != null && gettype($session) == "object")
			{
				$session->stop();
			}	
		}
	}


		
?>