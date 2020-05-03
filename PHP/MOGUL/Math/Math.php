<?php
abstract class Math
	{
	// Returns the Value closest to $value which lies in the Range $min to $max.
	static function clamp(float $value, float $min, float $max)
		{
		return min(max($value, $min), $max);
		}
		
	// Linearly interpolates between $a and $b using $weight. A Weight of 0 returns $a and a Weight of 1.0 returns $b.
	static function lerp(float $a, float $b, float $weight)
		{
		return $a * (1 - $weight) + $b * $weight;
		}
	}
?>