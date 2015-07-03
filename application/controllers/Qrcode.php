<?php

class Qrcode extends CI_CONTROLLER {
	public function __construct()
    {
            parent::__construct();
            $this->load->helper('url');
    }
	function index(){
		echo "<img src='".base_url()."image\RealCraft.png' />";
	}
}