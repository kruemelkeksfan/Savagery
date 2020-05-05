<?php

include_once 'Database.php';

// Database Connection
$database = new Database();

// Define Timestamps
$timestamps = array(array('User_Reset', time()), array('Game_Start', time()));
// Define Balance Settings
$settings = array(array('Example_Setting', '1'));

// Create Tables
// Administration Tables
$database->query('CREATE TABLE IF NOT EXISTS Timetable (
			timename VARCHAR(16),
			record INT,
			PRIMARY KEY (timename)
			);');
//ToDo: town = foreign key?
$database->query('CREATE TABLE IF NOT EXISTS Users (
			username VARCHAR(16),
			password VARCHAR(256),
			last_active INT,
			gold INT DEFAULT 100,
			town VARCHAR(16),
			PRIMARY KEY (username)
			);'); // Use at least VARCHAR(60) for Passwords to prevent cutting Hashes
// Setting Tables
$database->query('CREATE TABLE IF NOT EXISTS BalanceSettings (
			settingname VARCHAR(32),
			value VARCHAR(16),
			PRIMARY KEY (settingname)
			);');
$database->query('CREATE TABLE IF NOT EXISTS Buildingtypes (
			buildingtypename VARCHAR(16),
			effect VARCHAR(64),
			cost INT,
			PRIMARY KEY (buildingtypename)
			);');
// Game Tables
$database->query('CREATE TABLE IF NOT EXISTS Towns (
			townname VARCHAR(32),
			position INT,
			tax INT,
			population INT,
			owner VARCHAR(16),
			PRIMARY KEY (townname),
			FOREIGN KEY (owner) REFERENCES Users(username)
			);');
$database->query('CREATE TABLE IF NOT EXISTS Buildings (
			building_id INT,
			workers INT,
			level INT,
			town VARCHAR(32),
			buildingtype VARCHAR(16),
			PRIMARY KEY (building_id, town),
			FOREIGN KEY (town) REFERENCES Towns(townname),
			FOREIGN KEY (buildingtype) REFERENCES Buildingtypes(buildingtypename)
			);');
$database->query('CREATE TABLE IF NOT EXISTS Armies (
			army_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			armyname INT,
			strength INT,
			hometown VARCHAR(32),
			PRIMARY KEY (army_id),
			FOREIGN KEY (hometown) REFERENCES Towns(townname)
			);');

// Save Timestamps
$database->query('INSERT INTO Timetable (timename, record) VALUES (:0, :1);', $timestamps);
// Save Balance Settings
$database->query('INSERT INTO BalanceSettings (settingname, value) VALUES (:0, :1);', $settings);

echo json_encode(
    array('message' => 'Tables Created Successfully')
);