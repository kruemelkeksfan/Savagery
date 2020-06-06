<?php

include_once 'Database.php';
include_once 'MongoDatabase.php';

// Database Connection
$sql = new Database();
$mongo = new MongoDatabase();

// Import Balance Settings
$settingdata = $sql->query('SELECT settingname, value FROM BalanceSettings;', array());

$settings = array();
foreach($settingdata as $setting)
{
	$settings[$setting['settingname']] = $setting['value'];
}

$result['settings'] = $mongo->add_document('BalanceSettings', $settings);

//Import Buildingtypes
$buildingtypedata = $sql->query('SELECT buildingtypename, effect, cost, maxworkers FROM Buildingtypes;', array());

$result['buildingtypes'] = $mongo->add_document('Buildingtypes', $buildingtypedata);

// Import Player Data
$playerdata = $sql->query('SELECT Users.username, Users.password, Users.gold, Towns.townname, Towns.position, Towns.tax, Towns.population FROM Users
	INNER JOIN Towns ON Users.username = Towns.owner;', array());

foreach($playerdata as $player) {
    $buildings = $sql->query('SELECT building_id, buildingtype, level, workers FROM Buildings WHERE town = :0;', array($player['townname']));
    $armies = $sql->query('SELECT army_id, armyname, strength FROM Armies WHERE hometown = :0;', array($player['townname']));
    $treaties = $sql->query('SELECT user2, expiry_time FROM PeaceTreaty WHERE user1 = :0;', array($player['username']));
    $player['armies'] = $armies;
    $player['buildings'] = $buildings;
    $player['treaties'] = $treaties;

    $result['userdata'] = $mongo->add_document('Userdata', $player);
}

echo json_encode($result);
