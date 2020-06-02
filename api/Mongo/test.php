<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../MongoDB.php';

$db = new MongoDatabase();

$db->add_document('test_col', array('test'=>'Hello World!'));
$data = $db->find_document('test_col');

echo json_encode($data);