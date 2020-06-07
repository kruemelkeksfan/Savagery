<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../../MongoDatabase.php';

$database = new MongoDatabase();

$data = json_decode(file_get_contents("php://input"), true);

if($data['username'] != "") {

    $username = $data['username'];
    $password = $data['password'];

    // $userdata = $database->query('SELECT password FROM Users WHERE username=:0;', array($username));
	$userdata = $database->find_document('Userdata', array('username' => $username), array('projection'=>array('_id'=>0, 'password'=>1)));

    if(count($userdata) > 0 && password_verify($password, $userdata[0]['password'])){
        echo json_encode(
            array('message' => 'User Verified', 'success'=>true)
        );
    } else {
        echo json_encode(
            array('message' => 'User Not Found', 'success'=>false)
        );
    }
}