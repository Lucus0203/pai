<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Abilitymanage extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library(array('session', 'pagination'));
        $this->load->helper(array('form','url'));
        $this->load->model(array('user_model','useractionlog_model','company_model','student_model','department_model','companyabilityjob_model','companyabilityjoblevel_model','companyabilityjobseries_model','companyabilitymodel_model','companyabilitysubcategory_model','companyabilityjobevaluation_model'));

        $this->_logininfo=$this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            redirect('login', 'index');
        } else {
            $roleInfo = $this->session->userdata('roleInfo');
            $this->useractionlog_model->create(array('user_id' => $this->_logininfo['id'], 'url' => uri_string()));
            $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));
        }

    }


    public function index($seriesid=null) {
        if(empty($seriesid)){
            $series=$this->companyabilityjobseries_model->get_row(array('company_code'=>$this->_logininfo['company_code'],'isdel'=>2));
            if(!empty($series['id'])) redirect(site_url('abilitymanage/index/'.$series['id']));
        }else{
            $this->isAllowSeriesid($seriesid);
            $page = $this->input->get('per_page', true);
            $page = $page * 1 < 1 ? 1 : $page;
            $page_size = 10;
            $sql = "select job.*,joblevel.name as joblevel,count(evaluation.id) as evaluation_num from " . $this->db->dbprefix('company_ability_job') . " job "
                . " left join " . $this->db->dbprefix('company_ability_job_level') . " joblevel on job.ability_job_level_id = joblevel.id "
                . " left join " . $this->db->dbprefix('company_ability_job_evaluation') . " evaluation on job.id = evaluation.ability_job_id "
                . " where job.isdel = 2 and job.company_code = " . $this->_logininfo['company_code'] . " and job.ability_job_series_id=$seriesid group by job.id ";
            $query = $this->db->query("select count(*) as num from ($sql) s ");
            $num = $query->row_array();
            $total_rows = $num['num'];
            $config['base_url'] = site_url('abilitymanage/index');
            $config['per_page'] = $page_size;
            $config['total_rows'] = $total_rows;
            $this->pagination->initialize($config);
            $links=$this->pagination->create_links();

            $query = $this->db->query($sql . " order by job.id desc limit " . ($page - 1) * $page_size . "," . $page_size);
            $abilityjobs = $query->result_array();
        }
        $serieses=$this->companyabilityjobseries_model->get_all(array('company_code'=>$this->_logininfo['company_code'],'isdel'=>2));
        $series=$this->companyabilityjobseries_model->get_row(array('id'=>$seriesid,'isdel'=>2));
        $this->load->view ( 'header' );
        $this->load->view ( 'ability_manage/index',compact('serieses','series','abilityjobs','links','total_rows'));
        $this->load->view ( 'footer' );
    }

    //创建岗位系列
    public function addjobseries(){
        $act=$this->input->post('act');
        if(!empty($act)){
            $series=array('company_code'=>$this->_logininfo['company_code'],
                'name'=>$this->input->post('series_name'),
                'hasmanage'=>$this->input->post('hasmanage'));
            $seriesid=$this->companyabilityjobseries_model->create($series);
            $name=$this->input->post('name');
            $level=$this->input->post('level');
            $series_type=$this->input->post('series_type');
            foreach ($name as $k=>$n){
                if(!empty($n)) {
                    $job_level = array('company_code' => $this->_logininfo['company_code'],
                        'ability_job_series_id' => $seriesid,
                        'name' => $n,
                        'series_type' => $series_type[$k],
                        'level' => $level[$k]);
                    $this->companyabilityjoblevel_model->create($job_level);
                }
            }
            redirect(site_url('abilitymanage/index'));
        }
        $this->load->view ( 'header' );
        $this->load->view ( 'ability_manage/job_series_edit',compact('jobs','links'));
        $this->load->view ( 'footer' );
    }

    //编辑岗位系列
    public function editjobseries($seriesid){
        $this->isAllowSeriesid($seriesid);
        $act=$this->input->post('act');
        if(!empty($act)){
            $series=array('company_code'=>$this->_logininfo['company_code'],
                'name'=>$this->input->post('series_name'),
                'hasmanage'=>$this->input->post('hasmanage'));
            $this->companyabilityjobseries_model->update($series,$seriesid);
            $name=$this->input->post('name');
            $id=$this->input->post('id');
            $level=$this->input->post('level');
            $series_type=$this->input->post('series_type');
            foreach ($name as $k=>$n){
                if(!empty($n)){
                    $job_level=array('company_code'=>$this->_logininfo['company_code'],
                        'ability_job_series_id'=>$seriesid,
                        'name'=>$n,
                        'series_type'=>$series_type[$k],
                        'level'=>$level[$k]);
                    if(empty($id[$k])){
                        $this->companyabilityjoblevel_model->create($job_level);
                    }else{
                        $this->companyabilityjoblevel_model->update($job_level,$id[$k]);
                    }
                }
            }
            echo '<script>history.go(-2);</script>';
            return;
        }
        $series=$this->companyabilityjobseries_model->get_row(array('id'=>$seriesid));
        $projob=$this->companyabilityjoblevel_model->get_all(array('ability_job_series_id'=>$seriesid,'series_type'=>1));
        $magjob=$this->companyabilityjoblevel_model->get_all(array('ability_job_series_id'=>$seriesid,'series_type'=>2));
        $this->load->view ( 'header' );
        $this->load->view ( 'ability_manage/job_series_edit',compact('series','projob','magjob'));
        $this->load->view ( 'footer' );
    }

    //删除岗位职级
    public function deljoblevel($joblevelid){
        if($this->companyabilityjob_model->get_count(array('ability_job_level_id'=>$joblevelid))<=0 && $this->companyabilityjoblevel_model->get_count(array('id'=>$joblevelid,'company_code'=>$this->_logininfo['company_code']))>0){//不包含能力模型
            $this->companyabilityjoblevel_model->del($joblevelid);
            echo '1';//删除成功
        }else{
            echo '2';//删除失败
        }
    }

    //删除岗位系列
    public function delseries($seriesid){
        if($this->companyabilityjob_model->get_count(array('ability_job_series_id'=>$seriesid))<=0 && $this->companyabilityjobseries_model->get_count(array('id'=>$seriesid,'company_code'=>$this->_logininfo['company_code']))>0){//不包含能力模型
            $this->companyabilityjobseries_model->update(array('isdel'=>1),$seriesid);
            echo '1';//删除成功
        }else{
            echo '2';//删除失败
        }
    }

    //创建能力模型
    public function createabilityjob($seriesid){
        $this->isAllowSeriesid($seriesid);
        $series=$this->companyabilityjobseries_model->get_row(array('id'=>$seriesid));
        $prolevels=$this->companyabilityjoblevel_model->get_all(array('ability_job_series_id'=>$seriesid,'series_type'=>1));
        $maglevels=$this->companyabilityjoblevel_model->get_all(array('ability_job_series_id'=>$seriesid,'series_type'=>2));

        $act=$this->input->post('act');
        if(!empty($act)){
            $abilityjob=array('company_code'=>$this->_logininfo['company_code'],
                'name'=>$this->input->post('name'),
                'ability_job_series_id'=>$seriesid,
                'ability_job_level_id'=>$this->input->post('ability_job_level_id'),
                'hasleadership'=>$this->input->post('hasleadership'),
                'created'=>date("Y-m-d H:i:s"));
            $abilityjobid=$this->companyabilityjob_model->create($abilityjob);
            redirect(site_url('abilitymanage/detailabilityjob/'.$abilityjobid));
            return;
        }
        $this->load->view ( 'header' );
        $this->load->view ( 'ability_manage/abilityjob_edit',compact('series','prolevels','maglevels'));
        $this->load->view ( 'footer' );
    }

    //编辑能力模型
    public function editabilityjob($seriesid,$abilityjobid){
        $this->isAllowSeriesid($seriesid);
        $this->isAllowAbilityjob($abilityjobid);
        $series=$this->companyabilityjobseries_model->get_row(array('id'=>$seriesid));
        $prolevels=$this->companyabilityjoblevel_model->get_all(array('ability_job_series_id'=>$seriesid,'series_type'=>1));
        $maglevels=$this->companyabilityjoblevel_model->get_all(array('ability_job_series_id'=>$seriesid,'series_type'=>2));
        $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$abilityjobid));

        $act=$this->input->post('act');
        if(!empty($act)){
            $abilityjob=array('company_code'=>$this->_logininfo['company_code'],
                'name'=>$this->input->post('name'),
                'ability_job_series_id'=>$seriesid,
                'ability_job_level_id'=>$this->input->post('ability_job_level_id'),
                'hasleadership'=>$this->input->post('hasleadership'));
            $this->companyabilityjob_model->update($abilityjob,$abilityjobid);
            echo '<script>history.go(-2);</script>';
            return;
        }
        $this->load->view ( 'header' );
        $this->load->view ( 'ability_manage/abilityjob_edit',compact('series','abilityjob','prolevels','maglevels'));
        $this->load->view ( 'footer' );

    }

    //复制能力模型
    public function copyabilityjob($abilityjobid){
        $this->isAllowAbilityjob($abilityjobid);
        $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$abilityjobid));
        $seriesid=$abilityjob['ability_job_series_id'];
        $series=$this->companyabilityjobseries_model->get_row(array('id'=>$abilityjob['ability_job_series_id']));
        $prolevels=$this->companyabilityjoblevel_model->get_all(array('ability_job_series_id'=>$seriesid,'series_type'=>1));
        $maglevels=$this->companyabilityjoblevel_model->get_all(array('ability_job_series_id'=>$seriesid,'series_type'=>2));

        $act=$this->input->post('act');
        if(!empty($act)){
            $abilityjob=array('company_code'=>$this->_logininfo['company_code'],
                'name'=>$this->input->post('name'),
                'ability_job_series_id'=>$seriesid,
                'ability_job_level_id'=>$this->input->post('ability_job_level_id'),
                'hasleadership'=>$this->input->post('hasleadership'),
                'created'=>date("Y-m-d H:i:s"));
            $newabilityjobid=$this->companyabilityjob_model->create($abilityjob);
            $copysql="INSERT INTO ".$this->db->dbprefix('company_ability_job_model')." (company_code,type,ability_job_id,ability_model_id,level_standard,created) select '".$this->_logininfo['company_code']."',type,$newabilityjobid,ability_model_id,level_standard,'".date("Y-m-d H:i:s")."' from ".$this->db->dbprefix('company_ability_job_model')." oldjob where oldjob.ability_job_id=$abilityjobid ";
            $this->db->query($copysql);
            redirect(site_url('abilitymanage/detailabilityjob/'.$newabilityjobid));
            return;
        }
        $abilityjob['name']=$abilityjob['name'].'（副本）';
        $this->load->view ( 'header' );
        $this->load->view ( 'ability_manage/abilityjob_edit',compact('series','abilityjob','prolevels','maglevels'));
        $this->load->view ( 'footer' );
    }

    //模型名称是否被使用
    public function isexistedname($abilityjobid=null){
        $name = $this->input->post('name');
        $where = " isdel=2 and name = '$name' and company_code='".$this->_logininfo['company_code']."' ";
        $where .=!empty($abilityjobid)?" and id<>$abilityjobid ":'';
        echo $this->companyabilityjob_model->get_count($where);
    }

    public function delabilityjob($abilityjobid){
        if($this->isAllowAbilityjob($abilityjobid,false) && $this->companyabilityjob_model->get_count(array('id'=>$abilityjobid,'company_code'=>$this->_logininfo['company_code']))>0){
            $this->companyabilityjob_model->update(array('isdel'=>1),$abilityjobid);
            echo '1';//删除成功
        }else{
            echo '2';//删除失败
        }
    }

    //能力模型详情
    public function detailabilityjob($abilityjobid){
        //能力模型详细
        $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$abilityjobid));
        $types=array('1'=>'专业能力','2'=>'通用能力','3'=>'领导力','4'=>'个性','5'=>'经验');
        $abilities=array('1'=>array('type'=>'专业能力'),'2'=>array('type'=>'通用能力'),'3'=>array('type'=>'领导力'),'4'=>array('type'=>'个性'),'5'=>array('type'=>'经验'));
        $levelradar=array('1'=>array('level_standard'=>0,'level_total'=>0),'2'=>array('level_standard'=>0,'level_total'=>0),'3'=>array('level_standard'=>0,'level_total'=>0),'4'=>array('level_standard'=>0,'level_total'=>0),'5'=>array('level_standard'=>0,'level_total'=>0));

        $sql = "select ability.*,cajm.level_standard from " . $this->db->dbprefix('company_ability_job_model') . " cajm "
            . "left join " . $this->db->dbprefix('company_ability_model') . " ability on ability.id = cajm.ability_model_id "
            . "where cajm.company_code = '".$this->_logininfo['company_code']."' and cajm.ability_job_id = $abilityjobid";
        if($abilityjob['hasleadership']=='2'){
            $sql.=" and cajm.type <> '3' ";
            unset($abilities['3']);
            unset($levelradar['3']);
            unset($types['3']);
        }
        $query = $this->db->query($sql . " order by ability.type asc,cajm.id asc ");
        $res = $query->result_array();
        $entries_count=0;
        foreach ($res as $a){
            $abilities[$a['type']]['abilities'][]=$a;
            $levelradar[$a['type']]['level_standard']+=$a['level_standard']*1;
            $levelradar[$a['type']]['level_total']+=$a['level']*1;
            $entries_count++;
        }
        if($entries_count==0){//未设置标准
            foreach($levelradar as $k=>$v){
                $levelradar[$k]=array('level_standard'=>5,'level_total'=>5);
            }
        }

        $this->load->view ( 'header' );
        $this->load->view ( 'ability_manage/abilityjob_detail',compact('types','abilityjob','abilities','levelradar','entries_count'));
        $this->load->view ( 'footer' );
    }

    //保存岗位能力模型及词条
    public function saveabilityjobentry($abilityjobid){
        $levels=$this->input->post('levels');
        $types=$this->input->post('types');
        $mids=$this->input->post('mids');
        $levels=explode(',',$levels);
        $types=explode(',',$types);
        $mids=explode(',',$mids);
        if(empty($mids)){
            echo json_encode(array('success'=>'failure','msg'=>'保存失败,数据不正确!'));
            return false;
        }
        //判断学员是否已提交评估
        $sql = "select count(*) as num from " . $this->db->dbprefix('company_ability_job_student_assess') . " assess "
            . "where assess.company_code='".$this->_logininfo['company_code']."' and assess.ability_job_id = $abilityjobid ";
        $query = $this->db->query($sql);
        $num = $query->row_array();
        $total_rows = $num['num'];
        if($total_rows>0){
            echo json_encode(array('success'=>'failure','msg'=>'保存失败,已有学员提交评估!'));
            return false;
        }
        //清除旧数据
        $clearsql="DELETE FROM `pai_company_ability_job_model` WHERE `company_code` = '".$this->_logininfo['company_code']."' and `ability_job_id` = $abilityjobid ";
        $this->db->query($clearsql);
        //插入新数据
        $insertsql="INSERT INTO `".$this->db->dbprefix('company_ability_job_model')."` (`company_code`, `type`, `ability_job_id`, `ability_model_id`, `level_standard`, `created`) VALUES ";
        $datastr='';
        foreach ($mids as $k=>$mid){
            $d="('{$this->_logininfo['company_code']}', '{$types[$k]}', '$abilityjobid', '{$mid}', '{$levels[$k]}', '".date('Y-m-d H:i:s')."')";
            $datastr.=($datastr=='')?$d:','.$d;
        }
        $insertsql.=$datastr.';';
        $this->db->query($insertsql);
        //标准总分
        $point_sql=" select sum(jm.level_standard) as level_standard,sum(m.level) as level from ".$this->db->dbprefix('company_ability_job_model')." jm left join ".$this->db->dbprefix('company_ability_model')." m on jm.ability_model_id=m.id where jm.ability_job_id = $abilityjobid ";
        $query=$this->db->query($point_sql);
        $res=$query->row_array();
        $point_standard=$res['level']==0?0:round($res['level_standard']/$res['level']*5,1);
        $this->companyabilityjob_model->update(array('point_standard'=>$point_standard),$abilityjobid);

        echo json_encode(array('success'=>'ok'));
    }

    //获取能力词条及子分类
    public function getcateoriesentries(){
        $model_type=$this->input->post('model_type');
        if(!empty($model_type)){
            $sql = "select id,name,info,IFNULL(ability_subcategory_id,'noassigned') as category from " . $this->db->dbprefix('company_ability_model') . " ability "
                . " where ability.isdel =2 and ability.company_code = " . $this->_logininfo['company_code'] . " and ability.type=$model_type ";
            $query = $this->db->query($sql . " order by ability.id desc ");
            $entries = $query->result_array();
            $categories=$this->companyabilitysubcategory_model->get_all(array('company_code'=>$this->_logininfo['company_code'],'type'=>$model_type));
            if(count($categories)>0 && $this->companyabilitymodel_model->get_count("company_code='".$this->_logininfo['company_code']."' and ability_subcategory_id is null and type ='$model_type' and isdel=2 ")>0){
                $categories[]=array('id'=>'noassigned','name'=>'未分配');
            }
            echo json_encode(array('entries'=>$entries,'categries'=>$categories));
        }else{
            echo 0;
        }

    }

    //获取详细能力词条
    public function getentries(){
        $entryids=$this->input->post('entryids');
        if(!empty($entryids)) {
            $sql = "select * from " . $this->db->dbprefix('company_ability_model') . " ability "
                . " where ability.isdel = 2 and ability.company_code = " . $this->_logininfo['company_code'] . " and ability.id in ($entryids) ";
            $query = $this->db->query($sql . " order by ability.id desc ");
            $entries = $query->result_array();
            echo json_encode(array('entries'=>$entries));
        }else{
            echo 0;
        }
    }

    //评估记录列表
    public function abilityjobrecords($abilityjobid){
        $this->isAllowAbilityjob($abilityjobid);
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $sql = "select evaluation.* from " . $this->db->dbprefix('company_ability_job_evaluation') . " evaluation "
            . " where evaluation.ability_job_id = $abilityjobid ";
        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = site_url('abilitymanage/index');
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

        $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$abilityjobid));
        $this->load->view ( 'header' );
        $this->load->view ( 'ability_manage/abilityjob_records',compact('abilityjob','abilityjobrecords','links'));
        $this->load->view ( 'footer' );
    }

    //发起评估
    public function createevaluation($abilityjobid){
        if(!empty($abilityjobid)){
            $this->isAllowAbilityjob($abilityjobid);
            $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$abilityjobid));
            $job_series=$this->companyabilityjobseries_model->get_row(array('id'=>$abilityjob['ability_job_series_id']));
            $job_level=$this->companyabilityjoblevel_model->get_row(array('id'=>$abilityjob['ability_job_level_id']));
        }else{
            $job_series=$this->companyabilityjobseries_model->get_all(array('company_code'=>$this->_logininfo['company_code'],'isdel'=>2));
        }
        $act = $this->input->post('act');
        if(!empty($act)){
            $obj = array('ability_job_id' => $this->input->post('ability_job_id'),
                'name' => $this->input->post('name'),
                'time_end' => $this->input->post('time_end'),
                'targetstudent' => $this->input->post('targetstudent'));
            if($this->isAllowAbilityjob($obj['ability_job_id'],false)){
                $evaluation_id=$this->companyabilityjobevaluation_model->create($obj);
                $this->updateTargetDataAndNotify($evaluation_id);
                $backuri=$this->input->post('backuri');
                redirect($backuri);
            }

        }
        //培训对象数据
        $deparone = $this->department_model->get_all(array('company_code' => $this->_logininfo['company_code'], 'level' => 0));
        $target['deparone'] = $deparone;
        $this->load->view ( 'header' );
        $this->load->view ( 'ability_manage/abilityjob_evaluation',compact('abilityjob','job_series','job_level','target'));
        $this->load->view ( 'footer' );
    }

    //获取岗位职级
    public function getabilityjobbyseries($seriesid){
        $prolevels=$this->companyabilityjoblevel_model->get_all(array('company_code'=>$this->_logininfo['company_code'],'ability_job_series_id'=>$seriesid,'series_type'=>1));
        $maglevels=$this->companyabilityjoblevel_model->get_all(array('company_code'=>$this->_logininfo['company_code'],'ability_job_series_id'=>$seriesid,'series_type'=>2));
        echo json_encode(array('prolevels'=>$prolevels,'maglevels'=>$maglevels));
    }
    //获取能力模型
    public function getabilityjobbylevel($seriesid,$levelid){
        $jobs=$this->companyabilityjob_model->get_all(array('company_code'=>$this->_logininfo['company_code'],'ability_job_series_id'=>$seriesid,'ability_job_level_id'=>$levelid,'isdel'=>2));
        echo json_encode(array('jobs'=>$jobs));
    }

    //编辑评估
    public function editevaluation($evaluationid){
        if(empty($evaluationid)) {
            redirect(site_url('abilitymanage/index'));
        }else{
            $evaluation=$this->companyabilityjobevaluation_model->get_row(array('id'=>$evaluationid));
            $this->isAllowAbilityjob($evaluation['ability_job_id']);
            $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$evaluation['ability_job_id']));
            $job_series=$this->companyabilityjobseries_model->get_row(array('id'=>$abilityjob['ability_job_series_id']));
            $job_level=$this->companyabilityjoblevel_model->get_row(array('id'=>$abilityjob['ability_job_level_id']));
        }
        $act = $this->input->post('act');
        if(!empty($act)){
            $obj = array('ability_job_id' => $this->input->post('ability_job_id'),
                'name' => $this->input->post('name'),
                'time_end' => $this->input->post('time_end'),
                'targetstudent' => $this->input->post('targetstudent'));
            if($this->isAllowAbilityjob($obj['ability_job_id'],false)){
                $this->companyabilityjobevaluation_model->update($obj,$evaluationid);
                $this->updateTargetDataAndNotify($evaluationid);
                $backuri=$this->input->post('backuri');
                redirect($backuri);
            }

        }
        //培训对象数据
        $deparone = $this->department_model->get_all(array('company_code' => $this->_logininfo['company_code'], 'level' => 0));
        $sql = "select s.id,s.name,s.department_parent_id,s.department_id,department.name as department from " . $this->db->dbprefix('student') . " s "
            . "left join " . $this->db->dbprefix('department') . " department on s.department_id = department.id "
            . "where s.id in ('" . $evaluation['targetstudent'] . "') and s.company_code='".$this->_logininfo['company_code']."' and s.isdel=2 and s.isleaving=2 ";
        $query = $this->db->query($sql . " order by s.department_id asc,s.id asc ");
        $students = $query->result_array();
        $target['deparone'] = $deparone;
        $target['targetone'] = implode(',',array_column($students,'department_parent_id'));
        $target['targettwo'] = implode(',',array_column($students,'department_id'));
        $target['targetstudent'] = $evaluation['targetstudent'];
        $this->load->view ( 'header' );
        $this->load->view ( 'ability_manage/abilityjob_evaluation',compact('evaluation','abilityjob','job_series','job_level','target','students'));
        $this->load->view ( 'footer' );
    }

    public function delevaluation($evaluationid){
        $this->db->where ( 'ability_job_evaluation_id', $evaluationid );
        $this->db->update ( 'company_ability_job_evaluation_student', array('isdel'=>1) );
        $this->companyabilityjobevaluation_model->del($evaluationid);
        redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * 更新公司能力评估学员表并通知
     * @param $ability_job_id
     */
    private function updateTargetDataAndNotify($evaluation_id){
        $evaluation=$this->companyabilityjobevaluation_model->get_row(array('id'=>$evaluation_id));
        $studentids=$evaluation['targetstudent'];
        if(!empty($studentids)){
            //已经不存在评估的对象改为删除状态
            $sql = "update ".$this->db->dbprefix('company_ability_job_evaluation_student')." set isdel = 1 where company_code='".$this->_logininfo['company_code']."' and	ability_job_evaluation_id=$evaluation_id and student_id not in ($studentids) ";
            $this->db->query($sql);
            //循环现有的评估对象,如果已有但是删除状态则更改删除状态,如果无则新增并通知
            $studentids=explode(',',$studentids);
            foreach ($studentids as $sid){
                $where="company_code='".$this->_logininfo['company_code']."' and ability_job_evaluation_id=$evaluation_id and student_id = $sid ";
                $query = $this->db->get_where ( 'company_ability_job_evaluation_student', $where );
                $saj=$query->row_array();
                if(!empty($saj)){//已有
                    if($saj['isdel']==1){//但是删除状态则更改正常状态
                        $this->db->where ( $where );
                        $this->db->update ( 'company_ability_job_evaluation_student', array('point'=>null,'status'=>1,'others_id'=>null,'others_point'=>null,'others_status'=>1,'isdel'=>2) );//恢复可用
                    }
                }else{//无则新增并通知
                    $obj=array('company_code'=>$this->_logininfo['company_code'],'ability_job_id'=>$evaluation['ability_job_id'],'ability_job_evaluation_id'=>$evaluation_id,'student_id'=>$sid,'created'=>date("Y-m-d H:i:s"));
                    $this->db->insert ( 'company_ability_job_evaluation_student', $obj );
                    //评估通知
                    $this->load->library(array('notifyclass'));
                    $this->notifyclass->abilitypublish($evaluation_id,$sid);
                }
            }

        }
    }

    //查看评估名单
    public function evaluationlist($evaluationid){
        if(empty($evaluationid)) {
            redirect(site_url('abilitymanage/index'));
        }else{
            $this->session->set_userdata('returnevaluationlisturl', 'abilitymanage/evaluationlist/'.$evaluationid);
            $evaluation=$this->companyabilityjobevaluation_model->get_row(array('id'=>$evaluationid));
            $this->isAllowAbilityjob($evaluation['ability_job_id']);
            $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$evaluation['ability_job_id']));

            $sql = "select s.id,s.name,s.department_parent_id,s.department_id,department.name as department,point,others_point,.abilityjob.point_standard from " . $this->db->dbprefix('student') . " s "
                . "left join " . $this->db->dbprefix('department') . " department on s.department_id = department.id "
                . "left join " . $this->db->dbprefix('company_ability_job_evaluation_student') . " evaluation_student on evaluation_student.student_id = s.id "
                . "left join " . $this->db->dbprefix('company_ability_job'). " abilityjob on evaluation_student.ability_job_id=abilityjob.id "
                . "where evaluation_student.ability_job_evaluation_id=$evaluationid and evaluation_student.isdel=2 and s.company_code='".$this->_logininfo['company_code']."' and s.isdel=2 and s.isleaving=2 ";
            $query = $this->db->query($sql . " order by evaluation_student.id asc ");
            $students = $query->result_array();
            $this->load->view ( 'header' );
            $this->load->view ( 'ability_manage/abilityjob_evaluation_list',compact('evaluation','abilityjob','students'));
            $this->load->view ( 'footer' );
        }
    }

    //删除名单
    public function delevaluationlist($evaluationid,$studentid){
        $evaluation=$this->companyabilityjobevaluation_model->get_row(array('id'=>$evaluationid));
        $arr=explode(',',$evaluation['targetstudent']);
        if(($key = array_search($studentid, $arr)) !== false) {
            unset($arr[$key]);
        }
        $targets=implode(',',$arr);
        $this->companyabilityjobevaluation_model->update(array('targetstudent'=>$targets),$evaluationid);
        $this->db->where ( array('ability_job_evaluation_id'=>$evaluationid,'student_id'=>$studentid) );
        $this->db->update ( 'company_ability_job_evaluation_student', array('isdel'=>1) );
        redirect($_SERVER['HTTP_REFERER']);
    }

    //评估报告
    public function reportevaluation($evaluationid,$studentid){
        $returnevaluationlisturl=$this->session->userdata('returnevaluationlisturl');
        $evaluation=$this->companyabilityjobevaluation_model->get_row(array('id'=>$evaluationid));
        $this->isAllowAbilityjob($evaluation['ability_job_id']);
        $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$evaluation['ability_job_id']));
        $student=$this->student_model->get_row(array('id'=>$studentid));
        $where="company_code='".$this->_logininfo['company_code']."' and ability_job_evaluation_id=$evaluationid and student_id = $studentid ";
        $query = $this->db->get_where ( 'company_ability_job_evaluation_student', $where );
        $evaluation_student=$query->row_array();
        //自评
        $selfsql="select type,sum(point) as point,sum(level) as level from ".$this->db->dbprefix('company_ability_job_student_assess')." assess where ability_job_evaluation_id=$evaluationid and isothersevaluation=2 and student_id=$studentid and company_code='".$this->_logininfo['company_code']."' group by type ";
        $query = $this->db->query($selfsql . " order by type asc ");
        $self = $query->result_array();
        $dataself = $this->relistmodel($self);
        //他评
        $otherssql="select type,sum(point) as point,sum(level) as level from ".$this->db->dbprefix('company_ability_job_student_assess')." assess where ability_job_evaluation_id=$evaluationid and isothersevaluation=1 and student_id=$studentid and company_code='".$this->_logininfo['company_code']."' group by type ";
        $query = $this->db->query($otherssql . " order by type asc ");
        $other = $query->result_array();
        $dataother = $this->relistmodel($other);
        //标准
        $standardsql="select jobmodel.type,sum(level_standard) as point,sum(level) as level from ".$this->db->dbprefix('company_ability_job_model')." jobmodel left join 
        ".$this->db->dbprefix('company_ability_model')." model on jobmodel.ability_model_id=model.id where jobmodel.ability_job_id=".$evaluation['ability_job_id']." and jobmodel.company_code='".$this->_logininfo['company_code']."' group by jobmodel.type ";
        $query = $this->db->query($standardsql . " order by type asc ");
        $standar = $query->result_array();
        $datastandar = $this->relistmodel($standar);
        $this->load->view ( 'header' );
        $this->load->view ( 'ability_manage/report_evaluation',compact('evaluation','abilityjob','student','evaluation_student','returnevaluationlisturl','dataself','dataother','datastandar'));
        $this->load->view ( 'footer' );
    }

    private function relistmodel($obj){
        $arr=array();
        $point_total=$level_total=0;
        foreach ($obj as $v){
            $arr[$v['type']]=$v['level']>0?round($v['point']/$v['level']*5,1):0;
            $point_total+=$v['point'];
            $level_total+=$v['level'];
        }
        $arr['point_total']=$level_total>0?round($point_total/$level_total*5,1):0;
        return $arr;
    }

    //自评结果
    public function selfevaluation($evaluationid,$studentid){
        $returnevaluationlisturl=$this->session->userdata('returnevaluationlisturl');
        $evaluation=$this->companyabilityjobevaluation_model->get_row(array('id'=>$evaluationid));
        $this->isAllowAbilityjob($evaluation['ability_job_id']);
        $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$evaluation['ability_job_id']));
        $student=$this->student_model->get_row(array('id'=>$studentid));
        $where="company_code='".$this->_logininfo['company_code']."' and ability_job_evaluation_id=$evaluationid and student_id = $studentid ";
        $query = $this->db->get_where ( 'company_ability_job_evaluation_student', $where );
        $evaluation_student=$query->row_array();
        if($evaluation_student['status']!=2){
            redirect($returnevaluationlisturl);
            return;
        }
        $types=array('1'=>'专业能力','2'=>'通用能力','3'=>'领导力','4'=>'个性','5'=>'经验');
        $abilities=array('1'=>array('type'=>'专业能力'),'2'=>array('type'=>'通用能力'),'3'=>array('type'=>'领导力'),'4'=>array('type'=>'个性'),'5'=>array('type'=>'经验'));
        $levelradar=array('1'=>array('level_standard'=>0,'point'=>0,'level_total'=>0),'2'=>array('level_standard'=>0,'point'=>0,'level_total'=>0),'3'=>array('level_standard'=>0,'point'=>0,'level_total'=>0),'4'=>array('level_standard'=>0,'point'=>0,'level_total'=>0),'5'=>array('level_standard'=>0,'point'=>0,'level_total'=>0));

        $sql = "select assess.*,cajm.level_standard from " . $this->db->dbprefix('company_ability_job_student_assess') . " assess "
            . " left join " . $this->db->dbprefix('company_ability_job_model') . " cajm on cajm.ability_model_id = assess.ability_model_id and cajm.ability_job_id=".$abilityjob['id']
            . " where assess.company_code = '".$this->_logininfo['company_code']."' and assess.ability_job_evaluation_id = $evaluationid and assess.student_id=$studentid and isothersevaluation=2 ";
        if($abilityjob['hasleadership']=='2'){
            $sql.=" and cajm.type <> '3' ";
            unset($abilities['3']);
            unset($levelradar['3']);
            unset($types['3']);
        }
        $query = $this->db->query($sql . " order by assess.type asc,cajm.id asc ");
        $res = $query->result_array();
        foreach ($res as $a){
            $abilities[$a['type']]['abilities'][]=$a;
            $levelradar[$a['type']]['point']+=$a['point']*1;
            $levelradar[$a['type']]['level_standard']+=$a['level_standard']*1;
            $levelradar[$a['type']]['level_total']+=$a['level']*1;
        }
        $this->load->view ( 'header' );
        $this->load->view ( 'ability_manage/self_evaluation',compact('evaluation','abilityjob','student','evaluation_student','types','abilities','levelradar','returnevaluationlisturl'));
        $this->load->view ( 'footer' );
    }

    //他评结果
    public function othersevaluation($evaluationid,$studentid){
        $returnevaluationlisturl=$this->session->userdata('returnevaluationlisturl');
        $evaluation=$this->companyabilityjobevaluation_model->get_row(array('id'=>$evaluationid));
        $this->isAllowAbilityjob($evaluation['ability_job_id']);
        $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$evaluation['ability_job_id']));
        $student=$this->student_model->get_row(array('id'=>$studentid));
        $where="company_code='".$this->_logininfo['company_code']."' and ability_job_evaluation_id=$evaluationid and student_id = $studentid ";
        $query = $this->db->get_where ( 'company_ability_job_evaluation_student', $where );
        $evaluation_student=$query->row_array();
        if(empty($evaluation_student['others_id'])){
            redirect($returnevaluationlisturl);
            return;
        }else{
            $other=$this->student_model->get_row(array('id'=>$evaluation_student['others_id']));
        }

        $types=array('1'=>'专业能力','2'=>'通用能力','3'=>'领导力','4'=>'个性','5'=>'经验');
        $abilities=array('1'=>array('type'=>'专业能力'),'2'=>array('type'=>'通用能力'),'3'=>array('type'=>'领导力'),'4'=>array('type'=>'个性'),'5'=>array('type'=>'经验'));
        $levelradar=array('1'=>array('level_standard'=>0,'point'=>0,'level_total'=>0),'2'=>array('level_standard'=>0,'point'=>0,'level_total'=>0),'3'=>array('level_standard'=>0,'point'=>0,'level_total'=>0),'4'=>array('level_standard'=>0,'point'=>0,'level_total'=>0),'5'=>array('level_standard'=>0,'point'=>0,'level_total'=>0));

        $sql = "select assess.*,cajm.level_standard from " . $this->db->dbprefix('company_ability_job_student_assess') . " assess "
            . " left join " . $this->db->dbprefix('company_ability_job_model') . " cajm on cajm.ability_model_id = assess.ability_model_id and cajm.ability_job_id=".$abilityjob['id']
            . " where assess.company_code = '".$this->_logininfo['company_code']."' and assess.ability_job_evaluation_id = $evaluationid and assess.student_id=$studentid and isothersevaluation=1 ";
        if($abilityjob['hasleadership']=='2'){
            $sql.=" and cajm.type <> '3' ";
            unset($abilities['3']);
            unset($levelradar['3']);
            unset($types['3']);
        }
        $query = $this->db->query($sql . " order by assess.type asc,cajm.id asc ");
        $res = $query->result_array();
        foreach ($res as $a){
            $abilities[$a['type']]['abilities'][]=$a;
            $levelradar[$a['type']]['point']+=$a['point']*1;
            $levelradar[$a['type']]['level_standard']+=$a['level_standard']*1;
            $levelradar[$a['type']]['level_total']+=$a['level']*1;
        }
        $this->load->view ( 'header' );
        $this->load->view ( 'ability_manage/others_evaluation',compact('evaluation','abilityjob','student','evaluation_student','other','types','abilities','levelradar','returnevaluationlisturl'));
        $this->load->view ( 'footer' );
    }

    //是否是自己公司下的岗位系列
    private function isAllowSeriesid($seriesid,$redirect=true){
        if(empty($seriesid)||$this->companyabilityjobseries_model->get_count(array('id' => $seriesid,'company_code'=>$this->_logininfo['company_code'],'isdel'=>2))<=0){
            if($redirect){redirect(site_url('abilitymanage/index'));}
            return false;
        }else{
            return true;
        }
    }

    //是否是自己公司下的能力模型
    private function isAllowAbilityjob($abilityjobid,$redirect=true){
        if($this->companyabilityjob_model->get_count(array('id' => $abilityjobid,'company_code'=>$this->_logininfo['company_code'],'isdel'=>2))<=0){
            if($redirect){redirect(site_url('abilitymanage/index'));}
            return false;
        }else{
            return true;
        }
    }


}
