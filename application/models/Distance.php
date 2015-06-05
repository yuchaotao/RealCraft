<?php
//this model is to deal with the stuff associating the distance

class Distance extends CI_Model{
	//calculate the distance between player and the target location
	// @param array[2] $playerLocation
	// @param array[2] $targetLocation
	function calculateDistance($playerLocation, $targetLocation) {
		list($target_x_str, $target_y_str) = explode(',', $targetLocation);
        list($self_x_str, $self_y_str) = explode(',', $playerLocation);
        $target_x = doubleval($target_x_str);
        $target_y = doubleval($target_y_str);
        $self_x = doubleval($self_x_str);
        $self_y = doubleval($self_y_str);
		return sqrt(($self_x - $target_x)^2 + ($self_y - $target_y)^2);
	}
	//judge whether the location is touchable
	//@param int $dis
	//@param int $vision
	function isAble($dis, $vision){
		return $dis <= $vision ? TRUE : FALSE;
	}
}

