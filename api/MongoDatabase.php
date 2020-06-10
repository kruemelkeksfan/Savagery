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
        $this->dblink = new MongoDB\Driver\Manager("mongodb://localhost:27017");
		
		return array('success' => true);
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
			
			return array('success' => true);
        }
	
    function add_array_field(string $collection, $filter, $data, $options = []) {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update($filter, array('$push'=>$data), $options);
        try {
            $this->dblink->executeBulkWrite($this->db_name . $collection, $bulk);
        } catch (Exception $e) {
            return($e);
        }
		
		return array('success' => true);
    }

    function update_field(string $collection, $filter, $data, $options = []) {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update($filter, $data, $options);
        try {
            $e = $this->dblink->executeBulkWrite($this->db_name . $collection, $bulk);
            return($e);
        } catch (Exception $e) {
            return($e);
        }
		
		return array('success' => true);
    }

    function aggregation(string $collection, $pipe) {
        $cmd = new MongoDB\Driver\Command([
            'aggregate'=>$collection,
            'pipeline'=>[$pipe,],
            'cursor'=> new stdClass(),
        ]);

        try {
            $cursor = $this->dblink->executeCommand('savagery_mongo',$cmd);

            //$cursor->setTypeMap(['root' => 'array']);
            return($cursor->toArray());
        } catch (Exception $e) {
            return($e);
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            return($e);
        }
		
		return array('success' => true);
    }

    function add_to_array(string $collection, $filter, $data) {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update($filter, array('$push'=>$data));
        try {
            $this->dblink->executeBulkWrite($this->db_name . $collection, $bulk);
        } catch (Exception $e) {
            return($e);
        }
		
		return array('success' => true);
    }

    function delete_field(string $collection, $filter, $data) {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update($filter, array('$pull'=>$data));
        try {
            $this->dblink->executeBulkWrite($this->db_name . $collection, $bulk);
        } catch (Exception $e) {
            return($e);
        }
		
		return array('success' => true);
    }

    function find_document(string $collection, $criteria = [], $options = []){
        $query = new MongoDB\Driver\Query($criteria, $options);
        try {
            $cursor = $this->dblink->executeQuery($this->db_name . $collection, $query);
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            return($e);
        }
        $cursor->setTypeMap(['root' => 'array']);
        return($cursor->toArray());
    }
	
    function check(){		
        return($this->find_document('BalanceSettings'));
    }
	}
?>