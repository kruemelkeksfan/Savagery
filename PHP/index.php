<?php
// Setup AutoLoader
include_once('MOGUL/AutoLoader.php');
new AutoLoader();

// Page Header
$page = new Page(new SavageryInfo(), 'Overview', true);
$page->print_header();

// Setup Database Connection
//$database = $page->get_database();

$page->print_text('Current Population: ' . 10);
$page->print_text('Current Tax Income: ' . 10 * 5 . '$');

$taxform = new Form('index.php'/*?action=settax*/,
	'post', $page, 'Set Tax per Head', array('formcolumn width150px', 'formcolumn  width150px'));
$taxform->add_field('', true, 'number', 5/*currenttax*/, true, 1, 'width50px',
	0, 10/*maxtax*/);
$taxform->add_column_break();
$taxform->add_submit('Set Tax');
$taxform->print();

$page->print_text('Next Payday: ' . '15:03'/*Calculate and display real time*/);

$paydayform = new Form('index.php'/*?action=collect*/,
	'post', null, null, array('formcolumnnarrow');
$paydayform->add_submit('Collect Taxes');
$paydayform->print();

// Page Footer
$page->print_footer();
?>