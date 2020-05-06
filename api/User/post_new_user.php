<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../Database.php';

$database = new Database();

$data = json_decode(file_get_contents("php://input"), JSON_OBJECT_AS_ARRAY);

if($data['username'] != "") {

    $username = $data['username'];
    $password = $data['password'];

    $gold = $database->query('SELECT value FROM BalanceSettings WHERE settingname=:0;', array('Start_Gold'))[0]['value'];


    if ($database->query('INSERT INTO Users (username, password, last_active, gold) VALUES (:0, :1, :2, :3);',
        array($username, password_hash($password, PASSWORD_DEFAULT), time(), $gold))) {

        echo json_encode(
            array('message' => 'User Created', 'success' => true)
        );
    } else {
        echo json_encode(
            array('message' => 'User Not Created', 'success' => false)
        );
    }
}else {
    echo json_encode(
        $data
    );
}