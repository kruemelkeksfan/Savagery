<?php
abstract class ErrorHandler
	{
	public static function handle_warning(string $message)
		{
		var_dump('Something might have died, please contact Admin if something here seems broken!'); // TODO: Add E-Mail-Address and/or Contact Form
		error_log('WARNING: ' . $message . PHP_EOL . 'Caused by ' . $_SESSION['username'] . PHP_EOL, 3, constant('LOG_FILE'));
		}
		
	public static function handle_error(string $message)
		{
		var_dump('Something just went wrong, please contact Admin!'); // TODO: Add E-Mail-Address and/or Contact Form
		error_log('ERROR: ' . $message . PHP_EOL . 'Caused by ' . $_SESSION['username'] . PHP_EOL, 3, constant('LOG_FILE'));
		exit();
		}
	}
?>