<?php

class Request extends CI_CONTROLLER {
    const vision = 0.002;
    const workerPrice = 100;
	function __construct(){
    	parent::__construct();
        $this->load->database();
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('construction');
        $this->load->model('resourcebase');
        $this->load->model('mproperty');
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

    function test(){
        $longitude = '123';
        $latitude = '456';
        $location = array('longitude'=>$longitude, 'latitude'=>$latitude);
        $this->construction->setBase($location);
    }

    function download() {
        $config = array(
            array(
                'field' => 'longitude',
                'label' => '玩家坐标_经度',
                'rules' => 'required'
                ),
            array(
                'field' => 'latitude',
                'label' => '玩家坐标_纬度',
                'rules' => 'required'
                )
            );
        $this->form_validation->set_rules($config);

        $longitude = $this->input->post('longitude');
        $latitude = $this->input->post('latitude');

        $location = array('longitude'=>$longitude, 'latitude'=>$latitude);

    	$playerId = $this->session->userdata('playerId');
    	if($playerId == NULL) {
    		echo -1;
    		return;
    	}

        $resourceBase = $this->resourcebase->get_surrounding($longitude, $latitude, self::vision);
        $construction = $this->construction->get_surrounding($longitude, $latitude, self::vision);
    	$res = array();
    	foreach($resourceBase->result() as $row) {
    		$data = array('id'=>$row->id, 'longitude'=>$row->longitude, 'latitude'=>$row->latitude, 'type'=>1);
            array_push($res, $data);
    	}
    	foreach($construction->result() as $row) {
    		$data = array('id'=>$row->id, 'longitude'=>$row->longitude, 'latitude'=>$row->latitude, 'type'=>2);
            array_push($res, $data);
    	}
        echo json_encode($res);
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
    		$username = $this->mproperty->get_username($detail->playerId);
    		$res = array(
    					'id'=>$detail->id, 'playerId'=>$detail->playerId, 'username'=>$username, 'longitude'=>$detail->longitude, 'latitude'=>$detail->latitude,
    					'value'=>$detail->value, 'maxdurability'=>$detail->maxdurability
    			);
    		echo json_encode($res);
        	}
    	else {
    		echo -2;
    		return;
    	}
    }

    function workforceCost() {
        $playerId = $this->session->userdata('playerId');
        if($playerId == NULL) {
            echo -1;
            return;
        }
        $player = $this->mproperty->get_by_id($playerId);
        $consume = (int) pow(10, $player->workforce - 1) * (self::workerPrice);
        echo $consume;
    }
}
