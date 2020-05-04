<?php

include_once 'Database.php';

// Database Connection
$database = new Database();

// Delete Database
$database->query('DROP DATABASE ' . 'Savagery' . ';');

// Rebuild Database
$dblink = new PDO('mysql:host=' . 'sql' . ';', 'Savagery', 'Password',
    array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$query = $dblink->prepare('CREATE DATABASE IF NOT EXISTS ' . 'Savagery' . ';');
$query->execute();
$query = $dblink->prepare('USE ' . 'Savagery' . ';');
$query->execute();

echo json_encode(
    array('message' => 'Succeeded in destroying EVERYTHING!')
);