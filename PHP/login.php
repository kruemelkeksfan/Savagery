<?php
// Setup AutoLoader
include_once('MOGUL/AutoLoader.php');
new AutoLoader();

$http = new HttpHelper();

// PAGE HEADER
$page = new Page(new SavageryInfo());
$page->print_header();

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
	$alphakey = InputHelper::get_post_string('Alpha-Key', '');
	// REGISTRATION
	if($action === 'register' && !empty($username) && !empty($password) && !empty($repeatpassword) && !empty($alphakey))
		{
		if($alphakey === 'GDPR')
			{
			if($password === $repeatpassword)
				{
				    //Todo: delete this
                    $test = $http->get("api_test.php");
                    echo "Test curl get: ". $test;
                    //call API using the HttpHelper
				$postdata = json_encode(array('username'=>$username, 'password'=>$password));
				var_dump($postdata);
				$created = $http->post('User/post_new_user.php', $postdata);
				$created = json_decode($created);
				var_dump($created);
				if($created['success'])
					{
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
		else
			{
			$page->add_error('This is a closed Alpha and you are not stinky enough to be invited!');
			}
		}
	// LOGIN
	else if($action === 'login' && !empty($username) && !empty($password))
		{
            //call API using the HttpHelper
		    $postdata = json_encode(array('username'=>$username, 'password'=>$password));
            $found = $http->post('User/post_find_user_pwd.php', $postdata);
            $found = json_decode($found);
            var_dump($found);

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
	}

// ERROR PANEL
$page->print_errors();

// REGISTRATION FORM
$registrationform = new Form('login.php?action=register' . (!empty($previouspage) ? ('&page=' . $previouspage) : ''), 'post', $page, 'New Account');
$registrationform->set_pattern(4, 16);
$registrationform->add_field('Username', true, 'text', '', true);
$registrationform->set_pattern(4, 64);
$registrationform->add_field('Password', true, 'password', '', true);
$registrationform->add_field('Repeat Password', true, 'password', '', true);
$registrationform->set_pattern(4, 16);
$registrationform->add_field('Alpha-Key', true, 'password', '', true);
$registrationform->add_submit('Register');
$registrationform->print();

// LOGIN FORM
$loginform = new Form('login.php?action=login' . (!empty($previouspage) ? ('&page=' . $previouspage) : ''), 'post', $page, 'Login');
$loginform->set_pattern(4);
$loginform->add_field('Username', true, 'text', '', true);
$loginform->add_field('Password', true, 'password', '', true);
$loginform->add_submit('Login');
$loginform->print();

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