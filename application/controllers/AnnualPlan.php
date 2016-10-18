<?php
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: lucus
 * Date: 2016/10/14
 * Time: 下午2:49
 */
class AnnualPlan extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session','pagination'));
        $this->load->helper(array('form', 'url'));
        $this->load->model(array('user_model','useractionlog_model', 'company_model', 'purview_model', 'industries_model','student_model','department_model','annualsurvey_model','annualplan_model'));

        $this->_logininfo = $this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            redirect('login', 'index');
        } else {
            $roleInfo = $this->session->userdata('roleInfo');
            $this->useractionlog_model->create(array('user_id'=>$this->_logininfo['id'],'url'=>uri_string()));
            $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));
        }

    }

    private function escapeVal($val){
        return !empty($val)?$this->db->escape($val):'';
    }

    //年度计划
    public function index(){
        $plans=$this->annualplan_model->get_all(array('company_code'=>$this->_logininfo['company_code']),'id','desc');
        $this->load->view('header');
        $this->load->view('annual_plan/list', compact('plans'));
        $this->load->view('footer');
    }

    //年度计划创建
    public function create(){
        $surveys=$this->annualsurvey_model->get_all(array('company_code'=>$this->_logininfo['company_code']));
        $this->load->view('header');
        $this->load->view('annual_plan/edit');
        $this->load->view('footer');
    }


    //是否是自己公司下的问卷
    private function isAllowAnnualid($surveyid,$redirect=true){
        if(empty($surveyid)||$this->annualsurvey_model->get_count(array('id' => $surveyid,'company_code'=>$this->_logininfo['company_code']))<=0){
            if($redirect){redirect(site_url('annualsurvey/list'));}
            return false;
        }else{
            return true;
        }
    }

}