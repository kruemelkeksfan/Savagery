<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../../MongoDatabase.php';

$database = new MongoDatabase();

$data = json_decode(file_get_contents("php://input"), true);

if ($data['username'] != "" && $data['building_id'] != "") {

    $building_id = $data['building_id'];
    $username = $data['username'];

    /*$database->query("UPDATE Buildings
		INNER JOIN Towns ON Buildings.town=Towns.townname
		SET Buildings.level=Buildings.level + 1 WHERE Towns.owner=:0 AND Buildings.building_id=:1;", array($data['username'], $building_id));

    $level = $database->query("SELECT Buildings.level FROM Buildings
		INNER JOIN Towns ON Buildings.town=Towns.townname
		WHERE Towns.owner=:0 AND Buildings.building_id=:1;", array($data['username'], $building_id));*/
	
	/*// TODO: Is the Referencing of level and building_id correct?
	$database->inc_array_field('Userdata', array('username' => $data['username']), array('level' => 1), array('building_id' => $building_id));
	
	$level = $database->find_document('Userdata', array('username' => $data['username'], 'buildings.building_id' => $building_id),
		array('projection'=>array('_id'=>0, 'buildings.level'=>1)));*/

    $error=$database->update_field('Userdata', array('username'=>$username), array('$inc'=>array('buildings.$[name].level'=>1)), array('arrayFilters'=>[array('name.building_id'=>intval($building_id))]));
    $level = $database->find_document('Userdata', array('username'=>$username), array('projection'=>array('_id'=>0, 'buildings'=>1)));//$database->find_document('Userdata',array('username'=>$username));//,array('projection'=>array('$_id'=>0, 'buildings'=>1))); //array('$elemMatch'=>array('building_id'=>$building_id)))));


    echo json_encode($level);
}