<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../../MongoDatabase.php';

$data = json_decode(file_get_contents("php://input"), true);

if($data['armyname'] != "") {

    $armyname = $data['armyname'];
    $strength = $data['strength'];
    $username = $data['username'];

    $database = new MongoDatabase();

    /*$id = null;
    $id = $database->aggregation('Userdata',[
        array('$project' => array('max' => array('$max' => array('$buildings.building_id')))),
        array('$group' => array('_id'=> null, 'id' => array('$max' => $max)))]);*/

    //echo(json_encode($id));


    //$id = $database->aggregation('Userdata', array('$match'=>array('username'=>$username)));//, array(array('$project'=>array(/*'_id'=>0,*/ 'count'=>array('$size'=>'$armies'))))));
    $id = $database->aggregation('Userdata', [array('$project'=>array('_id'=>0, 'count'=>array('$max'=>'$armies.army_id'))),array('$group'=>array('_id'=>null, 'id'=>array('$max'=>'$count'))),]);

    $new_id = $id[0]['id']+1;
    $database->add_to_array('Userdata', array('username'=>$username), array('armies'=>array('army_id'=>$new_id, 'armyname'=>$armyname, 'strength'=>intval($strength))));

    echo json_encode(array($id[0]['id']));
}else {
    echo json_encode(
        $data
    );
}