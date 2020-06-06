<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../Database.php';

$database = new MongoDatabase();

$users = $database->find_document('Userdata');

echo json_encode(
    array($users)
);