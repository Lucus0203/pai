<?php
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: lucus
 * Date: 2016/10/14
 * Time: 下午2:49
 */
class Annualplan extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session','pagination'));
        $this->load->helper(array('form', 'url'));
        $this->load->model(array('user_model','useractionlog_model', 'company_model','teacher_model', 'purview_model', 'industries_model','student_model','teacher_model','department_model','annualsurvey_model','annualplan_model','annualplancourse_model','annualcourse_model','annualcoursetype_model','annualcourselibrary_model'));

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
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $this->load->database();
        //status 1进行中2未开始3已结束
        $sql = "select p.*,a.title as survey_title from " . $this->db->dbprefix('annual_plan') . " p left join " . $this->db->dbprefix('annual_survey') . " a on p.annual_survey_id = a.id "
            . "where p.company_code = " . $this->_logininfo['company_code'] ;
        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = site_url('annualplan/index');
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);

        $query = $this->db->query($sql . " order by p.id desc limit " . ($page - 1) * $page_size . "," . $page_size);
        $plans = $query->result_array();

        $this->load->view('header');
        $this->load->view('annual_plan/list', array('plans' => $plans, 'links' => $this->pagination->create_links()));
        $this->load->view('footer');
    }

    //年度计划创建
    public function create($surveyid){
        $act = $this->input->post('act');
        if (!empty($act)) {
            $plan = array('title' => $this->input->post('title'),
                'company_code' => $this->_logininfo['company_code'],
                'annual_survey_id' => $this->input->post('annual_survey_id'),
                'note' => $this->input->post('note'));
            $id=$this->annualplan_model->create($plan);
            redirect(site_url('annualplan/course/'.$id));
        }
        $surveys=$this->annualsurvey_model->get_all("company_code = '".$this->_logininfo['company_code']."' and isdel = 2 and unix_timestamp(time_end) < unix_timestamp(now()) ");
        if(!empty($surveyid)){
            $this->isAllowAnnualid($surveyid);
            $survey=$this->annualsurvey_model->get_row(array('id'=>$surveyid));
        }
        $this->load->view('header');
        $this->load->view('annual_plan/edit',compact('surveys','survey'));
        $this->load->view('footer');
    }
    //年度计划编辑
    public function edit($planid){
        $act = $this->input->post('act');
        if (!empty($act)) {
            $plan = array('title' => $this->input->post('title'),
                'note' => $this->input->post('note'));
            $this->annualplan_model->update($plan,$planid);
            redirect(site_url('annualplan/course/'.$planid));
        }
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        $survey=$this->annualsurvey_model->get_row(array('id'=>$plan['annual_survey_id']));
        $this->load->view('header');
        $this->load->view('annual_plan/edit',compact('surveys','survey','plan'));
        $this->load->view('footer');
    }


    //课程信息
    public function course($planid){
        $this->isAllowPlanid($planid);
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $openstatus=$this->input->get('openstatus');
        $typeid=$this->input->get('typeid');
        $parm=array();
        $sql = "select ac.id,ac.title as course_title,course.title,course.price,course.day,course.external,act.name as type_name,course.openstatus,count(aac.id) as num from " . $this->db->dbprefix('annual_course') . " ac left join ".$this->db->dbprefix('annual_plan_course')." course on ac.id=course.annual_course_id left join ".$this->db->dbprefix('annual_course_type')." act on ac.annual_course_type_id=act.id left join " . $this->db->dbprefix('annual_answer_course') . " aac on aac.annual_course_id = ac.id "
            . "where ac.company_code = " . $this->_logininfo['company_code'] ." and ac.annual_survey_id = ".$plan['annual_survey_id'] ;
        if(!empty($openstatus)){
            $sql.=$openstatus==1?" and openstatus = 1 ":" and (openstatus != 1 or openstatus is null) ";
            $parm['openstatus']=$openstatus;
        }
        if(!empty($typeid)){
            $sql.=" and ac.annual_course_type_id = ".$this->escapeVal($typeid);
            $parm['typeid']=$typeid;
        }
        $sql.=" group by ac.id ";
        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = site_url('annualplan/course/'.$planid). '?openstatus=' . $parm['openstatus'] . '&typeid=' . $parm['typeid'];
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);
        $links=$this->pagination->create_links();
        $query = $this->db->query($sql . " order by ac.id asc limit " . ($page - 1) * $page_size . "," . $page_size);
        $courses = $query->result_array();

        $total=$this->annualcourse_model->get_count("company_code=" . $this->_logininfo['company_code'] ." and annual_survey_id = ".$plan['annual_survey_id']);
        $total_open=$this->annualplancourse_model->get_count("company_code=" . $this->_logininfo['company_code'] ." and annual_plan_id = ".$planid." and openstatus = 1 ");
        $typies=$this->annualcoursetype_model->get_all(array('annual_survey_id'=>$plan['annual_survey_id']));
        $this->load->view('header');
        $this->load->view('annual_plan/course',compact('plan','courses','links','total_open','total','parm','typies'));
        $this->load->view('footer');
    }

    //开课课程信息
    public function opencourse($planid,$annualcourseid){
        $this->isAllowPlanid($planid);
        if(empty($annualcourseid)||$this->annualcourse_model->get_count(array('id' => $annualcourseid,'company_code'=>$this->_logininfo['company_code']))<=0){
            redirect(site_url('annualplan/index'));
            return false;
        }
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        $annualcourse=$this->annualcourse_model->get_row(array('id'=>$annualcourseid));
        $act=$this->input->post('act');
        if(!empty($act)){
            $c['title']=$this->input->post('title');
            $c['year']=$this->input->post('year');
            $c['month']=$this->input->post('month');
            $c['teacher_id']=!empty($this->input->post('teacher_id'))?$this->input->post('teacher_id'):null;
            $c['price']=!empty($this->input->post('price'))?$this->input->post('price'):null;
            $c['external']=$this->input->post('external');
            $c['day']=!empty($this->input->post('day'))?$this->input->post('day'):null;
            $c['supplier']=$this->input->post('supplier');
            $c['people']=!empty($this->input->post('people'))?$this->input->post('people'):null;
            $c['info']=$this->input->post('info');
            $c['openstatus']=1;
            if($this->annualplancourse_model->get_count(array('annual_plan_id'=>$planid,'annual_course_id'=>$annualcourseid))>0){
                $this->annualplancourse_model->update($c,array('annual_plan_id'=>$planid,'annual_course_id'=>$annualcourseid));
            }else{
                $c['company_code']=$this->_logininfo['company_code'];
                $c['annual_plan_id']=$planid;
                $c['annual_course_id']=$annualcourseid;
                $c['annual_course_type_id']=$annualcourse['annual_course_type_id'];
                $this->annualplancourse_model->create($c);
            }
            redirect($this->input->post('preurl'));
            return;
        }
        $course=$this->annualplancourse_model->get_row(array('annual_plan_id'=>$planid,'annual_course_id'=>$annualcourseid));
        $teachers = $this->teacher_model->get_all(array('company_code' => $this->_logininfo['company_code'], 'isdel' => 2));
        $library=$this->annualcourselibrary_model->get_row(array('id'=>$annualcourse['annual_course_library_id']));
        $preurl=$_SERVER['HTTP_REFERER'];

        $this->load->view('header');
        $this->load->view('annual_plan/course_open',compact('course','annualcourse','teachers','library','preurl','plan'));
        $this->load->view('footer');
    }

    //取消课程
    public function closecourse($planid,$annualcourseid){
        $this->isAllowPlanid($planid);
        if(empty($annualcourseid)||$this->annualcourse_model->get_count(array('id' => $annualcourseid,'company_code'=>$this->_logininfo['company_code']))<=0){
            redirect(site_url('annualplan/index'));
            return false;
        }
        $this->annualplancourse_model->update(array('openstatus'=>2),array('annual_plan_id'=>$planid,'annual_course_id'=>$annualcourseid));
        redirect($_SERVER['HTTP_REFERER']);
    }

    //年度培训计划
    public function plan($planid){
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        $typies=$this->annualcoursetype_model->get_all(array('annual_survey_id'=>$plan['annual_survey_id']));
        $res=array();
        foreach ($typies as $k=>$t){
            $where=" where pc.annual_plan_id = $planid ".
                " and pc.company_code = '".$this->_logininfo['company_code']."' ".
                " and pc.openstatus=1 ".
                " and pc.annual_course_type_id = ".$t['id'];
            //课程统计
            $tsql="select count(pc.id) as count_num , sum(people) as people_num,sum(price) as price_num from " . $this->db->dbprefix('annual_plan_course') . " pc ".$where;
            $query = $this->db->query($tsql);
            $t['total'] = $query->row_array();
            //课程详细
            $csql="select pc.*,teacher.name as teacher from " . $this->db->dbprefix('annual_plan_course') . " pc left join " . $this->db->dbprefix('teacher') . " teacher on pc.teacher_id=teacher.id ".$where;
            $query = $this->db->query($csql." order by pc.year,pc.month ");
            $t['courses'] = $query->result_array();
            $res[$k]=$t;
        }
        $teachersql="select teacher.* from ".$this->db->dbprefix('annual_plan_course')." plan_course left join ".$this->db->dbprefix('teacher')." teacher on plan_course.teacher_id=teacher.id where plan_course.openstatus=1 ";
        $query = $this->db->query($teachersql . " order by plan_course.id asc ");
        $teachers = $query->result_array();

        $this->load->view('header');
        $this->load->view('annual_plan/plan',compact('plan','teachers','res'));
        $this->load->view('footer');
    }

    //年度培训计划删除
    public function del($planid){
        $this->annualplancourse_model->del($planid);
        $this->annualplan_model->del($planid);
        redirect($_SERVER['HTTP_REFERER']);
        return ;
    }

    //年度培训计划统计
    public function analysis($planid){
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        $coursesql="select count(pc.id) as count_num , sum(people) as people_num,sum(price) as price_num,act.name as type_name from " . $this->db->dbprefix('annual_plan_course') . " pc left join " . $this->db->dbprefix('annual_course_type') . " act on pc.annual_course_type_id=act.id ".
            " where pc.annual_plan_id = $planid ".
            " and pc.company_code = '".$this->_logininfo['company_code']."' ".
            " and pc.openstatus=1 ".
            " group by pc.annual_course_type_id ";
        $query = $this->db->query($coursesql);
        $courses = $query->result_array();
        $trendsql = "select pc.id,concat(pc.year,pc.month) as ym from " . $this->db->dbprefix('annual_plan_course') ." pc ".
            " where pc.annual_plan_id = $planid ".
            " and pc.company_code = '".$this->_logininfo['company_code']."' ".
            " and pc.openstatus=1 ";
        $trendsql = "select count(*) as count_num,s.ym from ($trendsql) s group by s.ym order by s.ym asc ";
        $query = $this->db->query($trendsql);
        $data = $query->result_array();
        $datatrend=$trend=array();
        foreach ($data as $d){
            $trend[$d['ym']]=$d['count_num'];
        }
        if(count($data)>0){
            $first=$data[0];
            $last=end($data);
            $firstym=$first['ym'];
            $lastym=$last['ym'];
            for($ym=$firstym;$ym<=$lastym;$ym++){
                $datatrend[$ym]=!empty($trend[$ym])?$trend[$ym]:0;
            }
        }
        $this->load->view('header');
        $this->load->view('annual_plan/analysis',compact('plan','courses','datatrend'));
        $this->load->view('footer');
    }

    //是否是自己公司下的问卷
    private function isAllowAnnualid($surveyid,$redirect=true){
        if(empty($surveyid)||$this->annualsurvey_model->get_count(array('id' => $surveyid,'company_code'=>$this->_logininfo['company_code']))<=0){
            if($redirect){redirect(site_url('annualplan/index'));}
            return false;
        }else{
            return true;
        }
    }

    //是否是自己公司下的计划
    private function isAllowPlanid($planid,$redirect=true){
        if(empty($planid)||$this->annualplan_model->get_count(array('id' => $planid,'company_code'=>$this->_logininfo['company_code']))<=0){
            if($redirect){redirect(site_url('annualplan/index'));}
            return false;
        }else{
            return true;
        }
    }

}