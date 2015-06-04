<?php

//this model is dealing with construction operations, e.g build, attack...

class Construction extends CI_Model{
	const constructionMaxDurability = 10; // up limit of the durability
	//pre isFree($targetLocation)
	//pre isAble($dis, $vision)
	function build($playerId, $targetId, $workForce){
		$query = $this->db->get_where('construction', array('id' => $targetId));
		// echo "<br>your building is in process.";
		if($query->row()->playerId == NULL) {
			$query->row()->playerId = $playerId;
		}
		else if($playerId != $query->row()->playerId){
				return -1;
		}
		// echo "<br>ID matched.<br>";
		$newContruction = $query->row();
		// echo $newContruction->location, ' ', $newContruction->value, '<br>';
		if($workForce)
		{
			$newContruction->value += $workForce;
			if($newContruction->value > self::constructionMaxDurability)
				$newContruction->value = self::constructionMaxDurability;
			$this->db->where('id', $targetId);
			$this->db->update('construction', $newContruction);
		}
		if($newContruction->value == self::constructionMaxDurability) {// Building finished.
			// echo "Construction complete..";
			return 1;
		}
		return 0;
	}

	function attack($playerId, $targetId, $attackDamage) {
		$query = $this->db->where('construction', array('id' => $targetId));
		if($query->num_rows() != 1) return -1;
		if($query->row()->value <= 0) return 1;
		$query->row()->value = $query->row()->value - $attackDamage;
		if($newValue <= 0) $newValue = 0;
		$this->db->update('construction', $query->row(), array('id' => $targetId));
		return 0;
	}

	function setBase($location){
		$this->db->insert('construction', array('playerId'=>NULL, 'location'=>$location, 'value'=>0));
	}
}