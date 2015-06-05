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
    	echo json_encode($player), '/';
    	foreach($resourceBase->result() as $row) {
    		$data = array('id'=>$row->id, 'location'=>$row->location, 'type'=>1);
    		echo 1, json_encode($data), '/';
    	}
    	foreach($construction->result() as $row) {
    		$data = array('id'=>$row->id, 'location'=>$row->location, 'type'=>2);
    		echo 2, json_encode($data), '/';
    	}
    }
}