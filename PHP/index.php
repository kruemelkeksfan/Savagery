<?php
// Setup AutoLoader
include_once('MOGUL/AutoLoader.php');
new AutoLoader();

// Page Header
$page = new Page(new SavageryInfo(), 'Overview', true);
$page->print_header();

// Setup Database Connection
$database = $page->get_database();



// Page Footer
$page->print_footer();
?>