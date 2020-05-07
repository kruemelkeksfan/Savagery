<?php
// Setup AutoLoader
include_once('MOGUL/AutoLoader.php');
new AutoLoader();

//Helper for API calls
$http = new HttpHelper();

// Page Header
$page = new Page(new SavageryInfo(), 'Your Army', true);
$page->print_header();

$recruitmentfee = 5;
$troopsize = InputHelper::get_post_int('troopsize', 1); //ToDo
$armyname = InputHelper::get_post_string('armyname', '');


$action = InputHelper::get_get_string('action', '');
if(!empty($action)) {
    // LOGOUT
    if ($action === 'recruit') {
        //get Gold
        $gold = $http->post('User/post_get_gold.php', array('username' => $_SESSION['username']))['gold'];
        $fee = $troopsize * $recruitmentfee;

        if ($gold-$fee >= 0){
            $new_gold = $http->post("User/post_substract_gold.php", array('username'=>$_SESSION['username'], 'value' =>$fee))['gold'];
            //var_dump($new_gold, $troopsize);
            $http->post('Armies/post_new_army.php', array('armyname'=>$armyname, 'strength'=>$troopsize,
                'username'=>$_SESSION['username']));
        } else {
            echo "You're too poor to feed all those soldiers, man!";
        }
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
	'post', $page, "Recruitment", array('formcolumn width150px', 'formcolumn width150px'));
$recruitform->add_field('armyname', true, 'text', "YourArmyNameHere");
$recruitform->add_field('troopsize', true, 'number', 1, true, 1, 'width50px', 0);
$recruitform->add_column_break();
$recruitform->add_submit('Recruit Army');
$recruitform->print();

$page->print_text('Cost per Soldier: ' . $recruitmentfee . '$');

// Page Footer
$page->print_footer();
?>