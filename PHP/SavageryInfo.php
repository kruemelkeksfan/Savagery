<?php
// Setup AutoLoader
include_once('MOGUL/AutoLoader.php');
new AutoLoader();

class SavageryInfo extends GameInfo
	{
	function __construct()
		{
		parent::__construct('Savagery',
			'127.0.0.1', 'Savagery', 'qhB4gdpf',
			'login.php', 'index.php',
			array('Overview' => 'index.php'),
			array('kruemelkeksfan', 'kruemel'), 'log.txt');
		}
	}
?>