<?php
class TerrainGenerator
	{
	function __construct()
		{

		}
	
	function generate_terrain(Map $map, array $heightlines, float $seaheight, int $roughwidth, int $roughheight, int $finewidth, int $fineheight, float $roughweight, float $fineweight, string $roughtype, string $finetype)
		{
		// Create Map Layer
		$map->create_layer('terrain');
			
		// Generate Maps
		$perlin = new PerlinGenerator();
		$roughmap = $perlin->generate_map($map->get_width(), $map->get_height(), $perlin->generate_gradients($roughwidth, $roughheight, $roughtype));
		$finemap = $perlin->generate_map($map->get_width(), $map->get_height(), $perlin->generate_gradients($finewidth, $fineheight, $finetype));
		
		// Join Map Layers
		for($y = 0; $y < count($roughmap); ++$y)
			{
			for($x = 0; $x < count($roughmap[$y]); ++$x)
				{
				$cellheight = 0;
				if($roughmap[$y][$x] > $seaheight)
					{
					$cellheight = ($roughmap[$y][$x] * $roughweight) + ($finemap[$y][$x] * $fineweight);
					}
				else
					{
					$cellheight = $roughmap[$y][$x];
					}
					
				foreach($heightlines as $heightindex => $heightline)
					{
					$map->set_tile('terrain', array($x, $y), $heightindex);
					if($cellheight <= $heightline)
						{
						break(1);
						}
					}
				}
			}
		}
	}
?>