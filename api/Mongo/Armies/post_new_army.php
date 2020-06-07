<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../../MongoDatabase.php';

$data = json_decode(file_get_contents("php://input"), true);

if($data['armyname'] != "") {

    $armyname = $data['armyname'];
    $strength = $data['strength'];
    $username = $data['username'];

    $database = new MongoDatabase();

    $id = $database->aggregation('Userdata', array('$match'=>array('username'=>$username), array('$project'=>array(/*'_id'=>0,*/ 'count'=>array('$size'=>'$armies')))));

    $database->add_to_array('Userdata', array('username'=>$username), array('armies'=>array('armyname'=>$armyname, 'strength'=>$strength)));

    echo json_encode($id);
}else {
    echo json_encode(
        $data
    );
}