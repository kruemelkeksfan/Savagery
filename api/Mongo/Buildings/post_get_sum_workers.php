<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../../MongoDatabase.php';

$database = new MongoDatabase();

$data = json_decode(file_get_contents("php://input"), true);

if ($data['username'] != "") {

    $username = $data['username'];

    // $workers = $database->query("SELECT sum(workers) FROM Buildings WHERE town IN 
    // 	(SELECT townname FROM Towns WHERE owner = :0);", array($username));
	$workers = $database->aggregation('Userdata',[
	    array('$match'=>array('username'=>$username)),
		array('$project' => array('_id'=>0, 'sum' => array('$sum' => array('$buildings.workers')))),])[0]['sum'];

    echo json_encode($workers);
}