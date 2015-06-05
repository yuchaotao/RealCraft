<?php

class Request extends CI_Controller {
	function __construct(){
    	parent::__construct();
        $this->load->database();
        $this->load->library('form_validation');
        $this->load->library('session');
    }

    function property() {
    	$playerId = $this->session->userdata('playerId');
    	if($playerId == NULL) {
    		echo -1;
    		return;
    	}
    	$player = $this->db->get_where('userproperty', array('playerId' => $playerId))->row();
    	echo json_encode($player);
    }

    function download() {
    	$playerId = $this->session->userdata('playerId');
    	if($playerId == NULL) {
    		echo -1;
    		return;
    	}
    	$resourceBase = $this->db->get('resourcebase');
    	$construction = $this->db->get('construction');
    	$res = '[';
    	foreach($resourceBase->result() as $row) {
    		$data = array('id'=>$row->id, 'location'=>$row->location, 'type'=>1);
    		$res += json_encode($data) + ',';
    	}
    	foreach($construction->result() as $row) {
    		$data = array('id'=>$row->id, 'location'=>$row->location, 'type'=>2);
    		$res += json_encode($data) + ',';
    	}
    	echo $res;
    }

    function detail() {
    	$config = array(
                array(
                	'field'=>'targetId',
                	'label'=>'目标ID',
                	'rules'=>'required'
             	),
             	array(
             		'field'=>'targetType',
             		'label'=>'目标类型',
             		'rules'=>'required'
             	)
        );
        $this->form_validation->set_rules($config);
        $playerId = $this->session->userdata('playerId');
    	if($playerId == NULL) {
    		echo -1;
    		return;
    	}
    	$targetId = $this->input->post('targetId');
    	$targetType = $this->input->post('targetType');
    	$database = '';
    	if($targetType == 1) $database = 'resourcebase';
    	else if($targetType == 2) $database = 'construction';
    	else {
    		echo -2;
    		return;
    	}
    	$query = $this->db->get_where($database, array('id' => $targetId));
    	if($query->num_rows() != 1) {
    		echo -3;
    		return;
    	}
    	$detail = $query->row();
    	echo json_encode($detail);
    }
}