<?php

//this model is dealing with construction operations, e.g build, attack...

class Construction extends CI_Model{
	//pre isFree($targetLocation)
	//pre isAble($dis, $vision)
	function build($playerId, $targetId, $workForce){
		$query = $this->db->get_where('construction', array('id' => $targetId));
		// echo "<br>your building is in process.";
		if($query->row()->playerId == NULL) {
			echo "You have owned this place...";
			$query->row()->playerId = $playerId;
		}
		else if($playerId != $query->row()->playerId){
				return -1;
		}
		// echo "<br>ID matched.<br>";
		$newConstruction = $query->row();
		// echo $newConstruction->location, ' ', $newConstruction->value, '<br>';
		if($workForce)
		{
			$newConstruction->value += $workForce;
			if($newConstruction->value > $newConstruction->maxdurability)
				$newConstruction->value = $newConstruction->maxdurability;
			$this->db->where('id', $targetId);
			$this->db->update('construction', $newConstruction);
		}
		
		return $newConstruction->value;
	}

	function attack($playerId, $targetId, $attackDamage) {
		$query = $this->db->get_where('construction', array('id' => $targetId));
		if($query->num_rows() != 1) return -1;
		$query->row()->value = $query->row()->value - $attackDamage;
		if($query->row()->value <= 0) $query->row()->value = 0;
		$this->db->update('construction', $query->row(), array('id' => $targetId));
		return $query->row()->value;
	}

	function setBase($location){
		$this->db->insert('construction', array('playerId'=>NULL, 'location'=>$location, 'value'=>0));
	}

	function get_by_id($targetId) {
		return $this->db->get_where('construction', array('id' => $targetId))->row();
	}

	function get_all() {
		return $this->db->get('construction');
	}
}