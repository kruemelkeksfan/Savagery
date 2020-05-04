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
    $password = $data->password;

    $userdata = $database->query('SELECT password FROM Users WHERE username=:0;', array($username));

    if(count($userdata) > 0 && password_verify($password, $userdata[0]['password'])){
        echo json_encode(
            array('message' => 'User Verified')
        );
    } else {
        echo json_encode(
            array('message' => 'User Not Found')
        );
    }
}