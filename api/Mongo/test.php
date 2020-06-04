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

$db->add_document('test_col', array('cat'=>'miau'));

$data = $db->find_document('test_col', [], array('projection'=>array('_id'=>0, 'cat'=>1))); //['test'=>'Hello World!'], $options = array('projection'=>array('_id'=>0, 'town.armies'=>1)));

echo json_encode(array('data'=>$data));