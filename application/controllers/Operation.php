<?php

class Operation extends CI_CONTROLLER {
    const vision = 0.002;
    const workerPrice = 10;
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
                	'field'=>'longitude',
                 	'label'=>'自身位置的经度',
                 	'rules'=>'required'
             	),
                array(
                    'field'=>'latitude',
                    'label'=>'自身位置的纬度',
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
        $user['longitude'] = $this->input->post('longitude');
        $user['latitude'] = $this->input->post('latitude');
        $currentTime = time();
        if($currentTime - $this->session->userdata('last_post') < 1) {
            return;
        }
        $this->session->set_userdata('last_post', $currentTime);
        $locationInfo = $this->construction->get_by_id($targetId);
        $target['longitude'] = $locationInfo->longitude;
        $target['latitude'] = $locationInfo->latitude;
        if($this->distance->calculateDistance($user, $target) < self::vision) {
            $player = $this->mproperty->get_by_id($playerId);
            $workforce = $player->workforce;
            if($player->wood >= 2 * $workforce && $player->stone >= 1 * $workforce) {
        	   $redundancy = $this->construction->build($playerId, $targetId, $workforce);
               $player->wood -= 2 * ($workforce - $redundancy);
               $player->stone -= ($workforce - $redundancy);
               $this->mproperty->update_property($playerId, $player);
               echo $redundancy; // durability
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
                    'field'=>'longitude',
                    'label'=>'自身位置的经度',
                    'rules'=>'required'
                ),
                array(
                    'field'=>'latitude',
                    'label'=>'自身位置的纬度',
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
        $user['longitude'] = $this->input->post('longitude');
        $user['latitude'] = $this->input->post('latitude');
        $currentTime = time();
        if($currentTime - $this->session->userdata('last_post') < 1) {
            return;
        }
        $this->session->set_userdata('last_post', $currentTime);
        $locationInfo = $this->construction->get_by_id($targetId);
        $target['longitude'] = $locationInfo->longitude;
        $target['latitude'] = $locationInfo->latitude;
        if($this->distance->calculateDistance($user, $target) < self::vision) {
            $player = $this->mproperty->get_by_id($playerId);
            $attackDamage = $player->workforce;
        	$state = $this->construction->attack($playerId, $targetId, $attackDamage);
        	echo $state; // durability
            if($state == 0) {
                // echo $locationInfo->playerId;
                $targetPlayer = $this->mproperty->get_by_id($locationInfo->playerId);
                $plunderWood = rand(0, round($targetPlayer->wood / 8));
                $plunderFood = rand(0, round($targetPlayer->food / 8));
                $plunderStone = rand(0, round($targetPlayer->stone / 8));
                $targetPlayer->wood = $targetPlayer->wood - $plunderWood;
                $targetPlayer->food = $targetPlayer->food - $plunderFood;
                $targetPlayer->stone = $targetPlayer->stone - $plunderStone;
                $player->wood = $player->wood + $plunderWood;
                $player->food = $player->food + $plunderFood;
                $player->stone = $player->stone + $plunderStone;
                $this->mproperty->update_property($playerId, $player);
                $this->mproperty->update_property($locationInfo->playerId, $targetPlayer);
            }
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
                    'field'=>'longitude',
                    'label'=>'自身位置的经度',
                    'rules'=>'required'
                ),
                array(
                    'field'=>'latitude',
                    'label'=>'自身位置的纬度',
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
        $user['longitude'] = $this->input->post('longitude');
        $user['latitude'] = $this->input->post('latitude');
        $locationInfo = $this->construction->get_by_id($targetId);
        $target['longitude'] = $locationInfo->longitude;
        $target['latitude'] = $locationInfo->latitude;
        if($this->distance->calculateDistance($user, $target) < self::vision) {
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
                    'field'=>'longitude',
                    'label'=>'自身位置的经度',
                    'rules'=>'required'
                ),
                array(
                    'field'=>'latitude',
                    'label'=>'自身位置的纬度',
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
        $user['longitude'] = $this->input->post('longitude');
        $user['latitude'] = $this->input->post('latitude');
        $currentTime = time();
        if($currentTime - $this->session->userdata('last_post') < 1) {
            return;
        }
        $this->session->set_userdata('last_post', $currentTime);
        $locationInfo = $this->resourcebase->get_by_id($targetId);
        $target['longitude'] = $locationInfo->longitude;
        $target['latitude'] = $locationInfo->latitude;
        if($this->distance->calculateDistance($user, $target) < self::vision) {
            $player = $this->mproperty->get_by_id($playerId);
            $workforce = $player->workforce;
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
        } else {
            echo -2;   
        }
    }

    //hire
    function hire() {
        $config = array(
                array(
                    'field'=>'quantity',
                    'label'=>'雇佣数量',
                    'rules'=>'required'
                )
        );
        $this->form_validation->set_rules($config);
        $playerId = $this->session->userdata('playerId');
        if($playerId == NULL) {
            echo -1;
            return;
        }
        $quantity = $this->input->post('quantity');
        $player = $this->mproperty->get_by_id($playerId);
        if($player->food < $quantity * (self::workerPrice)) {
            echo -2;
            return;
        }
        $player->food = $player->food - $quantity * (self::workerPrice);
        $player->workforce = $player->workforce + $quantity;
        $this->mproperty->update_property($playerId, $player);
        echo 1;
    }
}
