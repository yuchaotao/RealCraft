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
    	$resourceBase = $this->db->get_where('resourcebase')->row();
    	$construction = $this->db->get_where('construction')->row();
    	echo "playerInfo: ", json_encode($player);
    	echo "resourceBase: ", json_encode($resourceBase);
    	echo "construction: ", json_encode($construction);
    }
}