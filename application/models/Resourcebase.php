<?php

//this model is dealing with the resource operations, e.g collect
class Resourcebase extends CI_Model {
	/* function collect()
	 * @param $targetId, $workforce
	 * @return $got
	 * $got is an array including 'wood', 'stone', 'food'
	*/
	function collect($targetId, $workforce) {
		$query = $this->db->get_where('resourcebase', array('id' => $targetId));
		if($query->num_rows() != 1) return -1;
		$resource = $query->row();
		$got = array();
		$got['wood'] = $resource->wood < $workforce ? $resource->wood : $workforce;
		$got['stone'] = $resource->stone < $workforce ? $resource->stone : $workforce;
		$got['food'] = $resource->food < $workforce ? $resource->food : $workforce;
		$resource->wood -= $got['wood'];
		$resource->stone -= $got['stone'];
		$resource->food -= $got['food'];
		$this->db->update('resourcebase', $resource, array('id' => $targetId));
		return $got;
	}

	function setBase($longitude, $latitude, $wood, $stone, $food){
		$this->db->query("INSERT INTO resourcebase (location, wood, stone, food) VALUES (PointFromText('POINT($longitude $latitude)'), '$wood', '$stone', '$food');");
	}	

	function fresh($WOOD_RANGE, $STONE_RANGE, $FOOD_RANGE){
		$query = $this->db->get('resourcebase');
		foreach ($query->result_array() as $row) {
			$this->db->where('id',$row['id']);
			$row['wood'] = mt_rand($WOOD_RANGE['start'],$WOOD_RANGE['end']);
            $Srow['stone'] = mt_rand($STONE_RANGE['start'],$STONE_RANGE['end']);
            $row['food'] = mt_rand($FOOD_RANGE['start'],$FOOD_RANGE['end']);
			$this->db->update('resourcebase',$row);
		}
	}

	function get_by_id($targetId) {
		return $this->db->query("SELECT id, X(location) as longitude, Y(location) as latitude, wood, food, stone FROM resourcebase WHERE id = $targetId;")->row();	
	}
	
	function get_all() {
		return $this->db->query("SELECT id, X(location) as longitude, Y(location) as latitude, wood, food, stone FROM resourcebase;");
	}

	function delete_all() {
		return $this->db->truncate('resourcebase');
	}

	function get_surrounding($longitude, $latitude, $vision){
		$leftdown = (string)($longitude - $vision).' '.(string)($latitude - $vision);
		$rightdown = (string)($longitude + $vision).' '.(string)($latitude - $vision);
		$upright = (string)($longitude + $vision).' '.(string)($latitude + $vision);
		$upleft = (string)($longitude - $vision).' '.(string)($latitude + $vision);
		$query = $this->db->simple_query("SET @g1 = GeomFromText('Polygon(($leftdown,$rightdown,$upright,$upleft,$leftdown))');");
		$query = $this->db->query("SELECT id, X(location) as longitude, Y(location) as latitude, wood, food, stone FROM resourcebase WHERE MBRContains(@g1,location);");
		return $query;
	}
}