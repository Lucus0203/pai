<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class AnnualSurvey extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session','pagination'));
        $this->load->helper(array('form', 'url'));
        $this->load->model(array('user_model','useractionlog_model', 'company_model', 'purview_model', 'industries_model','student_model','annualsurvey_model'));

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

    public function index()
    {
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $parm['status'] = $this->input->get('status');
        $parm['keyword'] = $this->input->get('keyword');
        $parm['time_start'] = $this->input->get('time_start');
        $parm['time_end'] = $this->input->get('time_end');
        $pvalue=array_map(array($this,'escapeVal'),$parm);//防sql注入
        $this->load->database();
        //status 1已发布2未发布3结束
        $sql = "select a.*,if( a.ispublic != 1,2,if( unix_timestamp(now()) > unix_timestamp(a.time_end),3,1) ) as status from " . $this->db->dbprefix('annual_survey') . " a "
            . "where a.company_code = " . $this->_logininfo['company_code'] . " and a.isdel=2 ";
        if ($parm['status'] == 2) {//待发布
            $sql .= " and a.ispublic != 1";
        } elseif ($parm['status'] == 3) {//已发布并结束
            $sql .= " and a.ispublic = 1 and unix_timestamp(now()) > unix_timestamp(a.time_end) ";
        } elseif ($parm['status'] == 1) {//已发布未结束
            $sql .= " and a.ispublic = 1 and unix_timestamp(now()) <= unix_timestamp(a.time_end) ";
        }
        if (!empty($parm['keyword'])) {
            $sql .= " and (a.title like '%" .  $this->db->escape_like_str($parm['keyword']) . "%' )";
        }
        if (!empty($parm['time_start'])) {
            $sql .= " and unix_timestamp(time_start) >= unix_timestamp(" . $pvalue['time_start']  . ") ";
        }
        if (!empty($parm['time_end'])) {
            $sql .= " and unix_timestamp(time_start) <= unix_timestamp(" . $pvalue['time_end'] . ") ";
        }
        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = site_url('annualsurvey/index') . '?keyword=' . $parm['keyword'] . '&time_start=' . $parm['time_start'] . '&time_end=' . $parm['time_end'] . '&status=' . $parm['status'];
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);

        $query = $this->db->query($sql . " order by a.id desc limit " . ($page - 1) * $page_size . "," . $page_size);
        $surveies = $query->result_array();

        $this->load->view('header');
        $this->load->view('annual_survey/list', array('surveies' => $surveies,'parm' => $parm, 'links' => $this->pagination->create_links()));
        $this->load->view('footer');
    }

    public function create(){
        $act = $this->input->post('act');
        $msg = '';
        $c = array();
        if (!empty($act)) {
            $logininfo = $this->_logininfo;
            $c = array('company_code' => $logininfo['company_code'],
                'title' => $this->input->post('title'),
                'time_start' => $this->input->post('time_start'),
                'time_end' => $this->input->post('time_end'),
                'info' => $this->input->post('info'),
                'created'=>date("Y-m-d H:i:s"));
            $c['ispublic'] = $this->input->post('public') == 1 ? 1 : 2;
            $id = $this->annualsurvey_model->create($c);
            //二维码
            $survey = array('qrcode'=>$id . rand(1000, 9999));
            $this->load->library('ciqrcode');
            $params['data'] = $this->config->item('web_url') . 'annualsurvey/' . $id . '/' . $survey['qrcode'];
            $params['level'] = 'H';
            $params['size'] = 1024;
            $params['savename'] = './uploads/annualqrcode/' . $survey['qrcode'] . '.png';
            $this->ciqrcode->generate($params);
            $this->annualsurvey_model->update($survey, $id);
            redirect(site_url('annualsurvey/info/'.$id));
            return;
        }
        $this->load->view('header');
        $this->load->view('annual_survey/edit', array());
        $this->load->view('footer');
    }

    public function info($surveyid){
        $this->isAllowAnnualid($surveyid);
        $survey = $this->annualsurvey_model->get_row(array('id' => $surveyid,'company_code' => $this->_logininfo['company_code']));
        $this->load->view('header');
        $this->load->view('annual_survey/info', compact('survey'));
        $this->load->view('footer');
    }

    //发布
    public function public($surveyid){
        $this->isAllowAnnualid($surveyid);
        if (!empty($surveyid)) {
            $c = $this->annualsurvey_model->get_row(array('id' => $surveyid));
            if ($c['company_code'] == $this->_logininfo['company_code']) {
                $this->annualsurvey_model->update(array('ispublic' => 1), $surveyid);
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    //课程删除
    public function del($surveyid)
    {
        $this->isAllowAnnualid($surveyid);
        if (!empty($surveyid)) {
            $c = $this->annualsurvey_model->get_row(array('id' => $surveyid));
            if ($c['company_code'] == $this->_logininfo['company_code']) {
                $this->annualsurvey_model->update(array('isdel' => 1), $surveyid);
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    //是否是自己公司下的问卷
    private function isAllowAnnualid($surveyid){
        if(empty($surveyid)||$this->annualsurvey_model->get_count(array('id' => $surveyid,'company_code'=>$this->_logininfo['company_code']))<=0){
            redirect(site_url('annualsurvey/list'));
            return false;
        }
    }

}
