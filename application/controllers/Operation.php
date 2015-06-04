<?php

class Operation extends CI_Controller {
	const vision = 10;
	function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url'));
        $this->load->model('construction');
        $this->load->library('session');
        $this->load->model('distance');
    }

    function index() {
    	$this->load->view('operation/build');
    }

    function build() {
    	$config = array(
                array(
                	'field'=>'targetlocation',
                	'label'=>'目标位置',
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
        $targetLocation = $this->input->post('targetlocation');
        $selfLocation = $this->input->post('selflocation');
        if($this->distance->calculateDistance($selfLocation, $targetLocation) < self::vision) {
        	$workforce = 1;
        	$this->construction->build($playerId, $targetLocation, $workforce);
        }
        else {
        	echo "The target is too far to touch!";
        }
    }
}