<?php

include_once 'Database.php';

// Database Connection
$database = new MongoDB();

// Init Collections
$database->new_collection('Settings');
$database->new_collection('Buildingtypes');
$database->new_collection('Playerdata');

// Define Balance Settings
$database->add_document('Settings',
	array('Map_Size' => 100, 'Start_Gold' => 100, 'Start_Tax' => 5, 'Start_Population' => 5, 'Range_Multiplier' => 10, 'Upgrade_Multiplier' => 50));
//Define Buildingtypes
$database->add_document('Buildingtypes',
	array('Blacksmith' => array('Effect' => 'Increases the Defense of all Armies of this Town.', 'Cost' => '10', 'Maxworkers' => '6'),
    'Tavern' => array('Effect' => 'Increases Attack Strength of every Soldier of this Town.', 'Cost' => '20', 'Maxworkers' => '4'),
    'Townhall' => array('Effect' => 'Home to the Mayor of the Town (You).', 'Cost' => '0', 'Maxworkers' => '1'));

/*if($success && $fillBalaceSettings && $fillTimetable && $peaceTreatySuccess){
    echo json_encode(
        array('message' => 'Tables Created Successfully')
    );
} else {
    echo json_encode(
        array('message' => 'Tables Not Created Successfully')
    );*/
}
