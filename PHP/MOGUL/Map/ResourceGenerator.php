<?php
class ResourceGenerator
	{	
	function __construct()
		{

		}
	
	function generate_resources(Map $map, Database $database)
		{
		$perlin = new PerlinGenerator();
		$resourcedata = $database->query('SELECT resourcename, depositsize, depositfrequency, biomes FROM Resources;');
		foreach($resourcedata as $resource)
			{
			$map->create_layer($resource['resourcename']);
			
			$gridwidth = $resource['depositfrequency'] + 1;
			$gridheight = intval(ceil($resource['depositfrequency'] * ($map->get_height() / $map->get_width()))) + 1;
			
			$biomes = explode(', ', $resource['biomes']);
			
			$resourcemap = $perlin->generate_map($map->get_width(), $map->get_height(), $perlin->generate_gradients($gridwidth, $gridheight));
			for($y = 0; $y < $map->get_height(); ++$y)
				{
				for($x = 0; $x < $map->get_width(); ++$x)
					{
					if($resourcemap[$y][$x] < $resource['depositsize'] && in_array($map->get_tile('terrain', array($x, $y)), $biomes)
						|| ($resource['resourcename'] === 'Fish' && $map->get_tile('rivers', array($x, $y)) !== 0))
						{
						$map->set_tile($resource['resourcename'], array($x, $y), 1);
						}
					else
						{
						$map->set_tile($resource['resourcename'], array($x, $y), 0);
						}
					}
				}
			}
		}
	}
?>