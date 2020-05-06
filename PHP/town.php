<?php
// Setup AutoLoader
include_once('MOGUL/AutoLoader.php');
new AutoLoader();

//HttpHelper
$http = new HttpHelper();

// Page Header
$page = new Page(new SavageryInfo(), 'Your Town', true);
$page->print_header();

$buildingtable = new Table($page, 'Upgrades', array('tablecolumn width200px'));
$buildingtable->add_columns('ID', 'Building', 'Level', 'Workers', 'Upgrade', 'Set Workers');

$upgradeform = new Form('town.php'/*?tag=' . $towntag . '&action=upgrade&building=' . $buildingindex*/,
	'post', null, null, array('formcolumnsingle'));
$upgradeform->add_submit('Upgrade');

$workerform = new Form('town.php'/*?tag=' . $towntag . '&action=setworkers&building=' . $buildingindex*/,
	'post', null, null, array('formcolumn width100px', 'formcolumn width100px'));
$workerform->add_field('', true, 'number', 4/*maxworkers*/, true, 1, 'width50px',
	0, 4/*maxworkers*/);
$workerform->add_column_break();
$workerform->add_submit('Set Workers');

$buildingtable->add_data(array(array('1', 'Blacksmith', '2', '4', $upgradeform, $workerform)));
$buildingtable->print();

//Get Buildingtypes
$types = $http->get("Buildingtypes/get_buildingtypes.php");
foreach ($types as $row){
    $constructionform = new Form('town.php' . '&action=construct&building=' . $types['buildingtypename'],
        'post', null, null, array('formcolumn width100per'));
    $constructionform->add_submit('Build');
    $row[3] = $constructionform;
}

$constructiontable = new Table($page, 'Construction', array('tablecolumn width200px'));
$constructiontable->add_columns('Building', 'Effect', 'Cost', 'Build');

$constructiontable->add_data($types); //array(array('Blacksmith', 'Increases the Attack Strength of all Armies of this Town.', '20', $constructionform)));
$constructiontable->print();

// Page Footer
$page->print_footer();
?>