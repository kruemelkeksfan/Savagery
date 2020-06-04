<?php

include_once 'Database.php';
include_once 'MongoDatabase.php';

// Database Connection
$database = new MongoDatabase();

// Define Balance Settings
$settingdata = (new Database())->query('SELECT settingname, value FROM BalanceSettings;', array());

$settings = array();
foreach($settingdata as $setting)
{
	$settings[$setting['settingname']] =  $setting['value'];
}

(new MongoDatabase())->add_document('BalanceSettings', $settings);
//Define Buildingtypes
/*$database->add_document('Buildingtypes',
	array('Blacksmith' => array('Effect' => 'Increases the Defense of all Armies of this Town.', 'Cost' => '10', 'Maxworkers' => '6'),
    'Tavern' => array('Effect' => 'Increases Attack Strength of every Soldier of this Town.', 'Cost' => '20', 'Maxworkers' => '4'),
    'Townhall' => array('Effect' => 'Home to the Mayor of the Town (You).', 'Cost' => '0', 'Maxworkers' => '1'));*/

/*if($success && $fillBalaceSettings && $fillTimetable && $peaceTreatySuccess){
    echo json_encode(
        array('message' => 'Tables Created Successfully')
    );
} else {
    echo json_encode(
        array('message' => 'Tables Not Created Successfully')
    );
}*/
