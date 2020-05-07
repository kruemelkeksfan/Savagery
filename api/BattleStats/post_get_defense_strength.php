<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../Database.php';

$database = new Database();

$data = json_decode(file_get_contents("php://input"), true);

if ($data['username'] != "") {

    $username = $data['username'];
    $buildingtype = $data['buildingtype'];

    $result = $database->query('nr*sum(a.strength) FROM Armies as a INNER JOIN Towns as t on a.hometown = t.townname INNER JOIN (SELECT town, sum(level) as nr from Buildings where buildingtype = :0 group by town) On t.townname = town WHERE t.owner = :1;',
        array($buildingtype, $username));

    echo json_encode($result);
}