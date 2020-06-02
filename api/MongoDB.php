<?php
include('ErrorHandlerAPI.php');

class MongoDatabase
	{
	private $host = "sql";
	private $db_name = "Savagery";
	private $db_user = "Savagery";
	private $password = "password";
	private $dblink;
	
	function __construct()
		{
        $db = new MongoClient("mongodb://user:password@localhost:27017");
        $this->dblink = $db->savagery_mongo;

		}
		
	function new_collection(string $name)
		{
		$this->dblink->createCollection($name);
		}

	function add_document(string $collection, $data)
        {
            $col = $this->dblink->$collection;
            try {
                $col->insert($data);
                return('ok');
            } catch (MongoCursorTimeoutException $e) {
                return($e);
            } catch (MongoCursorException $e) {
                return($e);
            } catch (MongoException $e) {
                return($e);
            }
        }

    function find_document(string $collection, $criteria = []){
        $col = $this->dblink->$collection;
        $cursor = $col->find($criteria);
        return($cursor);
    }

	}
?>