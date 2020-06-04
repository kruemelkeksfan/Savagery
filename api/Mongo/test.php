<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../MongoDB.php';

$db = new MongoDatabase();

if (empty($db)){
    echo json_encode('db empty');
}

$db->add_document('test_col', array('test'=>'Hello World!', 'town'=>array('name'=>'my_town',
    'armies'=>array(array('armyname'=>'cattroopers', 'strength'=>5),array('armyname'=>'catguard', 'strength'=>15))), 'username'=>'stupidcat'));

$data = $db->find_document('test_col', [], $options = array('projection'=>array("armies"=>1)));

echo json_encode(array('data'=>$data));