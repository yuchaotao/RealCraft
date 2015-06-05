<?php

class Mproperty extends CI_Model {
	function get_by_id($playerId) {
		return $this->db->get_where('userproperty', array('playerId' => $playerId))->row();
	}

	function update_property($playerId, $player) {
       	$this->db->update('userproperty', $player, array('playerId' => $playerId));
	}

	function get_username($playerId) {
		return $this->db->get_where('user', array('id'=>$playerId))->row()->username;
	}
}