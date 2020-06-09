<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../../MongoDatabase.php';

$database = new MongoDatabase();

$data = json_decode(file_get_contents("php://input"), true);

if($data['max'] != "") {

    $max = $data['max'];
    $min = $data['min'];

    $targets = $database->find_document('Userdata', array('position'=>array('$gte'=>$min, '$lte'=>$max)), array('projection'=>array('_id'=>0, 'townname'=>1, 'username'=>1)));

    echo json_encode($targets); //check if works!!
}