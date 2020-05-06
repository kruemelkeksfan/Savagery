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
    $population = $data['population'];

    $database->query("UPDATE Towns SET population = :1 WHERE owner = :0;", array($username, $population));

    $new_pop = $database->query("SELECT population FROM Towns WHERE owner = :0", array($username));

    echo json_encode($new_pop[0]);

}