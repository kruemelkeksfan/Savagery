<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../../MongoDatabase.php';

$database = new MongoDatabase();

$data = json_decode(file_get_contents("php://input"), true);

if ($data['username'] != "") {

    $username = $data['username'];
    $buildingtype = $data['buildingtype'];

    $result = $database->aggregation('Userdata',[
        array('$match'=>array('username'=>$username)),
        array('$project'=>array('buildings'=>1, 'sum_army'=>array('$sum'=>'$armies.strength'))),
        array('$unwind'=>'$buildings'),
        array('$group'=>array('_id'=>'$buildings.buildingtype', 'sum_lvl'=>array('$sum'=>'$buildings.level'),
           'army'=>array('$first'=>'$sum_army'))),
        array('$match'=>array('_id'=>$buildingtype)),
        array('$project'=>array('defense'=>array('$multiply'=>array('$sum_lvl', '$army')))),]);


    echo json_encode(array('i.nr*sum(a.strength)'=>$result[0]['defense']));
}