<?php

class Request extends CI_Controller {
	function __construct(){
    	parent::__construct();
        $this->load->database();
        $this->load->library('form_validation');
        $this->load->library('session');
    }

    function download() {
    	$playerId = $this->session->userdata('playerId');
    	if($playerId == NULL) {
    		echo -1;
    		return;
    	}
    	$player = $this->db->get_where('userproperty', array('playerId' => $playerId))->row();
    	$resourceBase = $this->db->get('resourcebase');
    	$construction = $this->db->get('construction');
    	echo "playerInfo: ", json_encode($player), '<br>';
    	foreach($resourceBase->result() as $row) {
    		echo "resourceBase: ", json_encode($row), '<br>';
    	}
    	foreach($construction->result() as $row) {
    		echo "construction: ", json_encode($row), '<br>';
    	}
    }
}