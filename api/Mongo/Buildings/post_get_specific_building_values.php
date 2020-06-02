<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../Database.php';

$database = new Database();

$data = json_decode(file_get_contents("php://input"), true);

if ($data['username'] != "" && $data['buildingtype'] != "") {

    $username = $data['username'];

    $buildings = $database->query('SELECT Buildings.building_id, Buildings.buildingtype, Buildings.level, Buildings.workers FROM Buildings
		INNER JOIN Towns ON Buildings.town=Towns.townname WHERE Town.owner=:0 AND Building.buildingtype=:1;',
		array($username, $data['buildingtype']));

    echo json_encode($buildings);
}