<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../MongoDatabase.php';

$database = new MongoDatabase();

$types = $database->find_document('Buildingtypes');

echo json_encode(
    $types
);