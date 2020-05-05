<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../Database.php';

$database = new Database();

$data = json_decode(file_get_contents("php://input"));

if($data->username != "") {

    $username = $data->username;
    $last_active = $data->last_active;



    if($userdata = $database->query('UPDATE Users SET last_active=:0 WHERE username=:1;', array($last_active, $username))){
        echo json_encode(
            array('message' => 'Updated last_active', 'success'=>true)
        );
    } else {
        echo json_encode(
            array('message' => 'Could not update Last_active', 'success'=>false)
        );
    }
}