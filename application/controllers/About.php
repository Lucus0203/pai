<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class About extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library(array('session'));
		$this->load->helper(array('form','url'));
		$this->load->model(array('user_model'));
		
		$this->_logininfo=$this->session->userdata('loginInfo');
		
	}
	
	
	public function index() {
		$this->load->view ( 'header' );
		$this->load->view ( 'about/about');
		$this->load->view ( 'footer' );
		
	}
	
}
