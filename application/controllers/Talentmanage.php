<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Talentmanage extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session', 'pagination'));
        $this->load->helper(array('form', 'url'));
        $this->load->model(array('department_model', 'student_model', 'useractionlog_model','surveylist_model','ratingslist_model'));

        $this->_logininfo = $this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            redirect('login', 'index');
        } else {
            $roleInfo = $this->session->userdata('roleInfo');
            $this->useractionlog_model->create(array('user_id' => $this->_logininfo['id'], 'url' => uri_string()));
            $returntalenturl=$this->session->userdata('returntalenturl');
            $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo,'returntalenturl'=>$returntalenturl));
        }

    }


    public function index($dpid){
        $this->session->set_userdata('returntalenturl', site_url('talentmanage/index/'.$dpid));
        $logininfo = $this->_logininfo;
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $where = "parent_id is null and company_code = '{$logininfo['company_code']}' ";
        $departments = $this->department_model->get_all($where);
        $current_department = $this->department_model->get_row(array('id' => $dpid));
        foreach ($departments as $k => $d) {
            if (($d['id'] == $current_department['parent_id']) || $d['id'] == $dpid) {
                $departments[$k]['departs'] = $this->department_model->get_all(array('parent_id' => $d['id']));
            }
        }
        $sql = "select student.*,department.name as department from " . $this->db->dbprefix('student') . " student left join " . $this->db->dbprefix('department') . " department on student.department_id=department.id where student.user_name <> '' and student.isdel = 2 and student.company_code='{$logininfo['company_code']}' ";
        if(!empty($dpid)){
            $sql .= " and (department_id=".$this->db->escape($dpid)." or department.parent_id = ".$this->db->escape($dpid).") ";
        }
        //总人数
        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = base_url('talentmanage/index/' . $dpid);
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);
        $sql .= " order by department_id,student.id desc limit " . ($page - 1) * $page_size . "," . $page_size;
        $students = $this->student_model->get_sql($sql);
        foreach ($students as $k=>$s){
            $sqlnum="select count(DISTINCT ca.id) as course_num,count(DISTINCT es.id) as evaluation_num,count(DISTINCT aa.id) as annual_num from (select ".$s['id']." as studentid) s left join ".$this->db->dbprefix('course_apply_list') ." ca on ca.student_id=s.studentid left join ".$this->db->dbprefix('company_ability_job_evaluation_student') ." es on es.student_id=s.studentid and es.isdel=2 left join ".$this->db->dbprefix('annual_answer') ." aa on aa.student_id=s.studentid GROUP BY s.studentid ";
            $query=$this->db->query($sqlnum);
            $res=$query->row_array();
            $students[$k]['course_num']=$res['course_num'];
            $students[$k]['evaluation_num']=$res['evaluation_num'];
            $students[$k]['annual_num']=$res['annual_num'];
        }

        $this->load->view('header');
        $this->load->view('talent_manage/index', array('departments' => $departments, 'current_department' => $current_department, 'students' => $students, 'total' => $total_rows, 'links' => $this->pagination->create_links()));
        $this->load->view('footer');

    }

    public function courserecords($studentid){
        $this->session->set_userdata('returncourseurl', site_url('talentmanage/courserecords/'.$studentid));
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $sql = "select course.*,teacher.name as teacher from " . $this->db->dbprefix('course_apply_list') . " a left join " . $this->db->dbprefix('course') . " course on a.course_id=course.id left join " . $this->db->dbprefix('teacher') . " teacher on course.`teacher_id`=teacher.id where course.`company_code`='{$this->_logininfo['company_code']}' and a.`student_id`='$studentid' ";
        //总数
        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = base_url('talentmanage/courserecords/' . $studentid);
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);
        $sql .= " order by course.id desc limit " . ($page - 1) * $page_size . "," . $page_size;
        $query=$this->db->query($sql);
        $courses=$query->result_array();
        foreach ($courses as $k=>$c){
            $courses[$k]['survey_num']=$this->surveylist_model->count(array('course_id'=>$c['id']));
            $courses[$k]['ratings_num']=$this->ratingslist_model->count(array('course_id'=>$c['id']));
        }
        $student=$this->student_model->get_row(array('id'=>$studentid));
        $this->load->view('header');
        $this->load->view('talent_manage/course_records', array('courses' => $courses,'student'=>$student, 'total' => $total_rows, 'links' => $this->pagination->create_links()));
        $this->load->view('footer');
    }

    public function evaluaterecords($studentid){
        $this->session->set_userdata('returnevaluationlisturl', 'talentmanage/evaluaterecords/'.$studentid);
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $sql = "select evaluation_student.ability_job_evaluation_id,evaluation.name as evaluation,evaluation_student.isdel,abilityjob.name as abilityjob,point,others_point,abilityjob.point_standard from " . $this->db->dbprefix('company_ability_job_evaluation_student') . " evaluation_student "
            . "left join " . $this->db->dbprefix('company_ability_job'). " abilityjob on evaluation_student.ability_job_id=abilityjob.id "
            . "left join " . $this->db->dbprefix('company_ability_job_evaluation'). " evaluation on evaluation_student.ability_job_evaluation_id=evaluation.id "
            . "where evaluation_student.company_code='".$this->_logininfo['company_code']."' and evaluation_student.student_id=$studentid ";
        //总数
        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = base_url('talentmanage/evaluaterecords/' . $studentid);
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);
        $sql .= " order by evaluation_student.id desc limit " . ($page - 1) * $page_size . "," . $page_size;
        $query=$this->db->query($sql);
        $evaluates = $query->result_array();
        $student=$this->student_model->get_row(array('id'=>$studentid));
        $this->load->view('header');
        $this->load->view('talent_manage/evaluate_records', array('evaluates' => $evaluates,'student'=>$student, 'total' => $total_rows, 'links' => $this->pagination->create_links()));
        $this->load->view('footer');
    }

    public function annualrecords($studentid){
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $sql = "select annual_survey.*,annual_answer.id as answer_id from " . $this->db->dbprefix('annual_answer') . " annual_answer "
            . "left join " . $this->db->dbprefix('annual_survey'). " annual_survey on annual_answer.annual_survey_id=annual_survey.id "
            . "where annual_answer.company_code='".$this->_logininfo['company_code']."' and annual_answer.student_id=$studentid and annual_answer.step=5 ";
        //总数
        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = base_url('talentmanage/annualrecords/' . $studentid);
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);
        $sql .= " order by annual_answer.id desc limit " . ($page - 1) * $page_size . "," . $page_size;
        $query=$this->db->query($sql);
        $annuals = $query->result_array();
        $student=$this->student_model->get_row(array('id'=>$studentid));
        $this->load->view('header');
        $this->load->view('talent_manage/annual_records', array('annuals' => $annuals,'student'=>$student, 'total' => $total_rows, 'links' => $this->pagination->create_links()));
        $this->load->view('footer');
    }



}
