<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../Database.php';

$database = new Database();

$data = json_decode(file_get_contents("php://input"), true);

if ($data['army_id'] != "") {

    $army_id = $data['army_id'];

    $army = $database->query("SELECT army_id, armyname, strength FROM Armies WHERE army_id=:0;", array($army_id))[0];

    echo json_encode($army);
}