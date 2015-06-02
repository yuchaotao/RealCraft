<?php
//this model is to deal with the stuff associating the distance

class Distance extends CI_Model{
	//calculate the distance between player and the target location
	// @param array[2] $playerLocation
	// @param array[2] $targetLocation
	function calculateDistance($playerLocation, $targetLocation){
		return sqrt(($playerLocation->x - $targetLocation->x)^2 + ($playerLocation->y - $targetLocation->y)^2);
	}
	//judge whether the location is touchable
	//@param int $dis
	//@param int $vision
	function isAble($dis, $vision){
		return $dis <= $vision ? TRUE : FALSE;
	}
}

