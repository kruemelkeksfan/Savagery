<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../Database.php';

$database = new Database();

$data = json_decode(file_get_contents("php://input"), true);

if($data['building_id'] != "") {

    $building_id = $data['building_id'];
    $buildingtype = $data['buildingtype'];
    $username = $data['username'];

    $townname = $database->query('SELECT townname FROM Towns WHERE owner = :0', array($username))[0]['townname'];


    if ($success = $database->query('INSERT INTO Buildings (building_id, workers, level, town, buildingtype) VALUES (:0, :1, :2, :3, :4);',
        array($building_id, 1, 1, $townname, $buildingtype))) {

        echo json_encode(
            array('message' => 'User Created', 'success' => true)
        );
    } else {
        echo json_encode(
            array('message' => 'User Not Created', 'success' => $success)
        );
    }
}else {
    echo json_encode(
        $data
    );
}