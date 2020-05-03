<?php
abstract class GameInfo
	{
	function __construct(string $gametitle,
		string $dbaddress, string $dbuser, string $dbpassword,
		string $loginpage, string $overviewpage,
		array $navigation,
		array $admins, string $logfile,
		string $stylesheet = null)
		{
		define('GAME_TITLE', $gametitle);
		define('DB_ADDRESS', $dbaddress);
		define('DB_USER', $dbuser);
		define('DB_PASSWORD', $dbpassword);
		define('LOGIN_PAGE', $loginpage);
		define('OVERVIEW_PAGE', $overviewpage);
		define('NAVIGATION', $navigation);
		define('ADMINS', $admins);
		define('LOG_FILE', $logfile);
		define('STYLESHEET', $stylesheet);
		}
	}
?>
