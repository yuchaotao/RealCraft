<?php

class Operation extends CI_CONTROLLER {
    const vision = 0.001;
    // const vision has been moved to the parent controller
	function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url'));
        $this->load->model('construction');
        $this->load->model('resourcebase');
        $this->load->library('session');
        $this->load->model('distance');
        $this->load->model('mproperty');
    }

    function index() {
    	$this->load->view('operation/collect');
    }
    function index1() {
        $this->load->view('operation/attack');
    }
    function index2() {
        $this->load->view('operation/build');
    }

    function build() {
    	$config = array(
                array(
                	'field'=>'targetId',
                	'label'=>'目标ID',
                	'rules'=>'required'
             	),
             	array(
                	'field'=>'selflocation',
                 	'label'=>'自身位置',
                 	'rules'=>'required'
             	)
        );
        $this->form_validation->set_rules($config);
        $playerId = $this->session->userdata('playerId');
        if($playerId == NULL) {
        	echo -1;
        	return;
        }
        //echo "Your id is: ", $playerId, "<br>";
        $targetId = $this->input->post('targetId');
        $selfLocation = $this->input->post('selflocation');
        $locationInfo = $this->construction->get_by_id($targetId);
        if($this->distance->calculateDistance($selfLocation, $locationInfo->location) < self::vision) {
        	$workforce = 1;
            $player = $this->mproperty->get_by_id($playerId);
            if($player->wood >= 2 * $workforce && $player->stone >= 1 * $workforce) {
        	   $state = $this->construction->build($playerId, $targetId, $workforce);
               $player->wood -= 2 * $workforce;
               $player->stone -= $workforce;
               $this->mproperty->update_property($playerId, $player);
               echo $state; // durability
            }
            else {
                echo -3;
                // You don't have enough resources...
            }
        }
        else {
        	echo -2;
            // The target is too far to touch!
        }
    }

    function attack() {
    	$config = array(
                array(
                	'field'=>'targetId',
                	'label'=>'目标ID',
                	'rules'=>'required'
             	),
             	array(
                	'field'=>'selflocation',
                 	'label'=>'自身位置',
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
        $selfLocation = $this->input->post('selflocation');
        $locationInfo = $this->construction->get_by_id($targetId);
        if($this->distance->calculateDistance($selfLocation, $locationInfo->location) < self::vision) {
        	$attackDamage = 1;
        	$state = $this->construction->attack($playerId, $targetId, $attackDamage);
        	echo $state; // durability
        }
        else {
        	echo -2;
            // The target is too far to touch!
        }
    }

    function abandon() {
        $config = array(
                array(
                    'field'=>'targetId',
                    'label'=>'目标ID',
                    'rules'=>'required'
                ),
                array(
                    'field'=>'selflocation',
                    'label'=>'自身位置',
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
        $selfLocation = $this->input->post('selflocation');
        $locationInfo = $this->construction->get_by_id($targetId);
        if($this->distance->calculateDistance($selfLocation, $locationInfo->location) < self::vision) {
            $state = $this->construction->abandon($playerId, $targetId);
            echo $state;
        }
        else {
            echo -2;
            // The target is too far to touch!
        }
    }

    function collect() {
    	$config = array(
                array(
                	'field'=>'targetId',
                	'label'=>'目标ID',
                	'rules'=>'required'
             	),
             	array(
                	'field'=>'selflocation',
                 	'label'=>'自身位置',
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
        $selfLocation = $this->input->post('selflocation');
        $locationInfo = $this->resourcebase->get_by_id($targetId);
        $player = $this->mproperty->get_by_id($playerId);
        if($this->distance->calculateDistance($selfLocation, $locationInfo->location) < self::vision) {
        	$workforce = 1;
        	$type = $this->resourcebase->collect($targetId, $workforce);
        	switch($type) {
        		case 1:
        			$player->wood += $workforce;
                    echo 1;
        			break;
        		case 2:
        			$player->stone += $workforce;
                    echo 1;
        			break;
        		case 3:
        			$player->stone += $workforce;
        			$player->wood += $workforce;
                    echo 1;
        			break;
        		case 4:
        			$player->food += $workforce;
                    echo 1;
        			break;
        		case 5:
        			$player->wood += $workforce;
        			$player->food += $workforce;
                    echo 1;
        			break;
        		case 6:
        			$player->stone += $workforce;
        			$player->food += $workforce;
                    echo 1;
        			break;
        		case 7:
        			$player->stone += $workforce;
        			$player->wood += $workforce;
        			$player->food += $workforce;
                    echo 1;
        			break;
        		case -1:
        			echo -2;
        			break;
        		case 0:
        			echo 0;
        			break;
        	}
        	$this->mproperty->update_property($playerId, $player);
        }
    }
}
