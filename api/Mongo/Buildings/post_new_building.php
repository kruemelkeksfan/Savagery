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

    //$townname = $database->query('SELECT townname FROM Towns WHERE owner = :0', array($username))[0]['townname'];

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

    /*if ($success = $database->query('INSERT INTO Buildings (building_id, workers, level, town, buildingtype) VALUES (:0, :1, :2, :3, :4);',
        array($building_id, $workers, $level, $townname, $buildingtype))) {

        echo json_encode(
            array('message' => 'User Created', 'success' => true)
        );
    } else {
        echo json_encode(
            array('message' => 'User Not Created', 'success' => $success)
        );
    }*/
	
	$id = null;
	$id = $database->aggregation('Userdata',
		array('$project' => array('id' => array('$max' => array('$town.buildings.building_id')))))['id'];
	if($id == null)
	{
		$id = 0;
	}
	else
	{
		++$id;
	}
		
	$database->add_array_field('Userdata', array('username' => $username),
		array('buildings' => array('building_id' => $id, 'workers' => $workers, 'level' => $level, 'buildingtype' => $buildingtype)));
	
}else {
    echo json_encode(
        $data
    );
}