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
    	$username = $this->session->userdata('username');
    	if($playerId == NULL) {
    		echo -1;
    		return;
    	}
    	$player = $this->mproperty->get_by_id($playerId);
    	$res = array('id'=>$player->id, 'playerId'=>$player->playerId, 'wood'=>$player->wood, 
    				'stone'=>$player->stone, 'food'=>$player->food, 'workforce'=>$player->workforce,
    				'username'=>$username
    			);
    	echo json_encode($res);
    }

    function download() {
    	$playerId = $this->session->userdata('playerId');
    	if($playerId == NULL) {
    		echo -1;
    		return;
    	}
    	$resourceBase = $this->resourcebase->get_all();
    	$construction = $this->construction->get_all();
    	$res = '';
    	foreach($resourceBase->result() as $row) {
    		$data = array('id'=>$row->id, 'location'=>$row->location, 'type'=>1);
    		$res .= json_encode($data).',';
    	}
    	foreach($construction->result() as $row) {
    		$data = array('id'=>$row->id, 'location'=>$row->location, 'type'=>2);
    		$res .= json_encode($data).',';
    	}
    	echo '['.substr($res,0,strlen($res)-1).']';
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
    	if($targetType == 1) {
    		$detail = $this->resourcebase->get_by_id($targetId);
    		echo json_encode($detail);
    	}
    	else if($targetType == 2) {
    		$detail = $this->construction->get_by_id($targetId);
    		echo json_encode($detail);
    	}
    	else {
    		echo -2;
    		return;
    	}
    }
}