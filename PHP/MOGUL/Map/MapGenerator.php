<?php
class MapGenerator
	{
	private $map;
		
	function __construct()
		{

		}
	
	function generate_map(string $seed, int $mapwidth, int $mapheight,
		int $roughtype, int $finetype,
		int $roughwidth, int $roughheight, int $finewidth, int $fineheight,
		float $roughweight, float $fineweight,
		array $heightlines, int $sealevel,
		int $rivercount, int $riverclearance,
		int $coastaltowns, array $biometowns, int $townclearance,
		Database $database, Page $page, bool $save = false)
		{
		// Seed
		$seed = hexdec(hash('crc32', $seed)) % 1000000;
		mt_srand($seed);
			
		// Create Map
		$map = Map::init($database, $mapwidth, $mapheight, $sealevel);
		
		// Generate Generators
		$terraingenerator = new TerrainGenerator();
		$rivergenerator = new RiverGenerator();
		$towngenerator = new TownGenerator();
		$resourcegenerator = new ResourceGenerator();
		
		// Generate Terrain
		$terraingenerator->generate_terrain($map, $heightlines, $heightlines[$sealevel],
			$roughwidth, $roughheight, $finewidth, $fineheight, $roughweight, $fineweight, $roughtype, $finetype);

		// Generate Rivers
		$rivergenerator->generate_rivers($map, $rivercount, $riverclearance, $page);
			
		// Generate Towns
		$towngenerator->generate_towns($map, $database, $coastaltowns, $biometowns, $townclearance, $page, $save);
		
		// Generate Resources
		$resourcegenerator->generate_resources($map, $database);
		
		// Finished
		return $map;
		}
	}
?>