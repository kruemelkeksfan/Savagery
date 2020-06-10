<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../../MongoDatabase.php';

$database = new MongoDatabase();

$data = json_decode(file_get_contents("php://input"), true);

if($data['username'] != "")
{
    $username = $data['username'];
    $password = $data['password'];

	$userdata = $database->find_document('Userdata', array('username' => $username), array('projection'=>array('_id'=>0, 'password'=>1)));
    if (empty($userdata) || count($userdata) === 0)
	{
	    if(empty($data['gold']))
		{
	        $gold = $database->find_document('BalanceSettings', [], array('projection'=>array('_id'=>0, 'Start_Gold'=>1)))[0]['Start_Gold'];
	    }
		else
		{
	        $gold = $data['gold'];
	    }

	    $result = $database->add_document('Userdata', array('username'=>$username, 'password'=>$password, 'gold'=>$gold));
		
        echo json_encode(array('message' => 'User Created', 'success' => true));
    }
	else
	{
        echo json_encode(array('message' => 'User Not Created', 'success' => false));
    }
}
else
{
    echo json_encode($data);
}