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
    $mapsize = $database->$database->find_document('BalanceSettings', [], array('projection'=>array('_id'=>0, 'Map_Size'=>1)))[0]['Map_Size'];

    if (empty($data['tax'])) {
        $tax = $database->find_document('BalanceSettings', [], array('projection'=>array('_id'=>0, 'Start_Tax'=>1)))[0]['Start_Tax'];
    } else {
        $tax = $data['tax'];
    }

    if (empty($data['population'])){
        $population = $database->find_document('BalanceSettings', [], array('projection'=>array('_id'=>0, 'Start_Population'=>1)))[0]['Start_Population'];
    } else {
        $population = $data['population'];
    }


    $result = $database->update_field('Userdata', array('username'=>$username),
        array('$set'=>array('townname'=>$username.'s Town', 'position'=>mt_rand(0, $mapsize - 1), 'tax'=>$tax, 'population'=>$population)));
        echo json_encode(
            array($result)
        );

}