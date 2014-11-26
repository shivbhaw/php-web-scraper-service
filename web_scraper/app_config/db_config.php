<?php
	define("DATABASE_NAME","");
	define("DATABASE_SERVER_ADDRESS", "");
	define("WP_PREFIX", "wp_");
	define("DATABASE_USERNAME", "");
	define("DATABASE_PASSWORD", $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, '2539998557', /*PUT PASSWORD HERE --> */ "", MCRYPT_MODE_ECB));
?>
