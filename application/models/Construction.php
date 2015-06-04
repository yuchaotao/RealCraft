<?php

//this model is dealing with construction operations, e.g build, attack...

class Construction extends CI_Model{
	const constructionMaxDurability = 10;
	//pre isFree($targetLocation)
	//pre isAble($dis, $vision)
	function build($playerId, $targetLocation, $workForce){
		$newContruction = array('playerId'=>$playerId, 'location'=>$targetLocation, 'value'=>0);
		// $newContruction['playerId'] = $playerId;
		// $newContruction['location'] = $targetLocation;
		// $newContruction['value'] = 0;
		$query = $this->db->get_where('construction', array('location' => $targetLocation));
		if($query->num_rows() == 1) {
			// echo "<br>your building is in process.";
			if($playerId != $query->row()->playerId){
					return -1;
			}
			// echo "<br>ID matched.<br>";
			$newContruction = $query->row();
			// echo $newContruction->location, ' ', $newContruction->value, '<br>';
		}
		else {
			// echo "a new construction is in process...<br>";
			$this->db->insert('construction', $newContruction);
		}
		if($workForce)
		{
			$newContruction->value += $workForce;
			if($newContruction->value > self::constructionMaxDurability)
				$newContruction->value = self::constructionMaxDurability;
			$this->db->where('location', $targetLocation);
			$this->db->update('construction', $newContruction);
		}
		if($newContruction->value == self::constructionMaxDurability) {// Building finished.
			// echo "Construction complete..";
			return 1;
		}
		return 0;
	}

	function attack($playerId, $targetLocation, $attackDamage) {
		$query = $this->db->where('construction', array('location' => $targetLocation));
		if($query->num_rows() != 1) return -1;
		if($query->row()->value <= 0) return 1;
		$query->row()->value = $query->row()->value - $attackDamage;
		if($newValue <= 0) $newValue = 0;
		$this->db->update('construction', $query->row(), array('location' => $targetLocation));
		return 0;
	}
}