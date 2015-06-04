<?php

class Operation extends CI_Controller {
	const vision = 10;
	function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url'));
        $this->load->model('construction');
        $this->load->model('resourcebase');
        $this->load->library('session');
        $this->load->model('distance');
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

        $playerId = $this->session->userdata('playerId');
        if($playerId == NULL) {
        	echo -1;
        	return;
        }
        //echo "Your id is: ", $playerId, "<br>";
        $targetId = $this->input->post('targetId');
        $selfLocation = $this->input->post('selflocation');
        $locationInfo = $this->db->get_where('construction', array('id' => $targetId))->row();
        if($this->distance->calculateDistance($selfLocation, $locationInfo->location) < self::vision) {
        	$workforce = 1;
            $query = $this->db->get_where('userproperty', array('playerId' => $playerId));
            $player = $query->row();
            if($player->wood >= 2 * $workforce && $player->stone >= 1 * $workforce) {
        	   $state = $this->construction->build($playerId, $targetId, $workforce);
               $player->wood -= 2 * $workforce;
               $player->stone -= $workforce;
               $this->db->update('userproperty', $player, array('playerId' => $playerId));
               echo $state;
            }
            else {
                echo -3, ";You don't have enough resources...";
            }
        }
        else {
        	echo -2, ";The target is too far to touch!";
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
        $playerId = $this->session->userdata('playerId');
        if($playerId == NULL) {
        	echo -1;
        	return;
        }
        $targetId = $this->input->post('targetId');
        $selfLocation = $this->input->post('selflocation');
        $locationInfo = $this->db->get_where('construction', array('id' => $targetId))->row();
        if($this->distance->calculateDistance($selfLocation, $locationInfo->location) < self::vision) {
        	$attackDamage = 1;
        	$state = $this->construction->attack($playerId, $targetId, $attackDamage);
        	echo $state;
        }
        else {
        	echo "The target is too far to touch!";
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
        $playerId = $this->session->userdata('playerId');
        if($playerId == NULL) {
        	echo -1;
        	return;
        }
        $targetId = $this->input->post('targetId');
        $selfLocation = $this->input->post('selflocation');
        $locationInfo = $this->db->get_where('resourcebase', array('id' => $targetId))->row();
        $query = $this->db->get_where('userproperty', array('playerId' => $playerId));
        $player = $query->row();
        if($this->distance->calculateDistance($selfLocation, $locationInfo->location) < self::vision) {
        	$workforce = 1;
        	$type = $this->resourcebase->collect($targetId, $workforce);
        	switch($type) {
        		case 1:
        			$player->wood += $workforce;
        			break;
        		case 2:
        			$player->stone += $workforce;
        			break;
        		case 3:
        			$player->stone += $workforce;
        			$player->wood += $workforce;
        			break;
        		case 4:
        			$player->food += $workforce;
        			break;
        		case 5:
        			$player->wood += $workforce;
        			$player->food += $workforce;
        			break;
        		case 6:
        			$player->stone += $workforce;
        			$player->food += $workforce;
        			break;
        		case 7:
        			$player->stone += $workforce;
        			$player->wood += $workforce;
        			$player->food += $workforce;
        			break;
        		case -1:
        			echo "Wrong target location!";
        			break;
        		case 0:
        			echo "The source base is empty!";
        			break;
        	}
        	$this->db->update('userproperty', $player, array('playerId' => $playerId));
        }
    }
}