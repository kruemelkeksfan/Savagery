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
			$this->dblink = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->db_user, $this->password,
				array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			}
		catch (PDOException $exc)
			{
			if($exc->getCode() == 1049)
				{
				ErrorHandlerAPI::handle_error('Database not available!');
				}
			else
				{
				ErrorHandlerAPI::handle_error('Database connection could not be established: ' . $exc->getMessage() . '!');
				}
			}
		}
		
	// UPDATE table SET column1 = :0, column2 = :1 WHERE filter1 = :2 filter2 = :3;
	// $parameters may be a single- or multi-dimensional numerically indexed array, depending on whether you want to execute the query once ore multiple times
	// Remember to sanitize all non parametrized inputs
	function query(string $querystring, array $parameters = array())
		{
		if(strpos($querystring, ':0') !== false && empty($parameters))
			{
			return array();
			}
			
		$query = $this->dblink->prepare($querystring);
		$multi = !empty($parameters) && is_array($parameters[0]);	// Errors in this Line probably mean that you are feeding non-numerically indexed Parameters
		$success = true;
		$result = null;
	
		// TODO: Clean up the following code duplication by using a separate method or some other clever way
		if(!$multi)
			{
			for($i = 0; $i < count($parameters); ++$i)
				{
				$query->bindValue(':' . $i, $parameters[$i]);
				}
		
			try
				{
				$success = $query->execute();
				if(substr($querystring, 0, 6) === 'SELECT')
					{
					$result = $query->fetchAll(PDO::FETCH_ASSOC);
					}
				}	
			catch(PDOException $exc)
				{
				if($exc->getCode() === '23000')
					{
					$success = false;
					}
				else
					{
					ErrorHandlerAPI::handle_error('PDOException ' . $exc->getMessage() . ' while trying to perform query ' . $querystring . '!');
					}
				}
			}
		else
			{
			$result = array();
			foreach($parameters as $parameterrow)
				{
				for($i = 0; $i < count($parameterrow); ++$i)
					{
					$query->bindValue(':' . $i, $parameterrow[$i]);
					}
					
				try
					{
					$success = $query->execute() && $success;	// if success is false, it stays false during subsequent iterations
					if(substr($querystring, 0, 6) === 'SELECT')
						{
						$result[] = $query->fetchAll(PDO::FETCH_ASSOC);
						}
					}	
				catch(PDOException $exc)
					{
					if($exc->getCode() === '23000')
						{
						$success = false;
						}
					else
						{
						ErrorHandlerAPI::handle_error('PDOException ' . $exc->getMessage() . ' while trying to perform query ' . $querystring . '!');
						}
					}
				}
			}
	
		if(substr($querystring, 0, 6) === 'SELECT')
			{
			return $result;
			}
		else
			{
			return $success;
			}
		}
	}
?>