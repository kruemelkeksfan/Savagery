<?php
abstract class Vector2
	{	
	// Returns the Length of $vector
	static function length(array $vector)
		{
		return sqrt(($vector[0] ** 2) + ($vector[1] ** 2));
		}
		
	// Returns the Length of $vector
	static function taxicab_length(array $vector)
		{
		return abs($vector[0]) + abs($vector[1]);
		}
		
	// Returns a Version of $vector which is scaled by $t
	static function scale(array $vector, float $t)
		{
		return array($vector[0] * $t, $vector[1] * $t);
		}
		
	// Returns a normalized Version of $vector
	static function normalize(array $vector)
		{
		$length = self::length($vector);
		if($length !== 0)
			{
			return array($vector[0] / $length, $vector[1] / $length);
			}
		else
			{
			ErrorHandler::handle_error('Vector (' . $vector[0] . '|' . $vector[1] . ') with Length 0 can not be normalized!');
			}
		}
		
	// Adds Vectors $lho and $rho and returns the Result
	static function add(array $lho, array $rho)
		{
		return array($lho[0] + $rho[0], $lho[1] + $rho[1]);
		}
				
	// Subtracts Vector $rho from Vector $lho and returns the Result
	static function subtract(array $lho, array $rho)
		{
		return array($lho[0] - $rho[0], $lho[1] - $rho[1]);
		}
	
	// Returns the Dot Product of $lho and $rho
	static function dot_product(array $lho, array $rho)
		{
		return $lho[0] * $rho[0] + $lho[1] * $rho[1];
		}
	}
?>