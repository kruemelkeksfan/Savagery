<?php
class PerlinGenerator
	{
	const TYPE_MESSY = 0;
	const TYPE_ISLAND = 1;
	const TYPE_POND = 2;
	
	private $maxresult;
		
	function __construct()
		{
		$this->maxresult = sqrt(0.5);
		}
	
	// Ideally Gridsize should be the number of desired Gridcells + 1
	// Types: TYPE_MESSY (default), TYPE_ISLAND, TYPE_POND
	// Types TYPE_ISLAND and TYPE_POND work best with 2x2 Gridsize
	function generate_gradients(int $gridwidth, int $gridheight, int $type = self::TYPE_MESSY)
		{
		// Validate Parameters
		if($gridwidth < 2 || $gridheight < 2 )
			{
			ErrorHandler::handle_error($gridwidth . '|' . $gridheight . ' is not a valid Gridsize for PerlinGenerator!');
			}

		// Generate Gradients
		$gradients = array();
		for($y = 0; $y < $gridheight; ++$y)
			{
			$gradients[] = array();
			for($x = 0; $x < $gridwidth; ++$x)
				{
				if($type === self::TYPE_MESSY)
					{
					$gradients[$y][] = self::generate_random_vector();
					}
				else if($type === self::TYPE_ISLAND || $type === self::TYPE_POND)
					{
					// TODO: Test Island!
					// Get Vector between the Middle of the Map and the current Gradient Position
					$diff = array(0, 0);
					if($type === self::TYPE_ISLAND)
						{
						$diff = Vector2::subtract(array(($gridwidth - 1) * 0.5, ($gridheight - 1) * 0.5), array($x, $y));
						}
					else if($type === self::TYPE_POND)
						{
						$diff = Vector2::subtract(array($x, $y), array(($gridwidth - 1) * 0.5, ($gridheight - 1) * 0.5));
						}
					
					// Generate a random Deviation from $diff
					if($diff[0] != 0 || $diff[1] != 0)
						{
						// Get a Vector which is orthogonal to $diff by transposing $diff and applying 1 random Flip
						$rand = mt_rand(0, 1);
						$orthogonal = array($rand === 0 ? -$diff[1] : $diff[1], $rand === 1 ? -$diff[0] : $diff[0]);
					
						// Get a random Gradient within 45 Degrees of $diff
						$gradients[$y][] = Vector2::normalize(Vector2::add($diff, Vector2::scale($orthogonal, mt_rand(1, 10) * 0.1)));
						}
					// If there is no valid $diff generate an entirely random Vector
					else
						{
						$gradients[$y][] = self::generate_random_vector();
						}
					}
				else
					{
					ErrorHandler::handle_error('Unknown Maptype ' . $type . '!');
					}
				}
			}
		
		return $gradients;
		}
	
	// TODO: Use Map parameter instead of mapwidth/mapheight
	function generate_map(int $mapwidth, int $mapheight, array $gradients)
		{
		// Calculate Step Sizes
		$xstepwidth = (count($gradients[0]) - 1) / $mapwidth;
		$ystepwidth = (count($gradients) - 1) / $mapheight;
			
		// Calculate Map Tiles
		$map = array();
		for($y = $ystepwidth * 0.5; $y < count($gradients) - 1; $y += $ystepwidth)
			{
			$map[] = array();
			for($x = $xstepwidth * 0.5; $x < count($gradients[$y]) - 1; $x += $xstepwidth)
				{
				// Calculate corresponding Grid Cell
				$gridx = floor($x);
				$gridy = floor($y);
				
				// Calculate Dot Products with Grid Corner Gradients
				$corners = array(array($gridx, $gridy), array($gridx + 1, $gridy), array($gridx, $gridy + 1), array($gridx + 1, $gridy + 1));
				$distances = array();
				$dots = array();
				foreach($corners as $corner)
					{
					$distances[] = Vector2::subtract(array($x, $y), $corner);
					$dots[] = Vector2::dot_product($distances[count($distances) - 1], $gradients[$corner[1]][$corner[0]]);
					}
				
				// Lerp Results together
				$weight = abs($distances[0][0]) / (abs($distances[0][0]) + abs($distances[1][0]));
				$toplerp = Math::lerp($dots[0], $dots[1], $weight);
				$bottomlerp = Math::lerp($dots[2], $dots[3], $weight);
										  
				$weight = abs($distances[0][1]) / (abs($distances[0][1]) + abs($distances[2][1]));
				$finallerp = Math::lerp($toplerp, $bottomlerp, $weight);
				
				$finallerp = Math::clamp((($finallerp + $this->maxresult) / ($this->maxresult * 2.0)), 0.0, 1.0);	// Transform Result into Range 0.0 to 1.0
				
				// Save Results
				$map[count($map) - 1][] = $finallerp;
				}
			}
		
		return $map;
		}
		
	private function generate_random_vector()
		{	
		$randx = (mt_rand(0, 1) > 0) ? (mt_rand(0, 9) * 0.1) : -(mt_rand(0, 9) * 0.1);
		$randy = (mt_rand(0, 1) > 0) ? (sqrt(-($randx ** 2) + 1)) : -(sqrt(-($randx ** 2) + 1));
		return array($randx, $randy);
		}
	}
?>