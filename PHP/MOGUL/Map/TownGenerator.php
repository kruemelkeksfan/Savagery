<?php
class TownGenerator
	{
	private $towns;
		
	function __construct()
		{		
		$this->towns = array();
		}
	
	function generate_towns(Map $map, Database $database, int $coastaltowns, array $biometowns, int $townclearance, Page $page, bool $save = false)
		{
		// Create City Names and Indices
		$towncount = $coastaltowns;
		foreach($biometowns as $biometowncount)
			{
			$towncount += $biometowncount;
			}
		$size = intval($database->query('SELECT value FROM BalanceSettings WHERE settingname=:0;', array('Town_Population_Initial'))[0]['value']);
		$towns = array();
		for($i = 0; $i <= $towncount; ++$i)
			{
			// TODO: Give real Names to Towns
			$towns[$i] = ($i != 0 ? strval($i) : '0');
			if($save)
				{
				$townname = ($i != 0 ? ('Town ' . $i) : 'No Town');
				$database->query('INSERT INTO Towns (towntag, townname, population_poor , population_middle, population_rich,
					income_poor, income_middle, income_rich, satisfaction_poor, satisfaction_middle, satisfaction_rich)
					VALUES (:0, :1, :2, :3, :4, :5, :6, :7, :8, :9, :10);',
					array($towns[$i], $townname, $size, 0, 0, 0, 0, 0, '0.0, 0.0, 0.0, 0.0', '0.0, 0.0, 0.0, 0.0', '0.0, 0.0, 0.0, 0.0'));
				}
			}
		
		// Create Map Layer
		$map->create_layer('towns');
		
		// Generate Towns
		$offset = 1;
		
		$tiles = $map->get_coast_tiles(true);
		$offset = $this->generate_biome_towns($map, $tiles, $townclearance, $coastaltowns, $towns, $offset, $page);
		
		foreach($biometowns as $level => $biometowncount)
			{
			$tiles = $map->get_level_tiles('terrain', $level, Map::OPERATOR_EQUAL);
			$offset = $this->generate_biome_towns($map, $tiles, $townclearance, $biometowncount, $towns, $offset, $page);
			}
		}
		
	function generate_biome_towns(Map $map, array $tiles, int $townclearance, int $towncount, array $towns, int $idoffset, Page $page)
		{
		// Check all and clear invalid Tiles
		foreach($tiles as $tileindex => $tile)
			{
			// Clear Tiles too close to the Edge of the Map
			if($tile[0] < $townclearance || $tile[0] >= $map->get_width() - $townclearance
				|| $tile[1] < $townclearance || $tile[1] >= $map->get_height() - $townclearance)
				{
				unset($tiles[$tileindex]);
				continue(1);
				}
				
			// Clear flooded Tiles
			if($map->is_flooded($tile, true))
				{
				unset($tiles[$tileindex]);
				continue(1);
				}
						
			// Clear Tiles which are too close to existing Towns
			foreach($this->towns as $town)
				{
				if(Vector2::taxicab_length(Vector2::subtract($tile, $town)) <= $townclearance)
					{
					unset($tiles[$tileindex]);
					continue(2);
					}
				}
			}
		$tiles = array_values($tiles);
		
		// Print Error if not enough Space is left
		if(count($tiles) < $towncount)
			{
			$page->add_error('Not enough Tiles left for ' . $towncount . ' Towns in this Biome!');
			return $idoffset + $towncount;
			}
			
		for($i = 0; $i < $towncount; ++$i)
			{
			$rand = mt_rand(0, count($tiles) - 1);
			$map->set_tile('towns', $tiles[$rand], $towns[$idoffset + $i]);
			$this->towns[] = $tiles[$rand];
			
			// Delete Candidates which are too close to the new Town
			foreach($tiles as $tileindex => $tile)
				{
				if(Vector2::taxicab_length(Vector2::subtract($tile, $this->towns[count($this->towns) - 1])) <= $townclearance)
					{
					unset($tiles[$tileindex]);
					continue(1);
					}
				}
			$tiles = array_values($tiles);
			
			// Print Error if not enough Space is left
			if(count($tiles) < $towncount - $i)
				{
				$page->add_error('Not enough Tiles left for ' . ($towncount - $i) . ' remaining Towns in this Biome!');
				return $idoffset + $towncount;
				}
			}
			
		return $idoffset + $towncount;
		}
	}
?>