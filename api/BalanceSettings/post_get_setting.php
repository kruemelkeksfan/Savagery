<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../Database.php';

$database = new Database();

$data = json_decode(file_get_contents("php://input"), true);

if($data['settingname'] != "") {

    $settingname = $data['settingname'];
    $value = $database->query('SELECT value FROM BalanceSettings WHERE settingname=:0;', array($settingname))[0]['value'];

    echo json_encode(array($value[0]['value'])); //returns not as array, but plain value!!S
}