<?php
// Setup AutoLoader
include_once('MOGUL/AutoLoader.php');
new AutoLoader();

// Page Header
$page = new Page(new SavageryInfo(), 'Admin Tools', true);
$page->print_header('Savagery - Admin Tools of Destruction');

// Redirect Muggles
if(!in_array($_SESSION['username'], constant('ADMINS'), true))
	{
	header('Location: ' . constant('OVERVIEW_PAGE'));
	exit();
	}

// HttpHelper for API calls
$http = new HttpHelper();

// Process Commands
if(!empty($_GET['action']))
	{
	// Database Management
	if($_GET['action'] === 'killeverything')
		{

        //API calls to DB
        $http->get("DBdestroy");
        $http->get("DBinit");

		    /*
		// Delete Database
		$database->query('DROP DATABASE ' . constant('GAME_TITLE') . ';');
		
		// Rebuild Database
		$dblink = new PDO('mysql:host=' . constant('DB_ADDRESS') . ';', constant('DB_USER'), constant('DB_PASSWORD'),
			array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$query = $dblink->prepare('CREATE DATABASE IF NOT EXISTS ' . constant('GAME_TITLE') . ';');
		$query->execute();
		$query = $dblink->prepare('USE ' . constant('GAME_TITLE') . ';');
		$query->execute();


		// Database Connection
		$database = new Database();
		
		// Define Timestamps
		$timestamps = array(array('User_Reset', time()), array('Game_Start', time()));
		// Define Balance Settings
		$settings = array(array('Map_Size', '100'), array('Start_Gold', '100'), array('Start_Tax', '5'), array('Start_Population', '5'));

		// Create Tables
		// Administration Tables
		$database->query('CREATE TABLE IF NOT EXISTS Timetable (
			timename VARCHAR(16),
			record INT,
			PRIMARY KEY (timename)
			);');
		$database->query('CREATE TABLE IF NOT EXISTS Users (
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
			armyname INT,
			strength INT,
			hometown VARCHAR(32),
			PRIMARY KEY (army_id),
			FOREIGN KEY (hometown) REFERENCES Towns(townname)
			);');

		// Save Timestamps
		$database->query('INSERT INTO Timetable (timename, record) VALUES (:0, :1);', $timestamps);
		// Save Balance Settings
		$database->query('INSERT INTO BalanceSettings (settingname, value) VALUES (:0, :1);', $settings);*/
		}
	}

// Print Errors
$page->print_errors();

// Print Management Options
// Database Management
$page->print_heading('Database Management');
$page->print_link('administration.php?action=killeverything', 'Kill Everything', true, '10px');
$page->print_newline();

// Page Footer
$page->print_footer();
?>