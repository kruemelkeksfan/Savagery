<?php
// Setup AutoLoader
include_once('MOGUL/AutoLoader.php');
new AutoLoader();

// Page Header
$page = new Page(new SavageryInfo(), 'Overview', true);
$page->print_header();

$http = new HttpHelper();

$town = $http->post('Towns/post_get_town_values.php', array('username' => $_SESSION['username']));
var_dump($town);

$action = InputHelper::get_get_string('action', null);
$tax = InputHelper::get_post_int('field_0_0', null);
if(!empty($action))
	{
	if($action === 'collect')
		{
		$http->post('User/post_add_gold.php', array('username' => $_SESSION['username'], 'value' => ($town['population'] * $town['tax'])));
		}
	else if($action === 'settax' && !empty($tax))
		{
		$http->post('Towns/post_set_tax.php', array('username' => $_SESSION['username'], 'tax' => $tax));
		}
	}

$gold = $http->post('User/post_get_gold.php', array('username' => $_SESSION['username']))['gold'];
var_dump($http->post('User/post_get_gold.php', array('username' => $_SESSION['username'])));

$page->print_text('Current Gold: ' . $gold . '$');
$page->print_text('Current Population: ' . $town['population']);
$page->print_text('Current Tax Income: ' . ($town['population'] * $town['tax']) . '$');

$taxform = new Form('index.php?action=settax',
	'post', $page, 'Set Tax per Head', array('formcolumn width150px', 'formcolumn  width150px'));
$taxform->add_field('', true, 'number', $tax, true, 1, 'width50px', 0);
$taxform->add_column_break();
$taxform->add_submit('Set Tax');
$taxform->print();

$paydayform = new Form('index.php?action=collect',
	'post', null, null, array('formcolumnnarrow'));
$paydayform->add_submit('Collect Taxes');
$paydayform->print();

// Page Footer
$page->print_footer();
?>