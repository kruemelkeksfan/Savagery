<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../../MongoDatabase.php';

$database = new MongoDatabase();

$data = json_decode(file_get_contents("php://input"), true);

if($data['building_id'] != "") {

    $building_id = $data['building_id'];
    $buildingtype = $data['buildingtype'];
    $username = $data['username'];

    if (empty($data['workers'])){
        $workers = 1;
    } else {
        $workers = $data['workers'];
    }

    if (empty($data['level'])){
        $level = 1;
    } else {
        $level = $data['level'];
    }

	$database->add_array_field('Userdata', array('username' => $username),
		array('buildings' => array('building_id' => intval($building_id), 'workers' => intval($workers), 'level' => intval($level), 'buildingtype' => $buildingtype)));
	
}else {
    echo json_encode(
        $data
    );
}