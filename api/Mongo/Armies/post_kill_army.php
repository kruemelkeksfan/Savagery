<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../../MongoDatabase.php';

$database = new MongoDatabase();

$data = json_decode(file_get_contents("php://input"), true);

if($data['army_id'] != "")
	{
    /*$database->query('DELETE FROM Armies WHERE army_id=:0;', array($data['army_id']));*/
	$database->delete_field('Userdata', array('username' => $data['username']), array('town.armies' => array('army_id' => $data['army_id']))); // TODO: update "army_id" if something in the id design changes
	}