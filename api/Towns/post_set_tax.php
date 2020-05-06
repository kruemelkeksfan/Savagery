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

    $database->query("UPDATE Towns SET tax = :1 WHERE owner = :0;", array($username, $tax));

    $new_tax = $database->query("SELECT tax FROM Towns WHERE owner = :0", array($username));

	var_dump($username);
	var_dump($new_tax);

    echo json_encode($new_tax[0]);

}