<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../../MongoDatabase.php';

$database = new MongoDatabase();

$data = json_decode(file_get_contents("php://input"), true);

if($data['username'] != "") {

    $username = $data['username'];

    $town_data = $database->find_document('Userdata',array('username'=>$username),
        array('projection'=>array('_id'=>0, 'position'=>1, 'tax'=>1, 'population'=>1, 'townname'=>1)));

    echo json_encode($town_data);
}