<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../Database.php';

$database = new Database();

$record = $database->query('SELECT record FROM Timetable WHERE timename=:0;', array('User_Reset'));

    echo json_encode(
        array('record' => $record[0]['record'], 'success'=>true)
    );