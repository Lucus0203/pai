<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Ajax extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form','url'));
        $this->load->model(array('user_model','industries_model'));

    }


    public function getIndustries() {
        $parent_id=$this->input->post('parent_id');
        $industries = $this->industries_model->get_all(array('parent_id'=>$parent_id));
        echo json_encode($industries);

    }

}
