<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Ability extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library(array('session', 'pagination'));
        $this->load->helper(array('form','url'));
        $this->load->model(array('user_model','useractionlog_model','company_model','abilityjob_model','ability_model','department_model','student_model'));

        $this->_logininfo=$this->session->userdata('loginInfo');
        $roleInfo = $this->session->userdata('roleInfo');
        $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));

        $this->useractionlog_model->create(array('user_id' => $this->_logininfo['id'], 'url' => uri_string()));

    }


    public function index() {
        $company=$this->company_model->get_row(array('code'=>$this->_logininfo['company_code']));
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $sql = "select job.*,cpjob.target,cpjob.target_one,cpjob.target_two,cpjob.target_student from " . $this->db->dbprefix('ability_job') . " job "
            . "left join " .$this->db->dbprefix('company_ability_job')." cpjob on cpjob.ability_job_id = job.id and cpjob.company_code='".$this->_logininfo['company_code']."' "
            . "where job.status = 1 and (job.type = 1 or job.company_code = " . $this->_logininfo['company_code'] . " or (job.industry_parent_id = '{$company['industry_parent_id']}' and job.industry_id = '{$company['industry_id']}' ))";

        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = site_url('ability/index');
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);

        $query = $this->db->query($sql . " order by job.id desc limit " . ($page - 1) * $page_size . "," . $page_size);
        $jobs = $query->result_array();

        //培训对象数据
        $deparone = $this->department_model->get_all(array('company_code' => $this->_logininfo['company_code'], 'level' => 0));
        if (!empty($deparone[0]['id'])) {
            $departwo = $this->department_model->get_all(array('parent_id' => $deparone[0]['id']));
        }
        if (!empty($departwo[0]['id'])) {
            $students = $this->student_model->get_all(array('department_id' => $departwo[0]['id'],'isdel'=>2));
        }
        $this->load->view ( 'header' );
        $this->load->view ( 'ability/index',compact('jobs','deparone','departwo','students'));
        $this->load->view ( 'footer' );
    }

    public function show($jobid){
        $sql = "select ability.* from " . $this->db->dbprefix('ability_job_model') . " job_model "
            . "left join " . $this->db->dbprefix('ability_model') . " ability on ability.id = job_model.model_id "
            . "where job_model.job_id = $jobid ";
        $query = $this->db->query($sql . " order by ability.type asc,job_model.id asc ");
        $res = $query->result_array();
        $abilities=array();
        foreach ($res as $a){
            $abilities[$a['type']][]=$a;
        }
        $this->load->view ( 'header' );
        $this->load->view ( 'ability/show',compact('abilities'));
        $this->load->view ( 'footer' );
    }


}
