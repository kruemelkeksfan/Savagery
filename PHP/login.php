<?php

include_once('MOGUL/DBFiller.php');
// Setup AutoLoader
include_once('MOGUL/AutoLoader.php');
new AutoLoader();

$http = new HttpHelper();

// PAGE HEADER
$page = new Page(new SavageryInfo());

// Get previous Page
$previouspage = InputHelper::get_get_string('page', '');

$action = InputHelper::get_get_string('action', '');
if(!empty($action))
	{
	// LOGOUT
	if($action === 'logout')
		{
		session_unset();
		}	

	$username = InputHelper::get_post_string('Username', '');
	$password = InputHelper::get_post_string('Password', '');
	$repeatpassword = InputHelper::get_post_string('Repeat_Password', '');
	// REGISTRATION
	if($action === 'register' && !empty($username) && !empty($password) && !empty($repeatpassword))
		{
		if($password === $repeatpassword)
			{
			// Merge Start
			// $gold = $database->query('SELECT value FROM BalanceSettings WHERE settingname=:0;', array('Start_Gold'))[0]['value'];

            //call API using the HttpHelper
			$postdata = array('username'=>$username, 'password'=>password_hash($password, PASSWORD_DEFAULT));
			$created = $http->post('User/post_new_user.php', $postdata);
			if($created['success'])
			// Merge End
				{
				$town_created = $http->post("Towns/post_new_town.php", array('username' => $username));
				$townhall_built = $http->post("Buildings/post_new_building.php",
                    array('building_id'=>'0', 'buildingtype' =>'Townhall', 'username'=>$username));
				$guard_established = $http->post('Armies/post_new_army.php', array('armyname'=>'City Guard', 'strength'=>'10',
                    'username'=>$username));

                    //var_dump($town_created);
				// $mapsize = $database->query('SELECT value FROM BalanceSettings WHERE settingname=:0;', array('Map_Size'))[0]['value'];
				// $tax = $database->query('SELECT value FROM BalanceSettings WHERE settingname=:0;', array('Start_Tax'))[0]['value'];
				// $population = $database->query('SELECT value FROM BalanceSettings WHERE settingname=:0;', array('Start_Population'))[0]['value'];
				// $database->query('INSERT INTO Towns (townname, position, tax, population, owner) VALUES (:0, :1, :2, :3, :4);',
				//	array($username . 's Town', mt_rand(0, $mapsize - 1), $tax, $population, $username));
					
				$_SESSION['username'] = $username;

				header('Location: ' . (empty($previouspage) ? constant('OVERVIEW_PAGE') : $previouspage));
				exit();
				}
			else
				{
				$page->add_error('Tell me your name and I will tell you that it is already in use!');
				}
			}
		else
			{
			$page->add_error('Entering the same password twice is not exactly rocket science!');
			}
		}
	// LOGIN
	else if($action === 'login' && !empty($username) && !empty($password))
		{
            //call API using the HttpHelper
		    $postdata = array('username'=>$username, 'password'=>$password);
            $found = $http->post('User/post_find_user_pwd.php', $postdata);
            //var_dump($found);

		if($found['success'])
			{
			$_SESSION['username'] = $username;

			header('Location: ' . (empty($previouspage) ? constant('OVERVIEW_PAGE') : $previouspage));
			exit();
			}
		else
			{
			$page->add_error('This is a creative username and password combination, but maybe you should stick to something that works!');
			}
		}
	// Output Error Message if not all Form Fields are filled
	else if($action === 'register' || $action === 'login')
		{
		$page->add_error('The data leech is not satisfied by your sacrifice, feed him more to gain his approval!');
		}
	else if($action === 'init_DB'){
        $result = $http->get("DBinit.php");
        fillUsers($http);
        fillTowns($http);
        fillBuildings($http);
        fillArmies($http);

        /*$attackstrength = $http->post('BattleStats/post_get_attack_strength.php',
            array('buildingtype'=>'Blacksmith', 'army_id'=>'4'));

        $defensestrength = $http->post('BattleStats/post_get_defense_strength.php',
            array('buildingtype'=>'Tavern', 'username'=>'xxxNOOBxxx'));*/

    }
	else if($action === 'init_Mongo'){
	    var_dump($http->post('Armies/post_get_army_by_town.php', array('username'=>'xxxNOOBxxx')));
        $result = $http->changeDB();
        //var_dump($result);
		//var_dump($http->get('test.php'));
		var_dump($http->post('BalanceSettings/post_get_setting.php', array('settingname'=>'Map_Size')));
		var_dump($http->post("BalanceSettings/post_get_setting.php", array('settingname'=>'Range_Multiplier')));
    }
	}

$page->print_header();

// ERROR PANEL
$page->print_errors();

// REGISTRATION FORM
$registrationform = new Form('login.php?action=register' . (!empty($previouspage) ? ('&page=' . $previouspage) : ''), 'post', $page, 'New Account');
$registrationform->set_pattern(4, 16);
$registrationform->add_field('Username', true, 'text', '', true);
$registrationform->set_pattern(4, 64);
$registrationform->add_field('Password', true, 'password', '', true);
$registrationform->add_field('Repeat Password', true, 'password', '', true);
$registrationform->add_submit('Register');
$registrationform->print();

// LOGIN FORM
$loginform = new Form('login.php?action=login' . (!empty($previouspage) ? ('&page=' . $previouspage) : ''), 'post', $page, 'Login');
$loginform->set_pattern(4);
$loginform->add_field('Username', true, 'text', '', true);
$loginform->add_field('Password', true, 'password', '', true);
$loginform->add_submit('Login');
$loginform->print();

//DB-Fill Button
$dbButton = new Form('login.php?action=init_DB' . (!empty($previouspage) ? ('&page=' . $previouspage) : ''), 'post', $page);
$dbButton->add_submit('Initialize and fill DB');
$dbButton->print();

//Mongo-Fill Button
$dbButton = new Form('login.php?action=init_Mongo' . (!empty($previouspage) ? ('&page=' . $previouspage) : ''), 'post', $page);
$dbButton->add_submit('Initialize and migrate Data to Mongo');
$dbButton->print();

// Cookie Notice
$page->print_heading('A Word about Cookies');
$page->print_text('"Strictly necessary cookies — These cookies are essential for you to browse the website and use its features, such as accessing secure areas of the
	site. Cookies that allow web shops to hold your items in your cart while you are shopping online are an example of strictly necessary cookies. These cookies will
	generally be first-party session cookies. While it is not required to obtain consent for these cookies, what they do and why they are necessary should be explained
	to the user."');
$page->print_link('https://gdpr.eu/cookies', 'https://gdpr.eu/cookies (visited 26 Dec 2019)', false, '10px');
$page->print_text('Under default settings this website only uses session cookies to remember usernames while players are online. These cookies only remain valid until
	the browser is closed and are strictly necessary to protect the login-restricted areas of the site and therefore considered essential.');

// PAGE FOOTER
$page->print_footer();
?>