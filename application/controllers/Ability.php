<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Ability extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library(array('session', 'pagination'));
        $this->load->helper(array('form','url'));
        $this->load->model(array('user_model','useractionlog_model','company_model','abilityjob_model','companyabilityjob_model','ability_model','department_model','student_model'));

        $this->_logininfo=$this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            redirect('login', 'index');
        } else {
            $loginInfo = $this->_logininfo;
            $roleInfo = $this->session->userdata('roleInfo');
            if ($loginInfo['role'] != 1) {
                $redirect_flag = true;
                foreach ($roleInfo as $key => $value) {
                    if (strpos(current_url(), $key)) {//包含则不用跳转
                        $redirect_flag = false;
                    }
                }
                if ($redirect_flag) {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
            $this->useractionlog_model->create(array('user_id' => $this->_logininfo['id'], 'url' => uri_string()));
            $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));
        }

    }


    public function index() {
        $company=$this->company_model->get_row(array('code'=>$this->_logininfo['company_code']));
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $sql = "select job.id,job.name,cpjob.target,cpjob.target_one,cpjob.target_two,cpjob.target_student,cpjob.status from " . $this->db->dbprefix('ability_job') . " job "
            . "left join " .$this->db->dbprefix('company_ability_job')." cpjob on cpjob.ability_job_id = job.id and cpjob.company_code='".$this->_logininfo['company_code']."' "
            . "where job.status = 1 and (job.type = 1 or job.company_code = " . $this->_logininfo['company_code'] . " or (job.industry_parent_id = '{$company['industry_parent_id']}' and job.industry_id = '{$company['industry_id']}' ))";

        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = site_url('ability/index');
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);
        $links=$this->pagination->create_links();

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
        $this->load->view ( 'ability/index',compact('jobs','deparone','departwo','students','total_rows','links'));
        $this->load->view ( 'footer' );
    }

    /**
     * 岗位能力评估发布并通知
     */
    public function publish($jobid){
        if (!empty($jobid)) {
            $compjob=$this->companyabilityjob_model->get_row(array('company_code'=>$this->_logininfo['company_code'],'ability_job_id'=>$jobid));
            if(!empty($compjob)){
                $this->companyabilityjob_model->update(array('status'=>1),$compjob['id']);
            }else{
                $this->companyabilityjob_model->create(array('company_code'=>$this->_logininfo['company_code'],'ability_job_id'=>$jobid,'status'=>1));
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    /**
     * 岗位能力评估取消发布
     */
    public function unpublish($jobid){
        if (!empty($jobid)) {
            $compjob=$this->companyabilityjob_model->get_row(array('company_code'=>$this->_logininfo['company_code'],'ability_job_id'=>$jobid));
            if(!empty($compjob)){
                $this->companyabilityjob_model->update(array('status'=>2),$compjob['id']);
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * 查看可评估的岗位能力详情
     * @param $jobid
     */
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

    /**
     * 匹配评估对象
     */
    public function updateTarget(){
        $data['company_code']=$this->_logininfo['company_code'];
        $data['ability_job_id']=$this->input->post('jobid');
        $data['target_one']=$this->input->post('targetone');
        $data['target_two']=$this->input->post('targettwo');
        $data['target_student']=$this->input->post('targetstudent');
        $data['updated']=date('Y-m-d H:i:s');
        $targetone=$this->department_model->get_all(' id in ('.$data['target_one'].') ');
        $target='';
        if(!empty($targetone)){
            $targetone = array_column($targetone, 'name');
            $target .= implode(",",$targetone);
        }
        $targettwo=$this->department_model->get_all(' id in ('.$data['target_two'].') ');
        if(!empty($targettwo)){
            $targettwo = array_column($targettwo, 'name');
            $target .= implode(",",$targettwo);
        }
        $targetstudent=$this->student_model->get_all(' id in ('.$data['target_student'].') ');
        if(!empty($targetstudent)){
            $targetstudent = array_column($targetstudent, 'name');
            $target .= implode(",",$targetstudent);
        }
        $data['target']=$target;
        $compjob=$this->companyabilityjob_model->get_row(array('company_code'=>$data['company_code'],'ability_job_id'=>$data['ability_job_id']));
        if(!empty($compjob)){
            $this->companyabilityjob_model->update($data,$compjob['id']);
        }else{
            $this->companyabilityjob_model->create($data);
        }
        $res = mb_strlen($target, 'utf-8') > 20 ? mb_substr( $target,0,40,"utf-8").'...':$target;
        echo $res;
    }

    /**
     *
     * 查看评估对象
     * @param $jobid
     */
    public function targets($jobmodelid){
        $companyjob=$this->companyabilityjob_model->get_row(array('ability_job_id'=>$jobmodelid,'company_code'=>$this->_logininfo['company_code']));
        $abilityjob=$this->abilityjob_model->get_row(array('id'=>$jobmodelid));
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $sql = "select parent_depart.name as parent_department_name ,depart.name as department_name ,student.* from " . $this->db->dbprefix('student') . " student "
            . "left join " .$this->db->dbprefix('department')." parent_depart on student.department_parent_id = parent_depart.id and parent_depart.company_code='".$this->_logininfo['company_code']."' "
            . "left join " .$this->db->dbprefix('department')." depart on student.department_id = depart.id and depart.company_code='".$this->_logininfo['company_code']."' "
            . "where student.isdel = 2 and student.id in (".$companyjob['target_student'].") ";

        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = site_url('ability/index');
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);
        $links=$this->pagination->create_links();

        $query = $this->db->query($sql . " order by student.id desc limit " . ($page - 1) * $page_size . "," . $page_size);
        $students = $query->result_array();
        $this->load->view ( 'header' );
        $this->load->view ( 'ability/targets',compact('students','total_rows','links','companyjob','abilityjob'));
        $this->load->view ( 'footer' );

    }

}
