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
    $mapsize = $database->query('SELECT value FROM BalanceSettings WHERE settingname=:0;', array('Map_Size'))[0]['value'];

    if (empty($data['tax'])) {
        $tax = $database->query('SELECT value FROM BalanceSettings WHERE settingname=:0;', array('Start_Tax'))[0]['value'];
    } else {
        $tax = $data['tax'];
    }

    if (empty($data['population'])){
        $population = $database->query('SELECT value FROM BalanceSettings WHERE settingname=:0;', array('Start_Population'))[0]['value'];
    } else {
        $population = $data['population'];
    }


    if ($database->query('INSERT INTO Towns (townname, position, tax, population, owner) VALUES (:0, :1, :2, :3, :4);',
        array($username . 's Town', mt_rand(0, $mapsize - 1), $tax, $population, $username))) {

        echo json_encode(
            array('message' => 'Town Created', 'success' => true)
        );
    } else{
        echo json_encode(
            array('message' => 'Town Not Created', 'success' => false)
        );
    }
}