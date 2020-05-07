<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../Database.php';

$database = new Database();

$data = json_decode(file_get_contents("php://input"), true);

if($data['username'] != "" && $data['building_id'] != "" && !empty($data['workers'])) {

    $building_id = $data['building_id'];
    $workers = $data['workers'];

    $database->query("UPDATE Buildings, Towns
		INNER JOIN Towns ON Buildings.town=Towns.townname
		SET Buildings.workers=:2 WHERE Towns.owner=:0 AND Buildings.building_id=:1;", array($data['username'], $building_id, $workers));

    $deployed_workers = $database->query("SELECT Buildings.workers FROM Buildings
		INNER JOIN Towns ON Buildings.town=Towns.townname
		WHERE Towns.owner=:0 AND Buildings.building_id=:1", array($data['username'], $building_id));

    echo json_encode($deployed_workers[0]);

}