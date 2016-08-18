<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Index extends CI_Controller
{
    var $_logininfo;

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('form', 'url','download'));
        $this->load->model(array('user_model','useractionlog_model', 'course_model', 'teacher_model', 'homework_model','company_model'));

        $this->_logininfo = $this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            redirect('login', 'index');
        } else {
            $roleInfo = $this->session->userdata('roleInfo');
            $this->useractionlog_model->create(array('user_id' => $this->_logininfo['id'], 'url' => uri_string()));
            $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));
        }
    }


    public function index()
    {
        $logininfo = $this->_logininfo;
        $company = $this->company_model->get_row(array('code'=>$logininfo['company_code']));
        $sql = "select count(*) as num from " . $this->db->dbprefix('course') . " c where c.company_code = " . $logininfo['company_code'] . " and c.isdel=2 ";
        $query = $this->db->query($sql)->row_array();
        $courses_num = $query['num'];
        $query = $this->db->query(" select count(*) num from " . $this->db->dbprefix('teacher') . " t where company_code='{$logininfo['company_code']}' and isdel=2 ")->row_array();
        $teachers_num = $query['num'];
        $query = $this->db->query(" select count(*) num from " . $this->db->dbprefix('student') . " s where company_code='{$logininfo['company_code']}' and (role=1 or role=2) and isdel = 2 ")->row_array();
        $students_num = $query['num'];
        $query = $this->db->query(" select count(*) num from " . $this->db->dbprefix('student') . " s where company_code='{$logininfo['company_code']}' and role=2 ")->row_array();
        $adms_num = $query['num'];

        //最新课程
        $sql = "select c.*,t.name as teacher from " . $this->db->dbprefix('course') . " c "
            . "left join " . $this->db->dbprefix('teacher') . " t on c.teacher_id=t.id "
            . "where c.company_code = " . $logininfo['company_code'] . " and c.isdel=2 order by c.id desc limit 5 ";
        $query = $this->db->query($sql);
        $courses = $query->result_array();

        //学员登录二维码
        if(!file_exists(base_url().'uploads/login_qrcode/'.$logininfo['company_code'].'.png')){
            $this->load->library('ciqrcode');
            $params['data'] = $this->config->item('web_url') . 'login/index/' . $logininfo['company_code'] . '.html';
            $params['level'] = 'H';
            $params['size'] = 1025;
            $params['savename'] = './uploads/login_qrcode/' . $logininfo['company_code'].'.png';
            $this->ciqrcode->generate($params);
        }

        $this->load->view('header');
        $this->load->view('index', compact('courses_num', 'teachers_num', 'students_num', 'adms_num', 'courses','company'));
        $this->load->view('footer');
    }

    public function guidReaded()
    {
        $userinfo = $this->_logininfo;
        $this->user_model->update(array('guid_step' => 4), $userinfo['id']);
        $userinfo['guid_step'] = 4;
        $this->session->set_userdata('loginInfo', $userinfo);
        $this->load->vars(array('loginInfo' => $userinfo));
        echo 1;
    }

    public function loginqrcode(){
        force_download('./uploads/login_qrcode/' . $this->_logininfo['company_code'].'.png', NULL);
    }
}
