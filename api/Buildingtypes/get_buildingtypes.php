<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../Database.php';

$database = new Database();

$types = $database->query('SELECT buildingtypename, effect, cost FROM Buildingtypes;');

echo json_encode(
    array($types)
);