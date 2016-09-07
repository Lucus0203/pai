<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Html extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library(array('session'));
		$this->load->helper(array('form','url'));
		$this->load->model(array('user_model'));
		
		$this->_logininfo=$this->session->userdata('loginInfo');
        $roleInfo = $this->session->userdata('roleInfo');
        $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));
		
	}
	

    public function about() {
        $this->load->view ( 'header' );
        $this->load->view ( 'html/about');
        $this->load->view ( 'footer' );

    }

    public function ability() {
        $this->load->view ( 'header' );
        $this->load->view ( 'html/ability');
        $this->load->view ( 'footer' );

    }

    public function abilityCustom(){
        $this->load->view ( 'header' );
        $this->load->view ( 'html/ability_custom');
        $this->load->view ( 'footer' );
    }

    public function price(){
        $this->load->view ( 'header' );
        $this->load->view ( 'html/price');
        $this->load->view ( 'footer' );
    }
	
}
