<?php
include('ErrorHandlerAPI.php');

class Database
	{
	private $host = "sql";
	private $db_name = "Savagery";
	private $db_user = "Savagery";
	private $password = "password";
	private $dblink;
	
	function __construct()
		{
        define('LOG_FILE', 'log.txt');
		
		try
			{
			$this->dblink = new MongoClient();
			}
		catch (PDOException $exc)
			{
			ErrorHandlerAPI::handle_error('Database connection could not be established: ' . $exc->getMessage() . '!');
			}
		}
		
	function new_collection(string $name)
		{
		$this->dblink->createCollection($name);
		}
	}
?>