<?php

class Mproperty extends CI_Model {
	function get_by_id($playerId) {
		return $this->db->get_where('userproperty', array('playerId' => $playerId))->row();
	}

	function update_property($playerId, $player) {
		$player->wood -= 2 * $workforce;
      	$player->stone -= $workforce;
       	$this->db->update('userproperty', $player, array('playerId' => $playerId));
	}
}