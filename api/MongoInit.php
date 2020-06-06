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

/*$buildingtypes = array();
foreach($buildingtypedata as $buildingtype)
{
	$buildingtypes[$buildingtype['buildingtypename']] =
		array('Effect' => $buildingtype['effect'], 'Cost' => $buildingtype['cost'], 'Maxworkers' => $buildingtype['maxworkers']);
}*/

$result['buildingtypes'] = $mongo->add_document('Buildingtypes', $buildingtypedata);
/*
// Import Player Data
$playerdata = $sql->query('SELECT Users.username, Users.password, Users.gold, Towns.townname, Towns.position, Towns.tax, Towns.population FROM Users
	INNER JOIN Towns ON Users.username = Towns.owner;', array());

$players = array();
foreach($playerdata as $player)
{
	$players[$player['username']] = array('Password' => $player['password'], 'Gold' => $player['gold']);
	$players[$player['username']][$player['townname']] = array('Position' => $player['position'], 'Tax' => $player['tax'], 'Population' => $player['population']);
	
	$buildingdata = $sql->query('SELECT building_id, workers, level, buildingtype FROM Buildings WHERE town = :0;', array($player['townname']));
	$players[$player['username']][$player['townname']]['Buildings'] = array();
	foreach($buildingdata as $building)
	{
		$players[$player['username']][$player['townname']]['Buildings'][$building['building_id']] =
			array('Workers' => $building['workers'], 'Level' => $building['level'], 'Buildingtype' => $building['buildingtype']);
	}
	
	$armydata = $sql->query('SELECT army_id, armyname, strength FROM Armies WHERE hometown = :0;', array($player['townname']));
	$players[$player['username']][$player['townname']]['Armies'] = array();
	foreach($armydata as $army)
	{
		$players[$player['username']][$player['townname']]['Armies'][$building['army_id']] =
			array('Armyname' => $building['armyname'], 'Strength' => $building['strength']);
	}
	
	$players[$player['username']][$player['townname']]['Treaties'] = array();
	$treatydata = $sql->query('SELECT user1, expiry_time FROM PeaceTreaty WHERE user2 = :0;', array($player['username']));
	foreach($treatydata as $treaty)
	{
		$players[$player['username']][$player['townname']]['Treaties'][$treaty['user1']] = $treaty['expiry_time'];
	}
	$treatydata = $sql->query('SELECT user2, expiry_time FROM PeaceTreaty WHERE user1 = :0;', array($player['username']));
	foreach($treatydata as $treaty)
	{
		$players[$player['username']][$player['townname']]['Treaties'][$treaty['user2']] = $treaty['expiry_time'];
	}
}*/

/*if($success && $fillBalaceSettings && $fillTimetable && $peaceTreatySuccess){
    echo json_encode(
        array('message' => 'Tables Created Successfully')
    );
} else {
    echo json_encode(
        array('message' => 'Tables Not Created Successfully')
    );
}*/
