<?php
class Map
	{
	const MODE_4 = 0;
	const MODE_8 = 1;
	const MODE_RECT = 2;
	const MODE_TAXICAB = 3;
	const MODE_CIRCLE = 4;
	const MODE_CROSS = 5;
	const MODE_8_CROSS = 6;
	const MODE_U = 7;
	const MODE_SHORT_U = 8;
	const MODE_CUSTOM = 9;
	const OPERATOR_EQUAL = 0;
	const OPERATOR_LOWER_EQUAL = 1;
	const OPERATOR_GREATER_EQUAL = 2;
	
	private $database;
	private $mapwidth;
	private $mapheight;
	private $sealevel;
	private $map;
		
	private function __construct(Database $database)
		{
		$this->database = $database;
		$this->map = array();
		}
	
	// Construct new Map for MapGeneration	
	static function init(Database $database, int $mapwidth, int $mapheight, int $sealevel = 0)
		{
		$map = new Map($database);
			
		$map->mapwidth = $mapwidth;
		$map->mapheight = $mapheight;
		$map->sealevel = $sealevel;
		
		return $map;
		}
	
	// Load existing Map from Database, MapGeneration Methods are not supported
	static function load(Database $database)
		{
		$map = new Map($database);
			
		$map->sealevel = null;
		$layers = Map::load_layers($database);
		$mapdata = $database->query('SELECT x, y FROM Map WHERE y=:0;', array(0));
		$map->mapwidth = count($mapdata);
		$mapdata = $database->query('SELECT * FROM Map;');
		$map->mapheight = ($map->mapwidth > 0) ? (count($mapdata) / $map->mapwidth) : 0;
	
		foreach($layers as $layer)
			{
			$map->map[$layer] = array();
			for($y = 0; $y < $map->mapheight; ++$y)
				{
				$map->map[$layer][$y] = array();
				for($x = 0; $x < $map->mapwidth; ++$x)
					{
					$map->map[$layer][$y][$x] = 0;
					}
				}
			}

		foreach($mapdata as $cell)
			{
			foreach($layers as $layer)
				{
				if($cell[$layer] !== '0')
					{
					$map->set_tile($layer, array($cell['x'], $cell['y']), $cell[$layer]);
					}
				}
			}
			
		return $map;
		}
	
	static function load_layers(Database $database)
		{
		$layerdata = $database->query('SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema=:0 AND table_name=:1;',
			array(constant('GAME_TITLE'), 'Map'));
		$layers = array();
		for($i = 2; $i < count($layerdata); ++$i)		// Skip x and y Columns
			{
			$layers[] = $layerdata[$i]['COLUMN_NAME'];
			}
	
		return $layers;
		}
		
	// TODO: Move into a static unified method	
	static function load_resources(Database $database)
		{
		$resourcedata = $database->query('SELECT resourcename FROM Resources;', array());
		$resources = array();
		foreach($resourcedata as $resource)
			{
			$resources[] = $resource['resourcename'];
			}

		return $resources;
		}
		
	function create_layer(string $layer)
		{	
		$this->map[$layer] = array();
		for($y = 0; $y < $this->mapheight; ++$y)
			{
			$this->map[$layer][] = array();
			for($x = 0; $x < $this->mapwidth; ++$x)
				{
				$this->map[$layer][$y][$x] = 0;
				}
			}
		}
		
	function save()
		{	
		$layers = array_keys($this->map);
		if(count($layers) <= 0)
			{
			ErrorHandler::handle_error('No Layers to save in Database on this Map!');
			}
		
		$tiles = array();
		for($y = 0; $y < $this->mapheight; ++$y)
			{
			for($x = 0; $x < $this->mapwidth; ++$x)
				{
				$tiles[] = array($x, $y);
				foreach($this->map as $layer => $tile)
					{
					$tiles[$y * $this->mapwidth + $x][] = $this->map[$layer][$y][$x];
					}
				}
			}
			
		$querystring = 'INSERT INTO Map (x, y';
		foreach($layers as $layer)
			{
			$querystring .= ', ' . $layer;
			}
		$querystring .= ') VALUES (:0, :1';
		for($i = 2; $i < (count($layers) + 2); ++$i)
			{
			$querystring .= ', :' . $i;
			}
		$querystring .= ');';
		$this->database->query($querystring, $tiles);
		}
		
	function print(Page $page, array $layers, array $showlayers, bool $clickable = false, array $player = array(-1, -1), array $selected = array(-1, -1),
		string $mapclass = 'table', string $cellclass = 'mapcell', string $borderclass = 'noborder')
		{	
		// Format Map
		// TODO: Make Colors a User Setting and read them in from Database
		$terraincolors = array();
		$terraincolors[] = '#FF8800';				// DEBUG
		$terraincolors[] = '#000088';				// Sea
		$terraincolors[] = '#228800';				// Plains
		$terraincolors[] = '#886644';				// Hills
		$terraincolors[] = '#666666';				// Mountains

		$layercolors = array();
		$layercolors[$layers[0]] = '#0088FF';		// River
		$layercolors[$layers[1]] = '#446600';		// Berries
		$layercolors[$layers[2]] = '#004488';		// Fish
		$layercolors[$layers[3]] = '#224400';		// Wood
		$layercolors[$layers[4]] = '#AA4422';		// Iron Ore
		$layercolors[$layers[5]] = '#CC8800';		// Gold Ore

		$cellstring = '<div style="background-color:%s;">%s</div>';
		if($clickable)
			{
			$cellstring = $page->generate_link('%s?x=%s&y=%s%s', $cellstring, false, 'hiddenlink');
			
			$mapsite =  $_SERVER['PHP_SELF'];
			$gets = '';
			foreach($_GET as $getindex => $get)
				{
				if($getindex != 'x' && $getindex != 'y' && $getindex != 'field_1_0' && $getindex != 'field_1_2')	// Exclude fields which can be used by hidden 
																													// Inputs to convey x and y in a GET request
					{
					$gets .= '&' . $getindex . '=' . $get;
					}
				}
			}
			
		$outputmap = array();
		for($y = 0; $y < $this->get_height(); ++$y)
			{
			$outputmap[] = array();
			for($x = 0; $x < $this->get_width(); ++$x)
				{
				$colors = array($terraincolors[$this->get_tile('terrain', array($x, $y))]);
				foreach($layers as $layer)
					{
					if($showlayers[$layer] && $this->get_tile($layer, array($x, $y)) !== 0)
						{
						$colors[] = $layercolors[$layer];	// Add twice to highlighten Layer against Background
						$colors[] = $layercolors[$layer];
						}
					}
	
				$text = '&emsp;';	
				if($this->get_tile('towns', array($x, $y)) !== 0)
					{
					$text = $this->get_tile('towns', array($x, $y));
					}
				if($x == $player[0] && $y == $player[1])
					{
					$text = 'X';
					}
				if($x == $selected[0] && $y == $selected[1])
					{
					$text = '[' . $text . ']';
					}
	
				if($clickable)
					{
					$outputmap[$y][$x] = sprintf($cellstring, $mapsite, $x, $y, $gets, Color::mix($colors), $text);
					}
				else
					{
					$outputmap[$y][$x] = sprintf($cellstring, Color::mix($colors), $text);
					}
				}
			}

		// Print Map
		$maptable = new Table(null, null, $mapclass, $cellclass, $borderclass, array('tablecolumntiny'));
		$maptable->add_data($outputmap);
		$maptable->print();
		}
		
	function print_tile_info(Page $page, array $coordinates, array $biomes = array(),
		string $tableclass = 'table', string $cellclass = 'tablecellleft',  string $borderclass = 'noborder')
		{
		$layers = array_keys($this->map);
		$tiledata = array();
		foreach($layers as $layer)
			{
			$tiledata[$layer] = $this->get_tile($layer, $coordinates);
			}
			
		if($tiledata['towns'] !== 0)
			{
			$towndata = $this->database->query('SELECT townname FROM Towns WHERE towntag=:0;', array($tiledata['towns']));
			$tiledata['towns'] = count($towndata) > 0 ? (Page::generate_link(('town.php?tag=' . $tiledata['towns']), $towndata[0]['townname'])) : 'none';
			}
		else
			{
			$tiledata['towns'] = 'none';
			}
			
		$tileinfo = array(array('Tile:', ($coordinates[0] . '|' . $coordinates[1])),
			array('Terrain:', (!empty($biomes[$tiledata['terrain']]) ? $biomes[$tiledata['terrain']] : 'unknown')),
			array('River:', (($tiledata['rivers'] !== 0) ? $tiledata['rivers'] : 'none')),
			array('Town:', $tiledata['towns']));
		for($i = 3; $i < count($layers); ++$i)
			{
			if($tiledata[$layers[$i]] == 0)
				{
				$tiledata[$layers[$i]] = 'none';
				}
			else if($tiledata[$layers[$i]] == 1)
				{
				$tiledata[$layers[$i]] = 'available';
				}
				
			$tileinfo[] = array((str_replace('_', ' ', ucfirst($layers[$i])) . ':'), $tiledata[$layers[$i]]);
			}
			
		$tiletable = new Table($page, 'Tile Info', $tableclass, $cellclass, $borderclass, array('tablecolumnmedium', 'tablecolumnmedium'));
		$tiletable->add_data($tileinfo);
		$tiletable->print();
		}
	
	function get_neighbours(array $coordinates, int $mode = self::MODE_4, $modeinfo = 1, bool $reportmapedge = false)
		{
		$directions = array();
		if($mode === self::MODE_4)
			{
			$directions[] = array(0, -1);
			$directions[] = array(1, 0);
			$directions[] = array(0, 1);
			$directions[] = array(-1, 0);
			}
		else if($mode === self::MODE_8)
			{
			$directions[] = array(0, -1);
			$directions[] = array(1, -1);
			$directions[] = array(1, 0);
			$directions[] = array(1, 1);
			$directions[] = array(0, 1);
			$directions[] = array(-1, 1);
			$directions[] = array(-1, 0);
			$directions[] = array(-1, -1);
			}
		else if($mode === self::MODE_RECT)
			{
			if(is_int($modeinfo))
				{
				for($y = -$modeinfo; $y <= $modeinfo; ++$y)
					{
					for($x = -$modeinfo; $x <= $modeinfo; ++$x)
						{
						if($x !== 0 || $y !== 0)
							{
							$directions[] = array($x, $y);
							}
						}
					}
				}
			else
				{
				ErrorHandler::handle_error('Non-Integer $modeinfo instead of Radius supplied to retrieve Neighbours!');
				}
			}
		else if($mode === self::MODE_TAXICAB)
			{
			if(is_int($modeinfo))
				{
				for($y = -$modeinfo; $y <= $modeinfo; ++$y)
					{
					for($x = -($modeinfo - abs($y)); $x <= ($modeinfo - abs($y)); ++$x)
						{
						if($x !== 0 || $y !== 0)
							{
							$directions[] = array($x, $y);
							}
						}
					}
				}
			else
				{
				ErrorHandler::handle_error('Non-Integer $modeinfo instead of Radius supplied to retrieve Neighbours!');
				}
			}
		else if($mode === self::MODE_CIRCLE)
			{
			if(is_int($modeinfo))
				{
				for($y = -$modeinfo; $y <= $modeinfo; ++$y)
					{
					for($x = -floor(sqrt(($modeinfo ** 2) - ($y ** 2))); $x <= floor(sqrt(($modeinfo ** 2) - ($y ** 2))); ++$x)
						{
						if($x !== 0 || $y !== 0)
							{
							$directions[] = array($x, $y);
							}
						}
					}
				}
			else
				{
				ErrorHandler::handle_error('Non-Integer $modeinfo instead of Radius supplied to retrieve Neighbours!');
				}
			}
		else if($mode === self::MODE_CROSS)
			{
			if(is_int($modeinfo))
				{
				for($y = -$modeinfo; $y <= $modeinfo; ++$y)
					{
					if($y !== 0)
						{
						$directions[] = array(0, $y);
						}
					}
				for($x = -$modeinfo; $x <= $modeinfo; ++$x)
					{
					if($x !== 0)
						{
						$directions[] = array($x, 0);
						}
					}
				}
			else
				{
				ErrorHandler::handle_error('Non-Integer $modeinfo instead of Radius supplied to retrieve Neighbours!');
				}
			}
		else if($mode === self::MODE_8_CROSS)
			{
			if(is_int($modeinfo))
				{
				$directions[] = array(-1, -1);
				$directions[] = array(1, -1);
				$directions[] = array(1, 1);
				$directions[] = array(-1, 1);
				
				for($y = -$modeinfo; $y <= $modeinfo; ++$y)
					{
					if($y !== 0)
						{
						$directions[] = array(0, $y);
						}
					}
				for($x = -$modeinfo; $x <= $modeinfo; ++$x)
					{
					if($x !== 0)
						{
						$directions[] = array($x, 0);
						}
					}
				}
			else
				{
				ErrorHandler::handle_error('Non-Integer $modeinfo instead of Radius supplied to retrieve Neighbours!');
				}
			}
		else if($mode === self::MODE_U)
			{
			if(is_array($modeinfo) && count($modeinfo) === 2 && is_int($modeinfo[0]) && is_int($modeinfo[1]))
				{
				for($y = -1; $y <= 1; ++$y)
					{
					for($x = -1; $x <= 1; ++$x)
						{
						if(($x !== 0 || $y !== 0) && array($x, $y) !== $modeinfo)
							{
							$directions[] = array($x, $y);
							}
						}
					}
				}
			else
				{
				ErrorHandler::handle_error('Need the excluded Point as $modeinfo when trying to retrieve Neighbours in MODE_U!');
				}
			}
		else if($mode === self::MODE_SHORT_U)
			{
			if(is_array($modeinfo) && count($modeinfo) === 2 && is_int($modeinfo[0]) && is_int($modeinfo[1]))
				{
				for($y = -1; $y <= 1; ++$y)
					{
					for($x = -1; $x <= 1; ++$x)
						{
						if(($x !== 0 || $y !== 0) && Vector2::taxicab_length(Vector2::subtract(array($x, $y), $modeinfo)) > 1)
							{
							$directions[] = array($x, $y);
							}
						}
					}
				}
			else
				{
				ErrorHandler::handle_error('Need the middle Point of the excluded Side as $modeinfo when trying to retrieve Neighbours in MODE_SHORTU!');
				}
			}
		else if($mode === self::MODE_CUSTOM)
			{
			if(is_array($modeinfo))
				{
				$directions = $modeinfo;
				}
			else
				{
				ErrorHandler::handle_error('Need a Direction Array as $modeinfo when trying to retrieve Neighbours in MODE_CUST!');
				}
			}
		else
			{
			ErrorHandler::handle_error('Unknown $mode ' . $mode . ' to retrieve neighbours!');
			}
		
		$neighbours = array();
		foreach($directions as $direction)
			{
			$neighbour = array($coordinates[0] + $direction[0], $coordinates[1] + $direction[1]);

			if($neighbour[0] >= 0 && $neighbour[0] < $this->mapwidth && $neighbour[1] >= 0 && $neighbour[1] < $this->mapheight)
				{
				$neighbours[] = $neighbour;
				}
			else if($reportmapedge)
				{
				$neighbours[] = null;
				}
			}
	
		return $neighbours;
		}
		
	function is_flooded(array $coordinates, bool $includerivers = false)
		{
		return ($this->get_tile('terrain', $coordinates) <= $this->sealevel) || ($includerivers && $this->get_tile('rivers', $coordinates) !== 0);
		}
	
	function get_coast_tiles(bool $includerivers = false)
		{
		$coasttiles = array();
		for($y = 0; $y < $this->get_height(); ++$y)
			{
			for($x = 0; $x < $this->get_width(); ++$x)
				{
				if(!$this->is_flooded(array($x, $y), $includerivers))
					{
					foreach($this->get_neighbours(array($x, $y)) as $neighbour)
						{
						if($this->is_flooded($neighbour, $includerivers))
							{
							$coasttiles[] = array($x, $y);
							break(1);
							}
						}
					}
				}
			}
		
		return $coasttiles;
		}
		
	function get_level_tiles(string $layer, int $level, int $operator = self::OPERATOR_EQUAL)
		{
		$leveltiles = array();
		for($y = 0; $y < $this->get_height(); ++$y)
			{
			for($x = 0; $x < $this->get_width(); ++$x)
				{
				$tile = $this->get_tile($layer, array($x, $y));
				if(($operator === self::OPERATOR_EQUAL && $tile === $level)
					|| ($operator === self::OPERATOR_LOWER_EQUAL && $tile <= $level)
					|| ($operator === self::OPERATOR_GREATER_EQUAL && $tile >= $level))
					{
					$leveltiles[] = array($x, $y);
					}
				}
			}
	
		return $leveltiles;
		}
	
	function get_width()
		{
		return $this->mapwidth;
		}

	function get_height()
		{
		return $this->mapheight;
		}
		
	function get_tile(string $layer, array $coordinates)
		{
		if(isset($this->map[$layer]))
			{
			if(isset($this->map[$layer][$coordinates[1]][$coordinates[0]]))
				{
				return $this->map[$layer][$coordinates[1]][$coordinates[0]];
				}
			else
				{
				ErrorHandler::handle_error('Map Coordinates (' . $coordinates[0] . '|' . $coordinates[1] . ') '
						. 'are unknown and can not be fetched from Map Layer ' . $layer . '!');
				}
			}
		else
			{
			ErrorHandler::handle_error('Map Layer ' . $layer . ' is unknown and can not be fetched!');
			}
		}
	
	final function set_tile(string $layer, array $coordinates, $tile)
		{
		if(isset($this->map[$layer]))
			{
			if(isset($this->map[$layer][$coordinates[1]][$coordinates[0]]))
				{
				$this->map[$layer][$coordinates[1]][$coordinates[0]] = $tile;
				}
			else
				{
				ErrorHandler::handle_error('Map Coordinates (' . $coordinates[0] . '|' . $coordinates[1] . ') '
					. 'are unknown and can not be set on Map Layer ' . $layer . '!');
				}
			}
		else
			{
			ErrorHandler::handle_error('Map Layer ' . $layer . ' is unknown and can not be set!');
			}
		}
	}