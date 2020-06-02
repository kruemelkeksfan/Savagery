<?php

include_once 'Database.php';

// Database Connection
$database = new Database();

// Init Collections
$database->new_collection('Settings');
$database->new_collection('Buildingtypes');
$database->new_collection('Playerdata');

// Define Timestamps
/*$timestamps = array(array('User_Reset', time()), array('Game_Start', time()));
// Define Balance Settings
$settings = array(array('Map_Size', '100'), array('Start_Gold', '100'), array('Start_Tax', '5'),
    array('Start_Population', '5'), array('Range_Multiplier', '10'), array('Upgrade_Multiplier', '50'));
//Define Buildingtypes
$buildingtypes = array(array('Blacksmith', 'Increases the Defense of all Armies of this Town.', '10', '6'),
    array('Tavern', 'Increases Attack Strength of every Soldier of this Town.', '20', '4'),
    array('Townhall', 'Home to the Mayor of the Town (You).', '0', '1'));

// Create Tables
// Administration Tables
$database->query('CREATE TABLE IF NOT EXISTS Timetable (
			timename VARCHAR(16),
			record INT,
			PRIMARY KEY (timename)
			);');
$success = $database->query('CREATE TABLE IF NOT EXISTS Users (
			username VARCHAR(16),
			password VARCHAR(256),
			last_active INT,
			gold INT,
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
			maxworkers INT,
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
			armyname VARCHAR(32),
			strength INT,
			hometown VARCHAR(32),
			PRIMARY KEY (army_id),
			FOREIGN KEY (hometown) REFERENCES Towns(townname)
			);');
$peaceTreatySuccess = $database->query('CREATE TABLE IF NOT EXISTS PeaceTreaty (
			user1 VARCHAR(16),
			user2 VARCHAR(16),
			expiry_time INT,
			PRIMARY KEY (user1, user2),
			FOREIGN KEY (user1) REFERENCES Users(username),
			FOREIGN KEY (user2) REFERENCES Users(username)
			);');

// Save Timestamps
$fillTimetable = $database->query('INSERT INTO Timetable (timename, record) VALUES (:0, :1);', $timestamps);
// Save Balance Settings
$fillBalaceSettings = $database->query('INSERT INTO BalanceSettings (settingname, value) VALUES (:0, :1);', $settings);

$database->query('INSERT INTO Buildingtypes (buildingtypename, effect, cost, maxworkers) VALUES (:0, :1, :2, :3);', $buildingtypes);*/


if($success && $fillBalaceSettings && $fillTimetable && $peaceTreatySuccess){
    echo json_encode(
        array('message' => 'Tables Created Successfully')
    );
} else {
    echo json_encode(
        array('message' => 'Tables Not Created Successfully')
    );
}
