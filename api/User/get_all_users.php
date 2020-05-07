<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../Database.php';

$database = new Database();

$users = $database->query('SELECT * FROM Users;');

echo json_encode(
    array($users)
);