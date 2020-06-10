<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../MongoDatabase.php';

$db = new MongoDatabase();

if (empty($db)){
    echo json_encode('db empty');
}

$db->add_document('test_col', array('test'=>'Hello World!', 'townname'=>'my_town',
    'armies'=>array(array('armyname'=>'cattroopers', 'strength'=>'5'),array('armyname'=>'catguard', 'strength'=>15)), 'username'=>'stupidcat'));

//$db->add_document('test_col', array('cat'=>'miau'));

//$error=$db->update_array_field('test_col',array('test'=>'Hello World!'), array('town.armies.$[name].strength'=>100), array('name.armyname'=>'cattroopers'));
$db->update_field('test_col', array('test'=>'Hello World!'), array('$set'=>array('armies.$[name].armyname'=>'kittencorps')), array('arrayFilters'=>[array('name.strength'=>5)]));
$db->update_field('test_col', array('test'=>'Hello World!'), array('$inc'=>array('armies.$[name].strength'=>10)), array('arrayFilters'=>[array('name.strength'=>15)]));
/*$db->add_to_array('test_col', array('test'=>'Hello World!'), array('town.armies'=>array('armyname'=>'catbattallion', 'strength'=>150)));
$db->add_field('test_col', array('test'=>'Hello World!'), array('town.password'=>'pwd'));
$db->update_field('test_col', array('test'=>'Hello World!'), array('test'=>'Goodbye World!'));
$db->delete_field('test_col', array('test'=>'Goodbye World!'), array('username'=>''));*/

$data = $db->find_document('test_col');
//$data2 = $db->find_document('test_col',['test'=>'Goodbye World!'], $options = array('projection'=>array('_id'=>0, 'town.armies'=>1)));

echo json_encode(array('data'=>$data));