<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../../MongoDatabase.php';

$database = new MongoDatabase();

$data = json_decode(file_get_contents("php://input"), true);

if ($data['buildingtype'] != "") {

    $buildingtype = $data['buildingtype'];
    $army_id = $data['army_id'];

    $result = $database->aggregation('Userdata',[
        array('$match'=>array('armies.army_id'=>$army_id)),
        array('$unwind'=>'$armies'),
        array('$match'=>array('armies.army_id'=>$army_id)),
        array('$unwind'=>'$buildings'),
        array('$group'=>array('_id'=>'$buildings.buildingtype', 'sum_lvl'=>array('$sum'=>'$buildings.level'),
            'army'=>array('$first'=>'$armies.strength'))),
        array('$match'=>array('_id'=>$buildingtype)),
        array('$project'=>array('atk'=>array('$multiply'=>array('$sum_lvl', '$army')))),]);

    echo json_encode([array('i.nr*a.strength'=>$result[0]['atk'])]);
}