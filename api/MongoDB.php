<?php
include('ErrorHandlerAPI.php');

class MongoDatabase
	{
	private $host = "mongo";
	private $db_name = "savagery_mongo.";
	private $db_user = "user";
	private $password = "password";
	private $dblink;
	
	function __construct()
		{
        /*$db = new MongoClient("mongodb://user:password@localhost:27017");
        $this->dblink = $db->savagery_mongo;*/
        $this->dblink = new MongoDB\Driver\Manager("mongodb://"./*$this->db_user.":".$this->password."@".*/"localhost:27017");
		}
		
	function new_collection(string $name)
		{
		$this->dblink->createCollection($name);
		}

	function add_document(string $collection, $data)
        {
            /*$col = $this->dblink->$collection;
            try {
                $col->insert($data);
                return('ok');
            } catch (MongoCursorTimeoutException $e) {
                return($e);
            } catch (MongoCursorException $e) {
                return($e);
            } catch (MongoException $e) {
                return($e);
            }*/
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->insert($data);
            try {
                $this->dblink->executeBulkWrite($this->db_name . $collection, $bulk);
            } catch (Exception $e) {
                echo($e);
            }

        }

    function find_document(string $collection, $criteria = []){
        /*$col = $this->dblink->$collection;
        $cursor = $col->find($criteria);*/
        $query = new MongoDB\Driver\Query($criteria, []);
        try {
            $cursor = $this->dblink->executeQuery($this->db_name . $collection, $query);
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            return($e);
        }
        return($cursor);
    }

	}
?>