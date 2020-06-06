<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../MongoDatabase.php';

$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'];

/*$database = new Database();

$armies = $database->query("SELECT army_id, armyname, strength FROM Armies WHERE hometown IN 
                           (SELECT townname FROM Towns WHERE owner = :0);", array($username));*/

$database = new MongoDatabase();

$armies = $database->find_document('Userdata', array('username'=>$username), array('projection'=>array('_id'=>0, 'town.armies'=>1)));

echo json_encode($armies[0]['armies']);