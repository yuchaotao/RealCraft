<?php

//this model is dealing with construction operations, e.g build, attack...

class Construction extends CI_Model{
	//pre isFree($targetLocation)
	//pre isAble($dis, $vision)
	function build($playerId, $targetId, $workForce){
		$query = $this->db->get_where('construction', array('id' => $targetId));
		// echo "<br>your building is in process.";
		if($query->row()->playerId == -1) {
			// echo "You have owned this place...";
			$query->row()->playerId = $playerId;
		}
		else if($playerId != $query->row()->playerId){
				return -1;
		}
		// echo "<br>ID matched.<br>";
		$newConstruction = $query->row();
		// echo $newConstruction->location, ' ', $newConstruction->value, '<br>';
		$output = 0;
		if($workForce)
		{
			$newConstruction->value += $workForce;
			if($newConstruction->value > $newConstruction->maxdurability) {
				$output = $newConstruction->value - $newConstruction->maxdurability;
				$newConstruction->value = $newConstruction->maxdurability;
			}
			$this->db->where('id', $targetId);
			$this->db->update('construction', $newConstruction);
		}
		
		return $output;
	}

	function attack($playerId, $targetId, $attackDamage) {
		$query = $this->db->get_where('construction', array('id' => $targetId));
		if($query->num_rows() != 1) return -1;
		$query->row()->value = $query->row()->value - $attackDamage;
		if($query->row()->value <= 0) $query->row()->value = 0;
		if($query->row()->value == 0) $query->row()->playerId = -1;
		$this->db->update('construction', $query->row(), array('id' => $targetId));
		return $query->row()->value;
	}

	function abandon($playerId, $targetId) {
		$query = $this->db->get_where('construction', array('id' => $targetId));
		if($query->num_rows() != 1) return -1;
		if($query->row()->playerId != $playerId) return -2;// illegal operation
		$property = $this->db->get_where('userproperty', array('playerId'=>$playerId))->row();
		$property->wood += 2 * $query->row()->value;
		$property->stone += $query->row()->value;
		$query->row()->value = 0;
		$query->row()->playerId = -1;
		$this->db->update('userproperty', $property, array('playerId' => $playerId));
		$this->db->update('construction', $query->row(), array('id' => $targetId));
		return 1;
	}

	function setBase($longitude,$latitude){
		$this->db->query("INSERT INTO construction (playerId, location, value) VALUES ('-1', PointFromText('POINT($longitude $latitude)'), '0');");
	}

	function get_by_id($targetId) {
		return $this->db->query("SELECT id,playerId,X(location) as longitude, Y(location) as latitude, value, maxdurability FROM construction WHERE id = $targetId")->row()	;
	}

	function get_all() {
		return $this->db->query("SELECT id,playerId,X(location) as longitude, Y(location) as latitude, value, maxdurability FROM construction");
	}
	function delete_all() {
		return $this->db->truncate('construction');
	}

	function get_surrounding($longitude, $latitude, $vision){
		$leftdown = (string)($longitude - $vision).' '.(string)($latitude - $vision);
		$rightdown = (string)($longitude + $vision).' '.(string)($latitude - $vision);
		$upright = (string)($longitude + $vision).' '.(string)($latitude + $vision);
		$upleft = (string)($longitude - $vision).' '.(string)($latitude + $vision);
		$query = $this->db->simple_query("SET @g1 = GeomFromText('Polygon(($leftdown,$rightdown,$upright,$upleft,$leftdown))');");
		$query = $this->db->query("SELECT id,playerId,X(location) as longitude, Y(location) as latitude, value, maxdurability FROM construction WHERE MBRContains(@g1,location);");
		return $query;
	}
}