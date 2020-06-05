<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../MongoDatabase.php';

$database = new Database();

$data = json_decode(file_get_contents("php://input"), true);

if($data['armyname'] != "") {

    $armyname = $data['armyname'];
    $strength = $data['strength'];
    $username = $data['username'];

    /*$townname = $database->query('SELECT townname FROM Towns WHERE owner = :0', array($username))[0]['townname'];

    if ($database->query('INSERT INTO Armies (armyname, strength, hometown) VALUES (:0, :1, :2);',
        array($armyname, $strength, $townname))) {

        echo json_encode(
            array('message' => 'Army Created', 'success' => true)
        );
    } else {
        echo json_encode(
            array('message' => 'Army Not Created', 'success' => false)
        );
    }*/

    $database = new MongoDatabase();

    $database->add_to_array('Userdata', array('username'=>$username), array('armyname'=>$armyname, 'strength'=>$strength));

}else {
    echo json_encode(
        $data
    );
}