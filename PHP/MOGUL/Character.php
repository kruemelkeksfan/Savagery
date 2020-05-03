<?php
abstract class Character
	{
	// TODO: Move into a static unified method	
	static function load_stats(Database $database)
		{
		$statdata = $database->query('SELECT statname FROM CharacterStats;', array());
		$stats = array();
		foreach($statdata as $stat)
			{
			$stats[] = $stat['statname'];
			}
	
		return $stats;
		}
	
	// TODO: Move into a static unified method	
	static function load_skills(Database $database)
		{
		$skilldata = $database->query('SELECT skillname FROM CharacterSkills;', array());
		$skills = array();
		foreach($skilldata as $skill)
			{
			$skills[] = $skill['skillname'];
			}

		return $skills;
		}
		
	static function spawn(Database $database)
		{
		$towndata = $database->query('SELECT towntag FROM Towns;', array());
		if(count($towndata) > 0)
			{
			$town = $towndata[mt_rand(0, count($towndata) - 1)];
			$position = $database->query('SELECT x, y FROM Map WHERE towns=:0;', array($town['towntag']))[0];
			
			$stats = Character::load_stats($database);
			$skills = Character::load_skills($database);
			$querystring = 'INSERT INTO Characters (owner, charactername, age, hometown, capital, karma, x, y, action, action_started, main';
			$querydata = array($_SESSION['username'], $_SESSION['username'], 18, $town['towntag'], 0, 0, $position['x'], $position['y'], '', time(), 1);
			foreach($stats as $stat)
				{
				$querystring .= ', ' . $stat;
				$querydata[] = 1;
				}
			foreach($skills as $skill)
				{
				$querystring .= ', ' . $skill;
				$querystring .= ', ' . $skill . '_xp';
				$querydata[] = 1;
				$querydata[] = 0;
				}
			$querystring .= ') VALUES (:0, :1, :2, :3, :4, :5, :6, :7, :8, :9, :10';
			for($i = 11; $i < (count($stats) + (count($skills) * 2) + 11); ++$i)
				{
				$querystring .= ', :' . $i;
				}
			$querystring .= ');';
			$database->query($querystring, $querydata);
			
			$_SESSION['position'] = $town['towntag'];
				
			return array($position['x'], $position['y']);
			}
		else
			{
			ErrorHandler::handle_error('No Towns available to spawn Character!');
			return false;
			}
		}
		
	static function save_action(Database $database, string $action)
		{
		Character::update($database);
		$database->query('UPDATE Characters SET action=:0, action_started=:1 WHERE owner=:2 AND main=:3;',
			array($action, time(), $_SESSION['username'], 1));
		}
	
	static function update(Database $database)
		{
		$characterdata = $database->query('SELECT owner, charactername, x, y, action, action_started FROM Characters WHERE owner=:0 AND main=:1;',
			array($_SESSION['username'], 1));
		if(count($characterdata) > 0 && !empty($characterdata[0]['action']))
			{
			$character = $characterdata[0];
			$starttime = $character['action_started'];
			
			if(substr($character['action'], 0, 4) === 'Move')
				{
				$actiondata = explode(', ', $character['action']);
				$target = array($actiondata[1], $actiondata[2]);
				
				while($character['action_started'] < time())
					{	
					if($character['x'] == $target[0] && $character['y'] == $target[1])
						{
						$database->query('UPDATE Characters SET action=:0, action_started=:1 WHERE owner=:2 AND main=:3;',
							array('', time(), $_SESSION['username'], 1));
						break(1);
						}
					else
						{
						$dxs = array(0);
						$dys = array(0);
						if($character['x'] < $target[0])
							{
							$dxs[] = 1;
							}
						else if($character['x'] > $target[0])
							{
							$dxs[] = -1;
							}
						if($character['y'] < $target[1])
							{
							$dys[] = 1;
							}
						else if($character['y'] > $target[1])
							{
							$dys[] = -1;
							}
					
						$nexttiles = array();
						foreach($dys as $dy)
							{
							foreach($dxs as $dx)
								{
								if(!($dx == 0 && $dy == 0))
									{
									$nexttiles[] = array($character['x'] + $dx, $character['y'] + $dy);
									}
								}
							}
						
						$tilecostdata = $database->query('SELECT Map.x, Map.y, TerrainTypes.movementcost
							FROM Map INNER JOIN TerrainTypes ON Map.terrain=TerrainTypes.typeid
							WHERE Map.x=:0 AND Map.y=:1;', $nexttiles);
						$min = $tilecostdata[0][0]['movementcost'];
						$x = $tilecostdata[0][0]['x'];
						$y = $tilecostdata[0][0]['y'];
						foreach($tilecostdata as $tilecost)
							{
							if($tilecost[0]['movementcost'] < $min)
								{
								$min = $tilecost[0]['movementcost'];
								$x = $tilecost[0]['x'];
								$y = $tilecost[0]['y'];
								}
							}
						
						if($character['action_started'] + $min <= time())
							{
							$character['x'] = $x;
							$character['y'] = $y;
							$character['action_started'] = $character['action_started'] + $min;
							}
						else
							{
							$database->query('UPDATE Characters SET action_started=:0 WHERE owner=:1 AND main=:2;',
								array($character['action_started'], $_SESSION['username'], 1));
							break(1);
							}
						}
					}
				
				$database->query('UPDATE Characters SET x=:0, y=:1 WHERE owner=:2 AND main=:3;',
					array($character['x'], $character['y'], $_SESSION['username'], 1));
					
				$characterdata = $database->query('SELECT Characters.*, Map.towns FROM Characters
					INNER JOIN Map ON Characters.x=Map.x AND Characters.y=Map.y
					WHERE Characters.owner=:0 AND Characters.main=:1;', array($_SESSION['username'], 1));
				if(count($characterdata) > 0)
					{
					$_SESSION['position'] = $characterdata[0]['towns'];
					}
				}
			else
				{
				$actiondata = $database->query('SELECT resourcename, action, duration, tool FROM Resources WHERE action=:0;', array($character['action']));
				if(count($actiondata) > 0)
					{	
					$maxtime = intval($database->query('SELECT value FROM BalanceSettings WHERE settingname=:0;', array('Activity_Time_Max'))[0]['value']);
					$time = time() - $starttime;
					if($time > $maxtime)
						{
						$time = $maxtime;
						}
					
					$duration = $actiondata[0]['duration'];		// TODO: Apply Tool and Stats/Skills
					$amount = intdiv($time, $duration);			// TODO: Apply Tool and Stats/Skills
					$quality = 100;								// TODO: Apply Tool and Stats/Skills
					
					$items = array();
					for($i = 0; $i < $amount; ++$i)
						{
						// TODO: Update 'No Town'
						$items[] = array($actiondata[0]['resourcename'], $character['owner'], '0', $quality, -1);
						}
					$database->query('INSERT INTO Items (good, owner, location, quality, price) VALUES (:0, :1, :2, :3, :4);', $items);
					
					if($time < $maxtime && $amount > 0)
						{
						$database->query('UPDATE Characters SET action_started=:0 WHERE owner=:1 AND main=:2;',
							array($starttime + ($duration * $amount), $_SESSION['username'], 1));
						}
					else if($time >= $maxtime)
						{
						$database->query('UPDATE Characters SET action=:0, action_started=:1 WHERE owner=:2 AND main=:3;',
							array('', time(), $_SESSION['username'], 1));
						}
					}
				else
					{
					ErrorHandler::handle_warning('Tried to execute unknown Action ' . $character['action'] . '!');
					}
				}
			}
		else
			{
			Character::spawn($database);
			}
		}
		
	static function get_level_xp(int $currentlevel)
		{
		return ($currentlevel * 100) + (($currentlevel % 10) * 100);
		}
		
	static function get_position(Database $database)
		{
		Character::update($database);
			
		$positiondata = $database->query('SELECT x, y FROM Characters WHERE owner=:0 AND main=:1;', array($_SESSION['username'], 1));
		if(count($positiondata) > 0)
			{
			return array($positiondata[0]['x'], $positiondata[0]['y']);
			}
		else
			{
			return array(-1, -1);
			}
		}
	}
