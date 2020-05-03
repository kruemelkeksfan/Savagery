<?php
class RiverGenerator
	{
	function __construct()
		{

		}
	
	function generate_rivers(Map $map, int $rivercount, int $riverclearance, Page $page)
		{		
		// Generate River Names
		$rivernames = array();
		$rivernames[] = 0;		// No River
		for($i = 1; $i <= $rivercount; ++$i)
			{
			// TODO: Give real Names to Rivers
			$rivernames[$i] = 'River ' . $i;
			}
			
		// Create Map Layer
		$map->create_layer('rivers');
			
		// Generate Rivers
		$sources = array();
		for($i = 1; $i < count($rivernames); ++$i)
			{
			// Generate Sourcepoint for the River somewhere on the Coast
			// Get all Coasttiles and clear invalid Tiles
			$coasttiles = $map->get_coast_tiles(true);
			foreach($coasttiles as $coastindex => $coasttile)
				{	
				// Clear Tiles too close to the Edge of the Map
				if($coasttile[0] < $riverclearance || $coasttile[0] >= $map->get_width() - $riverclearance
					|| $coasttile[1] < $riverclearance || $coasttile[1] >= $map->get_height() - $riverclearance)
					{
					unset($coasttiles[$coastindex]);
					continue(1);
					}
				
				// Clear Tiles which border more than 1 Watertile
				$watercount = 0;
				foreach($map->get_neighbours($coasttile) as $neighbour)
					{
					if($map->is_flooded($neighbour, true))
						{
						++$watercount;
						}

					if($watercount > 1)
						{
						unset($coasttiles[$coastindex]);
						continue(2);
						}
					}
				
				// Clear Tiles which are too close to existing Sources
				foreach($sources as $source)
					{
					if(Vector2::taxicab_length(Vector2::subtract($coasttile, $source)) <= $riverclearance)
						{
						unset($coasttiles[$coastindex]);
						continue(2);
						}
					}
				}
			$coasttiles = array_values($coasttiles);
			
			// Check Availability of enough Coasttiles
			if(count($coasttiles) < $rivercount)
				{
				$page->add_error('Not enough Coasttiles left for ' . $rivercount . ' Rivers!');
				break(1);
				}
			
			// Choose a random Coasttile and save as River Endpoint
			$coordinates = $coasttiles[mt_rand(0, count($coasttiles) - 1)];
			
			// Determine Directions in which the River may flow
			// Generate possible Directions
			$directions = array(array(0, -1), array(1, 0), array(0, 1), array(-1, 0));
			$invaliddirections = array();
			
			// Find Directions which lead into the Mapedge or Water
			$directionindex = 0;
			foreach($map->get_neighbours($coordinates, Map::MODE_CUSTOM, $directions, true) as $neighbour)
				{
				if(is_null($neighbour) || $map->is_flooded($neighbour, true))
					{
					$invaliddirections[] = $directionindex;
					}
					
				++$directionindex;
				}
				
			// Cut one invalid Direction
			unset($directions[$invaliddirections[mt_rand(0, count($invaliddirections) - 1)]]);
			$directions = array_values($directions);
			
			// Generate Riverflow
			$previousheight = 0;
			while(true)
				{
				// Save the Terrain of the current Tile
				$currentheight = $map->get_tile('terrain', $coordinates);
					
				// Set the current Tile to Water on River Layer
				$map->set_tile('rivers', $coordinates, $rivernames[$i]);
				
				// Find Candidate Tiles
				$flowcandidates = array();
				foreach($map->get_neighbours($coordinates, Map::MODE_CUSTOM, $directions, true) as $neighbour)
					{
					// Clear all Candidates and finish when reaching the Mapedge
					if(!$neighbour)
						{
						$flowcandidates = array();
						break(1);
						}
						
					// Skip all Candidates that are lower than the previous Rivertile and prevent going backwards
					if($map->get_tile('terrain', $neighbour) < $previousheight || $map->is_flooded($neighbour, true))
						{
						continue(1);
						}
					
					// Skip all Candidates that border a Tile lower than the previous Rivertile			
					foreach($map->get_neighbours($neighbour, Map::MODE_SHORT_U, VECTOR2::subtract($coordinates, $neighbour)) as $neighbourneighbour)
						{
						if(($map->get_tile('terrain', $neighbourneighbour) < $previousheight || $map->is_flooded($neighbourneighbour, true))
							&& $neighbourneighbour !== $coordinates)
							{
							continue(2);
							}
						}
						
					// Save Candidate
					$flowcandidates[] = $neighbour;
					}

				// Reduce Candidate Tiles based on surrounding Water
				if(count($flowcandidates) > 1)
					{
					// Count Watertiles in the Surroundings of each Candidate Tile
					$watercounts = array();
					foreach($flowcandidates as $candidateindex => $flowcandidate)
						{
						$watercounts[$candidateindex] = 0;
						foreach($map->get_neighbours($flowcandidate, Map::MODE_8_CROSS, $riverclearance) as $neighbour)
							{
							if($map->is_flooded($neighbour, true))
								{
								++$watercounts[$candidateindex];
								}
							}
						}
					
					// Remove all Candidates except those with the least surrounding Water
					$min = 	min($watercounts);
					foreach($watercounts as $candidate => $watercount)
						{
						if($watercount > $min)
							{
							unset($flowcandidates[$candidate]);
							}
						}
					$flowcandidates = array_values($flowcandidates);
					}
				
				// Choose a Candidate randomly or finish if no Candidates are available
				if(count($flowcandidates) > 0)
					{
					// Always use the Height of the previous Tile instead of the current one to allow moving through 1 Tile of higher Terrain
					$previousheight = $currentheight;
					
					// Choose a Candidate
					$coordinates = $flowcandidates[mt_rand(0, count($flowcandidates) - 1)];
					}
				else
					{
					// Finish River and save as last Tile as River Source
					$sources[] = $coordinates;
					--$rivercount;
					break(1);
					}
				}
			}
			
		// Debug Output
		/*foreach($sources as $source)
			{
			$map->set_tile('terrain', $source, 0);
			}*/
		
		/*foreach($map->get_neighbours(array(10, 10), Map::MODE_U, array(0, -1)) as $neighbour)
			{
			$map->set_tile('terrain', $neighbour, 0);
			}*/
		}
	}
?>