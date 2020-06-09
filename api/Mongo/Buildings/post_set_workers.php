<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../../MongoDatabase.php';

$database = new MongoDatabase();

$data = json_decode(file_get_contents("php://input"), true);

if($data['username'] != "" && $data['building_id'] != "" && !empty($data['workers'])) {

    $username = $data['username'];
    $building_id = $data['building_id'];
    $workers = $data['workers'];

    //$error=$database->update_field('Userdata',array('username'=>$username),array('$set'=>array('buildings.$[id].workers'=>$workers)), array('arrayFilters'=>[array('id.building_id'=>$building_id)]));
    $error=$database->update_field('Userdata', array('username'=>$username), array('$set'=>array('buildings.$[name].workers'=>$workers)), array('arrayFilters'=>[array('name.building_id'=>strval($building_id))]));
    $deployed_workers = $database->find_document('Userdata', array('username'=>$username), array('projection'=>array('_id'=>0, 'buildings'=>1)));//$database->find_document('Userdata',array('username'=>$username));//,array('projection'=>array('$_id'=>0, 'buildings'=>1))); //array('$elemMatch'=>array('building_id'=>$building_id)))));

    echo json_encode(array($deployed_workers, $error));

}