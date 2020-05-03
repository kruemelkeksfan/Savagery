<?php
abstract class Color
	{
	// Mix multiple Colors given in hexadecimal Form
	static function mix(array $colors)
		{
		$red = 0;
		$green = 0;
		$blue = 0;
		foreach($colors as $color)
			{
			$rgb = null;
			if(substr($color, 0, 1) === '#')
				{
				$rgb = str_split(substr($color, 1), 2);
				}
			else
				{
				$rgb = str_split($color, 2);
				}
			
			$red += hexdec($rgb[0]);
			$green += hexdec($rgb[1]);
			$blue += hexdec($rgb[2]);
			}
		
		
		
		$count = count($colors);
		$red = dechex(Math::clamp(($red / $count), 0, 255));
		$green = dechex(Math::clamp(($green / $count), 0, 255));
		$blue = dechex(Math::clamp(($blue / $count), 0, 255));
			
		while(strlen($red) < 2)
			{
			$red = '0' . $red;
			}
		while(strlen($green) < 2)
			{
			$green = '0' . $green;
			}
		while(strlen($blue) < 2)
			{
			$blue = '0' . $blue;
			}
			
		return '#' . $red . $green . $blue;
		}
	}
?>