<?php

//this model is dealing with the resource operations, e.g collect
class Resourcebase extends CI_Model {
	/* function collect()
	 * @param $targetId, $workforce
	 * @return 1 -- wood resource base
	 * @return 2 -- stone resource base
	 * @return 4 -- food resource base
	 * @return 1 + 2 -- wood + stone resource base
	 * @return 1 + 4 -- wood + food resource base
	 * @return 2 + 4 -- stone + food resource base
	 * @return 1 + 2 + 4 -- all resource base
	 * @return 0 -- the resource base is empty
	*/
	function collect($targetId, $workforce) {
		$query = $this->db->get_where('resourcebase', array('id' => $targetId));
		if($query->num_rows() != 1) return -1;
		$resource = $query->row();
		if($resource->wood > 0 && $resource->stone == 0 && $resource->food == 0) {
			$resource->wood -= $workforce;
			$this->db->update('resourcebase', $resource, array('id' => $targetId));
			return 1;
		}
		else if($resource->wood == 0 && $resource->stone > 0 && $resource->food == 0) {
			$resource->stone -= $workforce;
			$this->db->update('resourcebase', $resource, array('id' => $targetId));
			return 2;
		}
		else if($resource->wood == 0 && $resource->stone == 0 && $resource->food > 0) {
			$resource->food -= $workforce;
			$this->db->update('resourcebase', $resource, array('id' => $targetId));
			return 4;
		}
		else if($resource->wood > 0 && $resource->stone > 0 && $resource->food == 0) {
			$resource->wood -= $workforce;
			$resource->stone -= $workforce;
			$this->db->update('resourcebase', $resource, array('id' => $targetId));
			return 3;
		}
		else if($resource->wood > 0 && $resource->stone == 0 && $resource->food > 0) {
			$resource->wood -= $workforce;
			$resource->food -= $workforce;
			$this->db->update('resourcebase', $resource, array('id' => $targetId));
			return 5;
		}
		else if($resource->wood == 0 && $resource->stone > 0 && $resource->food > 0) {
			$resource->food -= $workforce;
			$resource->stone -= $workforce;
			$this->db->update('resourcebase', $resource, array('id' => $targetId));
			return 6;
		}
		else if($resource->wood > 0 && $resource->stone > 0 && $resource->food > 0) {
			$resource->wood -= $workforce;
			$resource->stone -= $workforce;
			$resource->food -= $workforce;
			$this->db->update('resourcebase', $resource, array('id' => $targetId));
			return 7;
		}
		else
			return 0;
	}
}