<?php
// Setup AutoLoader
include_once('MOGUL/AutoLoader.php');
new AutoLoader();

// Page Header
$page = new Page(new SavageryInfo(), 'Your Town', true);
$page->print_header();

// Setup Database Connection
$database = $page->get_database();

$armytable = new Table($page, 'Your Armies', array('tablecolumn width200px'));
$armytable->add_columns('ID', 'Name', 'Strength', 'Split', 'Merge', 'Attack');

$splitform = new Form('armies.php'/*?action=split*/,
	'post', null, null, array('formcolumn width150px', 'formcolumn width150px'));
$splitform->add_field('', true, 'number', floor(10/*armystrength*/ / 2), true, 1, 'width50px', 0, 10/*armystrength*/);
$splitform->add_column_break();
$splitform->add_submit('Split Army');

$mergeform = new Form('armies.php'/*?action=merge*/,
	'post', null, null, array('formcolumn width150px'));
$mergeform->add_submit('Merge Army');

$attackform = new Form('armies.php'/*?action=attack*/,
	'post', null, null, array('formcolumn width150px'));
$attackform->add_submit('Attack');

$armytable->add_data(array(array('1', 'Royal Guard', 10, $splitform, $mergeform, $attackform)));
$armytable->print();

$recruitform = new Form('armies.php'/*?action=recruit*/,
	'post', null, null, array('formcolumn width150px', 'formcolumn width150px'));
$recruitform->add_field('', true, 'number', 1, true, 1, 'width50px', 0);
$recruitform->add_column_break();
$recruitform->add_submit('Recruit Army');

$page->print_text('Cost per Soldier: ' . 5 . '$');

// Page Footer
$page->print_footer();
?>