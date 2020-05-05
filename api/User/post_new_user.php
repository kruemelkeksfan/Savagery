<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../Database.php';
//include_once '../models/Student.php';

$database = new Database();
//$db = $_db->connect();

//$student = new Student($db);

$data = json_decode(file_get_contents("php://input"), JSON_OBJECT_AS_ARRAY);

if($data->username != ""){
    /*$student->_firstName = $data->first_name;
    $student->_lastName = $data->last_name;
    $student->_grade = $data->grade;*/

    $username = $data->username;
    $password = $data->password;


    //ToDo: create town for player
    if($database->query('INSERT INTO Users (username, password, last_active) VALUES (:0, :1, :2);',
        array($username, password_hash($password, PASSWORD_DEFAULT), time()))) {
        echo json_encode(
            array('message' => 'User Created', 'success'=>true)
        );
    } else {
        echo json_encode(
            array('message' => 'User Not Created', 'success'=>false)
        );
    }
} else {
    echo json_encode(
        $data
    );
}