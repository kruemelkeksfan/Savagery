<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../Database.php';

$database = new Database();

$data = json_decode(file_get_contents("php://input"), true);

if($data['username'] != "") {

    $username = $data['username'];
    $tax = $data['tax'];

    if($database->query("UPDATE Towns SET tax = :1 WHERE owner = :0", array($username, $tax))){
        echo json_encode(array("message"=>"Tax adjusted", "success"=>true));
    }else {
        echo json_encode(array("message"=>"Failed to adjust Tax", "success"=>false));
    }
}