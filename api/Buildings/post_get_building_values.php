<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../Database.php';

$database = new Database();

$data = json_decode(file_get_contents("php://input"), true);

if ($data['username'] != "") {

    $username = $data['username'];

<<<<<<< HEAD
    $buildings = $database->query('SELECT Buildings.building_id, Buildings.buildingtype. Buildings.level, Buildings.workers FROM Buildings
		INNER JOIN Towns ON Buildings.town=Towns.townname WHERE Town.owner=:0;',
		array($username));
=======
    $buildings = $database->query("SELECT building_id, buildingtype, level, workers  FROM Buildings WHERE town = :0;", array($townname));
>>>>>>> daa04ecae09f01b62a42e3d33396a02f82571dd3

    echo json_encode($buildings);
}