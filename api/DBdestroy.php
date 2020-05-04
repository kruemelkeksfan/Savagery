<?php

include_once 'Database.php';

// Database Connection
$database = new Database();

// Delete Database
$database->query('DROP DATABASE ' . constant('GAME_TITLE') . ';');

// Rebuild Database
$dblink = new PDO('mysql:host=' . constant('DB_ADDRESS') . ';', constant('DB_USER'), constant('DB_PASSWORD'),
    array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$query = $dblink->prepare('CREATE DATABASE IF NOT EXISTS ' . constant('GAME_TITLE') . ';');
$query->execute();
$query = $dblink->prepare('USE ' . constant('GAME_TITLE') . ';');
$query->execute();

echo json_encode(
    array('message' => 'Succeeded in destroying EVERYTHING!')
);