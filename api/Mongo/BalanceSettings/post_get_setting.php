<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../../MongoDatabase.php';

$database = new MongoDatabase();

$data = json_decode(file_get_contents("php://input"), true);

if($data['settingname'] != "") {

    $settingname = $data['settingname'];
    $value = $database->find_document('BalanceSettings', [], array('projection'=>array('_id'=>0, $settingname=>1)));

    var_dump($value[0]);
    echo json_encode($value[0][$settingname]);
}