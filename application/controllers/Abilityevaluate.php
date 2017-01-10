<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Abilityevaluate extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library(array('session', 'pagination'));
        $this->load->helper(array('form','url'));
        $this->load->model(array('user_model','useractionlog_model','company_model','companyabilityjob_model','companyabilityjoblevel_model','department_model','student_model','companyabilityjobseries_model','companyabilitymodel_model','companyabilitysubcategory_model','companyabilityjobevaluation_model'));

        $this->_logininfo=$this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            redirect('login', 'index');
        } else {
            $roleInfo = $this->session->userdata('roleInfo');
            $this->useractionlog_model->create(array('user_id' => $this->_logininfo['id'], 'url' => uri_string()));
            $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));
        }

    }

    public function index() {
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $sql = " select abilityjob.name as ability_name,abilityjob.isdel as abilityjob_delstatus,evaluation.* from " . $this->db->dbprefix('company_ability_job_evaluation') . " evaluation "
            . " left join " . $this->db->dbprefix('company_ability_job') . " abilityjob on abilityjob.id = evaluation.ability_job_id "
            . " where abilityjob.company_code = '".$this->_logininfo['company_code']."' ";
        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = site_url('abilityevaluate/index');
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);
        $links=$this->pagination->create_links();

        $query = $this->db->query($sql . " order by evaluation.id desc limit " . ($page - 1) * $page_size . "," . $page_size);
        $abilityjobrecords = $query->result_array();
        foreach ($abilityjobrecords as $rk=>$r){
            $where=array('company_code'=>$this->_logininfo['company_code'],'ability_job_evaluation_id'=>$r['id'],'isdel'=>2);
            $this->db->where ($where);
            $abilityjobrecords[$rk]['evaluation_num']=$this->db->count_all_results('company_ability_job_evaluation_student');
            $where="company_code='".$this->_logininfo['company_code']."' and ability_job_evaluation_id=".$r['id']." and (status=2 or others_status=2) and isdel=2 ";
            $this->db->where ($where);
            $abilityjobrecords[$rk]['submit_num']=$this->db->count_all_results('company_ability_job_evaluation_student');
        }
        $this->load->view ( 'header' );
        $this->load->view ( 'ability_evaluate/index',compact('abilityjob','abilityjobrecords','links'));
        $this->load->view ( 'footer' );
    }


    //查看评估名单
    public function evaluationlist($evaluationid){
        if(empty($evaluationid)) {
            redirect(site_url('abilityevaluate/index'));
        }else{
            $this->session->set_userdata('returnevaluationlisturl', 'abilityevaluate/evaluationlist/'.$evaluationid);
            $evaluation=$this->companyabilityjobevaluation_model->get_row(array('id'=>$evaluationid));
            $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$evaluation['ability_job_id']));
            $sql = "select s.id,s.name,s.department_parent_id,s.department_id,department.name as department,point,others_point,.abilityjob.point_standard from " . $this->db->dbprefix('student') . " s "
                . "left join " . $this->db->dbprefix('department') . " department on s.department_id = department.id "
                . "left join " . $this->db->dbprefix('company_ability_job_evaluation_student') . " evaluation_student on evaluation_student.student_id = s.id "
                . "left join " . $this->db->dbprefix('company_ability_job'). " abilityjob on evaluation_student.ability_job_id=abilityjob.id "
                . "where evaluation_student.ability_job_evaluation_id=$evaluationid and evaluation_student.isdel=2 and s.company_code='".$this->_logininfo['company_code']."' and s.isdel=2 and s.isleaving=2 ";
            $query = $this->db->query($sql . " order by evaluation_student.id asc ");
            $students = $query->result_array();
            $this->load->view ( 'header' );
            $this->load->view ( 'ability_evaluate/evaluation_list',compact('evaluation','abilityjob','students'));
            $this->load->view ( 'footer' );
        }
    }

    //是否是自己公司下的能力模型
    private function isAllowAbilityjob($abilityjobid,$redirect=true){
        if($this->companyabilityjob_model->get_count(array('id' => $abilityjobid,'company_code'=>$this->_logininfo['company_code'],'isdel'=>2))<=0){
            if($redirect){redirect(site_url('abilityevaluate/index'));}
            return false;
        }else{
            return true;
        }
    }

}
