<?php

//this model is dealing with construction operations, e.g build, attack...

class Construction extends CI_Model{
	const constructionMaxDurability = 10;
	//pre isFree($targetLocation)
	//pre isAble($dis, $vision)
	function build($playerId, $targetLocation, $workForce){
		$newContruction['playerId'] = $playerId;
		$newContruction['location'] = $targetLocation;
		$newContruction['value'] = 0;
		if(!isFree($targetLocation)){
			if($query->num_rows() == 1){
				if($playerId != $query->row->playerId){
					return -1;
				}
				$newContruction = $query->row;
			}
			else{
				return -1;
			}
		}
		else{
			$this->db->insert('construction', $newContruction);
		}
		$this->db->where('location', $targetLocation);
		while($workForce)
		{
			sleep(1);
			$newConstruction->value += $workForce;
			if($newConstruction->value > self::constructionMaxDurability)
				$newConstruction->value = self::constructionMaxDurability;
			$this->db->update('construction', $newConstruction);
			if($newConstruction->value == self::constructionMaxDurability)
				return 1;
		}
		return 0;
	}
}