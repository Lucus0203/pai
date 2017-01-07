<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Ajax extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form','url'));
        $this->load->model(array('user_model','industries_model','course_model','student_model','department_model'));
        $this->_logininfo = $this->session->userdata('loginInfo');

    }


    public function getIndustries() {
        $parent_id=$this->input->post('parent_id');
        $industries = $this->industries_model->get_all(array('parent_id'=>$parent_id));
        echo json_encode($industries);

    }

    //开启报名通知对象,Ajax获取二级部门及学员
    public function ApplyTargetDepartmentAndStudent($courseid)
    {
        $course = $this->course_model->get_row(array('id' => $courseid));
        $departmentid = $this->input->post('departmentid');
        $departs = empty($course['targettwo'])?array():$this->department_model->get_all(" id in (" . $course['targettwo'] . ") and company_code='".$this->_logininfo['company_code']."' and parent_id=".$departmentid);

        $departmentid=$departs[0]['id']??$departmentid;
        $students=array();
        if(!empty($departmentid)){
            $students = empty($course['targetstudent'])?array():$this->student_model->get_all(" id in (" . $course['targetstudent'] . ") and company_code='".$this->_logininfo['company_code']."' and department_id=".$departmentid." and isdel=2 and isleaving=2 ");
        }
        echo json_encode(array('departs' => $departs, 'students' => $students));
    }

    //开启报名通知对象,Ajax获取部门里的学员
    public function ApplyTargetAjaxStudent($courseid)
    {
        $course = $this->course_model->get_row(array('id' => $courseid));
        $departmentid = $this->input->post('departmentid');
        $students = empty($course['targetstudent'])?array():$this->student_model->get_all(" id in (" . $course['targetstudent'] . ") and company_code='".$this->_logininfo['company_code']."' and department_id=".$departmentid." and isdel=2 and isleaving=2 ");
        echo json_encode(array('students' => $students));
    }

    //通过学员ids获取学员
    public function getStudentsByIds(){
        $ids=$this->input->post('targetstudent');
        if(!empty($ids)){
            $sql = "select s.*,department.name as department from " . $this->db->dbprefix('student') . " s "
                . "left join " . $this->db->dbprefix('department') . " department on s.department_id = department.id "
                . "where s.id in (" . $ids . ") and s.company_code='".$this->_logininfo['company_code']."' and s.isdel=2 and s.isleaving=2 ";
            $query = $this->db->query($sql . " order by s.department_id asc,s.id asc ");
            $res = $query->result_array();
            echo json_encode(array('students' => $res));
        }else{
            echo 0;
        }
    }


}
