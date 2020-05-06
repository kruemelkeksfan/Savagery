<?php
class Page
	{
	private $title;
	private $restricted;
	private $errors;
	private $httpHelper;
		
	function __construct(GameInfo $info, string $title = '', bool $restricted = false)
		{
		$this->title = $title;
		$this->restricted = $restricted;
		
		$this->errors = array();
		
		$this->httpHelper = new HttpHelper();
		}
		
	// TODO: Use in Map for Cells and other Locations where $_SERVER['PHP_SELF'] was used before
	static function get_current_site(array $excludedgets = array())
		{
		$site =  $_SERVER['PHP_SELF'];
		$gets = '';
		foreach($_GET as $getindex => $get)
			{
			if(!in_array($getindex, $excludedgets))
				{
				$gets .= '&' . $getindex . '=' . $get;
				}
			}
		if(strlen($gets) > 0)
			{
			return $site . '?' . $gets;
			}
		else
			{
			return $site;
			}
		}
		
	static function generate_link(string $link, string $text, bool $newline = false, string $class = '')
		{
		$linkstring = '';
		if($newline)
			{
			$linkstring .= '<br />';
			}
		$linkstring .= '<a href="' . $link . '" class="' . $class . '">' . $text . '</a>';
		
		return $linkstring;
		}
		
	function add_error(string $error)
		{
		$this->errors[] = $error;
		}
		
	function print_header(string $heading = null)
		{
		// PHP Settings
		ini_set('session.use_strict_mode', '1');
		ini_set('max_execution_time', 10);
		ignore_user_abort(true);
		
		// Start Session
		session_start();
		
		// Check Login Status
		if($this->restricted)
			{
			// On Login save first last_active Timestamp
			if(empty($_SESSION['last_active']))
				{
				$_SESSION['last_active'] = time();
				}
				
			// Verify Login and update last_active
			if(!empty($_SESSION['username']))
				{
				// Check if User was last_active before the User Table was wiped and act accordingly
				$userreset = $this->httpHelper->get('Timetable/get_record_reset.php');
				//var_dump($userreset);
				if($_SESSION['last_active'] <= $userreset['record'])
					{
					// Force-logout the User
					$_SESSION = array();
					}
				else
					{
					// Update last_active
					$_SESSION['last_active'] = time();
					$postdata =  json_encode(array($_SESSION['last_active'], $_SESSION['username']));
					$result = $this->httpHelper->post('User/post_last_active.php', $postdata);
					//var_dump($result);
					}
				}
			
			// Redirect User to Login if he is not logged in
			if(empty($_SESSION['username'])) // Double Check, since Session could be unset above
				{
				header('Location: ' . constant('LOGIN_PAGE') . '?page=' . $_SERVER['PHP_SELF']);
				exit();
				}

			$_SESSION['refresh'] = InputHelper::get_post_int('Auto-Refresh', (!empty($_SESSION['refresh']) ? $_SESSION['refresh'] : 0));
			}
		
		// Print HTML Header
		echo('<!DOCTYPE html>');
		echo('<html lang="en">');
		echo('<head>');
		echo('<title>' . constant('GAME_TITLE') . (!empty($this->title) ? (' - ' . $this->title) : '') . '</title>');
		echo('<link rel="stylesheet" type="text/css" href="MOGUL/default.css">');
		if(!empty(constant('STYLESHEET')))
			{
			echo('<link rel="stylesheet" type="text/css" href="' . constant('STYLESHEET') . '">');
			}
		if(!empty($_SESSION['refresh']))
			{
			echo('<meta http-equiv="refresh" content="' . $_SESSION['refresh'] . '" />');
			}
		echo('</head>');
		echo('<body>');
		
		// Print Page Header for restricted pages
		if($this->restricted)
			{
			echo('<div class="navbar">' . "\n");
			
			echo('<div class="navelementwide">');
			foreach(constant('NAVIGATION') as $title => $link)
				{
				if($link === substr($_SERVER['PHP_SELF'], -strlen($link)))
					{
					$title = '[' . $title . ']';
					}
				$this->print_link($link, $title, false, 'navlink');
				}
			echo('</div>' . "\n");
			
			echo('<div class="navelementwide">');
			/*
			$refreshform = new Form(Page::get_current_site(), 'post', null, null, array('formcolumnmedium', 'formcolumnnarrow'));
			$options = array(0 => 'No Refresh', 2 => '2 Seconds', 4 => '4 Seconds',
				12 => '12 Seconds', 24 => '24 Seconds', 48 => '48 Seconds');
			$refreshform->add_dropdown_field('Auto-Refresh', $options, true, true);
			$refreshform->add_column_break();
			$refreshform->add_submit('Refresh');
			$refreshform->print();
			if(!empty($_SESSION['refresh']))
				{
				$this->print_inline_text('(Currently ' . $_SESSION['refresh'] . ' Seconds)', 'smalltext');
				}
			*/
            echo('</div>' . "\n");
				
			echo('<div class="navelementnarrow">');
			$this->print_text('You are logged in as ' . $_SESSION['username']
				. ' (' . $this->generate_link(constant('LOGIN_PAGE') . '?action=logout', 'Logout') . ')');
            echo('</div>' . "\n");
				
            echo('</div>' . "\n");
			}
			
		if(!empty($heading))
			{
			$this->print_heading($heading, false);
			}
		}
		
	function print_heading(string $text, bool $subheading = true)
		{
		$heading = $subheading ? 'h4' : 'h2';
		echo('<' . $heading . '>' . $text . '</' . $heading . '>');
		}
		
	function print_text(string $text, string $class = 'text')
		{
		echo('<p class="' . $class . '">' . $text . '</p>');
		}
		
	function print_inline_text(string $text, string $class = 'text')
		{
		echo('<span class="' . $class . '">' . $text . '</span>');
		}
		
	function print_link(string $link, string $text, bool $newline = false, string $class = '')
		{
		echo(Page::generate_link($link, $text, $newline, $class));
		}
		
	function print_newline()
		{
		echo('<br /><br />');
		}
		
	function print_spacer(string $class = 'spacer')
		{
		echo('<div class="' . $class . '"' . $size . ';"> </div>');
		}
		
	function print_errors()
		{
		foreach($this->errors as $error)
			{
			echo('<p class="error">' . $error . '</p>');
			}
		}
		
	function print_footer()
		{
		$this->print_errors();
			
		echo('</body>');
		echo('</html>');
		}
		
	function get_database()
		{
		return $this->database;
		}
	}
?>