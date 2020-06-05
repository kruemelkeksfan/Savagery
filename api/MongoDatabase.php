<?php
include_once('ErrorHandlerAPI.php');

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
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->insert($data);
            try {
                $this->dblink->executeBulkWrite($this->db_name . $collection, $bulk);
            } catch (Exception $e) {
                return($e);
            }

        }

    function add_field(string $collection, $filter, $data) {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update($filter, array('$set'=>$data), array('multi'=>true));
        try {
            $this->dblink->executeBulkWrite($this->db_name . $collection, $bulk);
        } catch (Exception $e) {
            return($e);
        }
    }

    function update_field(string $collection, $filter, $data, $options = []) {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update($filter, array('$set'=>$data), $options);
        try {
            $this->dblink->executeBulkWrite($this->db_name . $collection, $bulk);
        } catch (Exception $e) {
            return($e);
        }
    }

    function add_to_array(string $collection, $filter, $data) {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update($filter, array('$push'=>$data));
        try {
            $this->dblink->executeBulkWrite($this->db_name . $collection, $bulk);
        } catch (Exception $e) {
            return($e);
        }
    }

    function delete_field(string $collection, $filter, $data) {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update($filter, array('$unset'=>$data));
        try {
            $this->dblink->executeBulkWrite($this->db_name . $collection, $bulk);
        } catch (Exception $e) {
            return($e);
        }
    }

    function find_document(string $collection, $criteria = [], $options = []){
        $query = new MongoDB\Driver\Query($criteria, $options);
        try {
            $cursor = $this->dblink->executeQuery($this->db_name . $collection, $query);
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            return($e);
        }
        return($cursor->toArray());
    }

	}
?>