<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../Database.php';

$database = new Database();

$data = json_decode(file_get_contents("php://input"), true);

if($data['max'] != "") {

    $max = $data['max'];
    $min = $data['min'];

    $targets = $database->query('SELECT townname, owner FROM Towns WHERE position <= :0 AND position >= :1;', array($max, $min));

    echo json_encode($targets);
}