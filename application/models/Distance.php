<?php
//this model is to deal with the stuff associating the distance

class Distance extends CI_Model{
	//calculate the distance between player and the target location
	// @param array[2] $playerLocation
	// @param array[2] $targetLocation
	function calculateDistance($playerLocation, $targetLocation) {
        $target_x = doubleval($targetLocation['longitude']);
        $target_y = doubleval($targetLocation['latitude']);
        $self_x = doubleval($playerLocation['longitude']);
        $self_y = doubleval($playerLocation['latitude']);
		return sqrt(pow($self_x - $target_x,2) + pow($self_y - $target_y,2));
	}
	//judge whether the location is touchable
	//@param int $dis
	//@param int $vision
	function isAble($dis, $vision){
		return $dis <= $vision ? TRUE : FALSE;
	}
}

