<?php
// Setup AutoLoader
include_once('MOGUL/AutoLoader.php');
new AutoLoader();

//HttpHelper
$http = new HttpHelper();

// Page Header
$page = new Page(new SavageryInfo(), 'Your Town', true);
$page->print_header();

// Retrieve Info
//$types = $http->get("Buildingtypes/get_buildingtypes.php")[0];
$buildings = $http->post("Buildings/post_get_building_values.php", array('username' => $_SESSION['username']));
$gold = $http->post('User/post_get_gold.php', array('username' => $_SESSION['username']))['gold'];
//$town = $http->post('Towns/post_get_town_values.php', array('username' => $_SESSION['username']))[0];
$next_id = count($buildings);
//var_dump($next_id);
//var_dump($buildings);
//var_dump($http->get("Buildings/get_all_buildings.php"));

// Count Workers
$workers = $http->post('Buildings/post_get_sum_workers.php', array('username' => $_SESSION['username']))[0]['sum(workers)'];
/*foreach($buildings as $building)
	{
	$workers += $building['workers'];
	}*/

$action = InputHelper::get_get_string('action', null);
$building = InputHelper::get_get_string('building', null);
$workerinput = InputHelper::get_post_int('field_0_0', null);
$buildingtype = InputHelper::get_get_string('buildingtype', null);
$cost = InputHelper::get_get_int('cost', null);

if(!empty($action))
	{
	if($action === 'upgrade' && !empty($building))
		{
		if($gold >= 100)
			{
			$http->post('User/post_subtract_gold.php', array('username' => $_SESSION['username'], 'value' => 100));
			$levelneu = $http->post('Buildings/post_set_level.php', array('username' => $_SESSION['username'], 'building_id' => $building));
			var_dump($levelneu);
			}
		else
			{
			$page->add_error('You need at least 100$ to upgrade a Building!');
			}
		}
	else if($action === 'setworkers' && !empty($building) && !empty($workerinput))
		{
		if($workerinput <= ($town['population'] - $workers))
			{
			$newworkers = $http->post('Buildings/post_set_workers.php', array('username' => $_SESSION['username'],'building_id'=>$building, 'workers'=>$workerinput));
			var_dump($newworkers);
			}
		else
			{
			$page->add_error('Not enough free Workers!');
			}
		}
	else if($action === 'construct' /*&& !empty($buildingtype) && !empty($cost)*/)
        {
        if ($gold-$cost >= 0)
            {
             $new=$http->post("Buildings/post_new_building.php",
                 array('building_id'=>$next_id, 'buildingtype' =>$buildingtype, 'username'=>$_SESSION['username']));
             $new_gold = $http->post("User/post_substract_gold.php", array('username'=>$_SESSION['username'], 'value' =>$cost))['gold'];
            var_dump($new);
            }
        }
	}

// Retrieve Info
$types = $http->get("Buildingtypes/get_buildingtypes.php")[0];
$buildings = $http->post("Buildings/post_get_building_values.php", array('username' => $_SESSION['username']));
$town = $http->post('Towns/post_get_town_values.php', array('username' => $_SESSION['username']))[0];
$next_id = count($buildings);


// General Info
$page->print_text('Current Gold: ' . $gold . '$');
$page->print_text('Current Population: ' . $town['population']);
$page->print_text('Current unemployed Population: ' . ($town['population'] - $workers));

// Building Table
$buildingtable = new Table($page, 'Upgrades', array('tablecolumn width200px'));
$buildingtable->add_columns('ID', 'Building', 'Level', 'Workers', 'Upgrade', 'Set Workers');

foreach($buildings as &$building)
	{
	$upgradeform = new Form('town.php?action=upgrade&building=' . $building['building_id'],
		'post', null, null, array('formcolumnsingle'));
	$upgradeform->add_submit('Upgrade');

	$workerform = new Form('town.php?action=setworkers&building=' . $building['building_id'],
		'post', null, null, array('formcolumn width100px', 'formcolumn width100px'));
	$workerform->add_field('', true, 'number', $types[$building['buildingtype']]['maxworkers'], true, 1, 'width50px',
		0, $types[$building['buildingtype']]['maxworkers']);
	$workerform->add_column_break();
	$workerform->add_submit('Set Workers');

	$building = array_values($building);

	$building[] = $upgradeform;
	$building[] = $workerform;
	}

$buildingtable->add_data($buildings);
$buildingtable->print();

// Construction Table
$constructiontable = new Table($page, 'Construction', array('tablecolumn width200px'));
$constructiontable->add_columns('Building', 'Effect', 'Max Workers', 'Cost', 'Build');

//var_dump($types);
foreach($types as &$row)
	{
    $constructionform = new Form('town.php' . '?action=construct&buildingtype=' . $row['buildingtypename'] . '&cost=' . $row['cost'],
        'post', null, null, array('formcolumn width100per'));
    $constructionform->add_submit('Build');
    $row = array_values($row);
    $row[] = $constructionform;
	}

$constructiontable->add_data($types);
$constructiontable->print();

// Page Footer
$page->print_footer();
?>