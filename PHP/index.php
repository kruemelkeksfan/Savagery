<?php
// Setup AutoLoader
include_once('MOGUL/AutoLoader.php');
new AutoLoader();

// Page Header
$page = new Page(new SavageryInfo(), 'Overview', true);
$page->print_header();

$http = new HttpHelper();

$towndata = $http->post('Towns/post_get_town_values.php', array('username' => $_SESSION['username']));
$town = empty($towndata[0]) ? $towndata : $towndata[0];

$action = InputHelper::get_get_string('action', null);
$tax = InputHelper::get_post_int('field_0_0', null);

if(!empty($action))
	{
	if($action === 'collect')
		{
		$http->post('User/post_add_gold.php', array('username' => $_SESSION['username'], 'value' => ($town['population'] * $town['tax'])));
		
		$towndata = $http->post('Towns/post_get_town_values.php', array('username' => $_SESSION['username']));
	    $town = empty($towndata[0]) ? $towndata : $towndata[0];
		}
	else if($action === 'settax' && !empty($tax) && $tax > 0)
		{
		$http->post('Towns/post_set_tax.php', array('username' => $_SESSION['username'], 'tax' => $tax));
		$pop = ceil(pow($tax, -1) * 50);
		$http->post('Towns/post_set_population.php', array('username' => $_SESSION['username'], 'population' => $pop));
		
		// Update Workers
		$workers = $http->post('Buildings/post_get_sum_workers.php', array('username' => $_SESSION['username']))[0]['sum(workers)'];
		
		$i = 0;
		while($workers > $town['population'] && $i < 100) // I feel so dirty, I will have to take a Shower after this
			{
			$http->post('Buildings/post_set_workers.php', array('username' => $_SESSION['username'], 'building_id' => $i++, 'workers' => 0));
			
            $workers = $http->post('Buildings/post_get_sum_workers.php', array('username' => $_SESSION['username']))[0]['sum(workers)'];
            }
			
		$towndata = $http->post('Towns/post_get_town_values.php', array('username' => $_SESSION['username']));
		$town = empty($towndata[0]) ? $towndata : $towndata[0];
		}
	}

$gold = $http->post('User/post_get_gold.php', array('username' => $_SESSION['username']))['gold'];
//var_dump($gold);

$page->print_heading($town['townname'], false);

$page->print_text('Current Gold: ' . $gold . '$');
$page->print_text('Current Population: ' . $town['population']);
$page->print_text('Current Tax Income: ' . ($town['population'] * $town['tax']) . '$');

$taxform = new Form('index.php?action=settax',
	'post', $page, 'Set Tax per Head', array('formcolumn width150px', 'formcolumn  width150px'));
$taxform->add_field('', true, 'number', $town['tax'], true, 1, 'width50px', 1);
$taxform->add_column_break();
$taxform->add_submit('Set Tax');
$taxform->print();

$page->print_text('<br />');

$paydayform = new Form('index.php?action=collect',
	'post', null, null, array('formcolumnnarrow'));
$paydayform->add_submit('Collect Taxes');
$paydayform->print();

// Page Footer
$page->print_footer();
?>