<?php
// Setup AutoLoader
include_once('MOGUL/AutoLoader.php');
new AutoLoader();

//Helper for API calls
$http = new HttpHelper();

// Page Header
$page = new Page(new SavageryInfo(), 'Your Army', true);
$page->print_header();

$gold = $http->post('User/post_get_gold.php', array('username' => $_SESSION['username']))['gold'];

$recruitmentfee = 5;
$troopsize = InputHelper::get_post_int('troopsize', 1); //ToDo
$armyname = InputHelper::get_post_string('armyname', '');

$army_strength = $http->post('Armies/post_get_sum_soldiers.php', array('username' => $_SESSION['username']))[0]['strength'];
//$range_mult = $http->post('BalanceSettings/post_get_setting.php', array('value'=>'Range_Multiplier'))[0]['value'];
//$range = $army_strength * $range_mult;
//$position = $http->post('Towns/post_get_town_values.php', array('username' => $_SESSION['username']))[0]['position'];
//$mapsize = $http->post('BalanceSettings/post_get_setting.php', array('value'=>'Map_Size'))[0]['value'];
//$min = max(0,$position-$range);
//$max = min($mapsize, $position+$range);

//var_dump($min, $max);

//$targets = $http->post('Towns/post_get_towns_in_range.php', array('max'=>$max,'min'=>$min));
var_dump($targets);

$action = InputHelper::get_get_string('action', '');
if(!empty($action))
	{
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
        	}
		else
			{
            echo "You're too poor to feed all those soldiers, man!";
       		}
    	}
	else if($action === 'attack')
		{
		// Elaborate Use Case
		$army = InputHelper::get_get_int('army', null);
		$targetowner = InputHelper::get_post_string('Target', null);
		if(!empty($army) && !empty($targetowner))
			{
			$attacker = $http->post('Armies/post_get_army_values.php', array('army_id'=>$army));
			$defenders = $http->post('Armies/post_get_all_army_values.php', array('username'=>$targetowner));

			$attackbuildings = $http->post('Buildings/post_get_specific_building_values.php', array('username'=>$_SESSION['username'], 'buildingtype'=>'Tavern'));
			$defensebuildings = $http->post('Buildings/post_get_specific_building_values.php', array('username'=>$targetowner, 'buildingtype'=>'Blacksmith'));

			$attackbonus = 0;
			foreach($attackbuildings as $b)
				{
				$attackbonus += $b['level'];
				}
			$defensebonus = 0;
			foreach($defensebuildings as $b)
				{
				$defensebonus += $b['level'];
				}

			$attackstrength = $attacker['strength'] * $attackbonus;
			$defensestrength = 0;
			foreach($defenders as $d)
				{
				$defensestrength += $d['strength'] * $defensebonus;
				}

			$tabletitle = 'Attack Results';
			$spoils = 0;
			if($attackstrength > $defensestrength)
				{
				// Adjust Result Table
				$tabletitle = 'Attack Results - Victory';

				// Loothochdruck
				$spoils = $http->post('User/post_get_gold.php', array('username' => $targetowner))['gold'];
				$http->post('User/post_subtract_gold.php', array('username' => $targetowner, 'value' => $spoils));
				$http->post('User/post_add_gold.php', array('username' => $_SESSION['username'], 'value' => $spoils));

				// Kill all Defenders
				$http->post('Armies/post_kill_armies.php', array('username' => $targetowner));
				}
			else
				{
				// Adjust Result Table
				$tabletitle = 'Attack Results - Defeat';

				// Kill attacking Army
				$http->post('Armies/post_kill_army.php', array('username' => $army));
				}

			$resulttable = new Table($page, $tabletitle, array('tablecolumn width200px', 'tablecolumn width200px'));
			$results = array(array('Target User', $targetowner),
				array('Attackers Strength', $attackstrength),
				array('Defenders Strength', $defensestrength),
				array('Spoils of War', $spoils . '$'));
			$resulttable->add_data($results);
			}
		}
	}

// Print Results
if(!empty($resulttable))
	{
	$resulttable->print();
	}

// General Info
$page->print_text('Current Gold: ' . $gold . '$');

$armytable = new Table($page, 'Your Armies', array('tablecolumn width200px'));
$armytable->add_columns('ID', 'Name', 'Strength', /*'Split', 'Merge',*/ 'Attack');

//Get Army Data
$armies = $http->post("Armies/post_get_army_values.php", array('username'=>$_SESSION['username']));
foreach ($armies as &$row){
//    $splitform = new Form('armies.php'/*?action=split*/,
//        'post', null, null, array('formcolumn width150px', 'formcolumn width150px'));
//    $splitform->add_field('', true, 'number', floor(10/*armystrength*/ / 2), true, 1, 'width50px', 0, 10/*armystrength*/);
//    $splitform->add_column_break();
//    $splitform->add_submit('Split Army');
//
//    $mergeform = new Form('armies.php'/*?action=merge*/,
//        'post', null, null, array('formcolumn width150px'));
//    $mergeform->add_submit('Merge Army');

    $attackform = new Form('armies.php?action=attack&army=' . $row['army_id'],
        'post', null, null, array('formcolumn width150px', 'formcolumn width150px'), 'form width300px');
	$position = $http->post("Towns/post_get_town_values.php", array('username'=>$_SESSION['username']))['position'];
	$range = $http->post("BalanceSettings/post_get_setting.php", array('settingname'=>$_SESSION['Attack_Range']));
	$targets = $http->post("Towns/post_get_towns_in_range.php", array('max' => ($position + $range), 'min' => ($position - $range)));
	$targetoptions = array();
	foreach($targets as $target)
		{
		$targetoptions[$target['townname']] = $target['owner'];
		}
	$attackform->add_dropdown_field('Target', $targetoptions, true, true);
	$attackform->add_column_break();
    $attackform->add_submit('Attack');

    $row = array_values($row);
//    $row[] = $splitform;
//    $row[] = $mergeform;
    $row[] = $attackform;
}

$armytable->add_data($armies);
$armytable->print();

$recruitform = new Form('armies.php?action=recruit',
	'post', $page, "Recruitment", array('formcolumn width150px', 'formcolumn width150px'));
$recruitform->add_field('armyname', true, 'text', "YourArmyNameHere");
$recruitform->add_field('troopsize', true, 'number', 1, true, 1, 'width50px', 0);
$recruitform->add_column_break();
$recruitform->add_submit('Recruit Army');
$recruitform->print();

$page->print_text('Cost per Soldier: ' . $recruitmentfee . '$');

$page->print_text('Current Troop Strength: ' . $army_strength);

// Page Footer
$page->print_footer();
?>