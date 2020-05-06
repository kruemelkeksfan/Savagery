<?php
// Setup AutoLoader
include_once('MOGUL/AutoLoader.php');
new AutoLoader();

//Helper for API calls
$http = new HttpHelper();

// Page Header
$page = new Page(new SavageryInfo(), 'Your Town', true);
$page->print_header();


$action = InputHelper::get_get_string('action', '');
if(!empty($action)) {
    // LOGOUT
    if ($action === 'recruit') {
        //get Inputs
        $troopsize = InputHelper::get_get_int('troopsize');
        $armyname = InputHelper::get_get_string('armyname');

        $http->post('Armies/post_new_army.php', array('armyname'=>$armyname, 'strength'=>$troopsize,
            'username'=>$_SESSION['username']));
    }
}

$armytable = new Table($page, 'Your Armies', array('tablecolumn width200px'));
$armytable->add_columns('ID', 'Name', 'Strength', 'Split', 'Merge', 'Attack');


//Get Army Data
$armies = $http->post("Armies/post_get_army_values.php", array('username'=>$_SESSION['username']));
foreach ($armies as &$row){
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

    $row = array_values($row);
    $row[] = $splitform;
    $row[] = $mergeform;
    $row[] = $attackform;
}


$armytable->add_data($armies); //array(array('1', 'Royal Guard', 10, $splitform, $mergeform, $attackform)));
$armytable->print();

$recruitform = new Form('armies.php?action=recruit',
	'post', null, "Recruitment", array('formcolumn width150px', 'formcolumn width150px'));
$recruitform->add_field('armyname', true, 'text', "YourArmyNameHere");
$recruitform->add_field('troopsize', true, 'number', 1, true, 1, 'width50px', 0);
$recruitform->add_column_break();
$recruitform->add_submit('Recruit Army');
$recruitform->print();

$page->print_text('Cost per Soldier: ' . 5 . '$');

// Page Footer
$page->print_footer();
?>