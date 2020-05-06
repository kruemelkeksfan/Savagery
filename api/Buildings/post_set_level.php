<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../Database.php';

$database = new Database();

$data = json_decode(file_get_contents("php://input"), true);

if ($data['building_id'] != "") {

    $building_id = $data['building_id'];

    if ($database->query("UPDATE Buildings SET level = level + 1 WHERE building_id = :0;", array($building_id))) {
        echo json_encode(array("message" => "Workers sent", "success" => true));
    } else {
        echo json_encode(array("message" => "Failed to send Workers", "success" => false));
    }
}