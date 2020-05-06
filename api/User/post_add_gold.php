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
    $value = $data['value'];

    $database->query("UPDATE Users SET gold = gold + :1 WHERE username = :0;", array($username, $value));
    $gold = $database->query("SELECT gold FROM Users WHERE username = :0;", array($username));

    echo json_encode($gold)[0]['gold'];
}