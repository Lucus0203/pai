<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Abilityentry extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library(array('session', 'pagination'));
        $this->load->helper(array('form','url'));
        $this->load->model(array('user_model','useractionlog_model','company_model','companyabilityjob_model','companyabilityjoblevel_model','companyabilityjobseries_model','companyabilitymodel_model','companyabilitysubcategory_model'));

        $this->_logininfo=$this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            redirect('login', 'index');
        } else {
            $roleInfo = $this->session->userdata('roleInfo');
            $this->useractionlog_model->create(array('user_id' => $this->_logininfo['id'], 'url' => uri_string()));
            $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));
        }

    }


    public function index($model_type=1,$subcategoryid) {
        if($subcategoryid!='model_type'&&!empty($subcategoryid)){
            $this->isAllowSubcategoryid($subcategoryid);
        }
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $sql = "select ability.*,category.name as category from " . $this->db->dbprefix('company_ability_model') . " ability "
            . " left join " . $this->db->dbprefix('company_ability_subcategory') . " category on ability.ability_subcategory_id = category.id "
            . " where ability.isdel =2 and ability.company_code = " . $this->_logininfo['company_code'] . " and ability.type=$model_type ";
        if($subcategoryid=='model_type'){//未分配条件
            $sql.=" and ability.ability_subcategory_id is null ";
        }elseif(!empty($subcategoryid)){
            $sql.=" and ability.ability_subcategory_id = $subcategoryid ";
        }
        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = site_url('abilityentry/index/'.$model_type.'/'.$subcategoryid);
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);
        $links=$this->pagination->create_links();

        $query = $this->db->query($sql . " order by ability.id desc limit " . ($page - 1) * $page_size . "," . $page_size);
        $entries = $query->result_array();

        $subcategory=$this->companyabilitysubcategory_model->get_row(array('id'=>$subcategoryid));
        $subcategories[$model_type]['categories']=$this->companyabilitysubcategory_model->get_all(array('company_code'=>$this->_logininfo['company_code'],'type'=>$model_type));
        $subcategories[$model_type]['isnoassigned']=$this->companyabilitymodel_model->get_count("company_code='".$this->_logininfo['company_code']."' and ability_subcategory_id is null and type ='$model_type' and isdel=2 ")>0;
        $types=array('1'=>'专业能力','2'=>'通用能力','3'=>'领导力','4'=>'个性','5'=>'经验');
        $this->load->view ( 'header' );
        $this->load->view ( 'ability_entry/index',compact('subcategoryid','subcategory','subcategories','model_type','types','isnoassigned','entries','links'));
        $this->load->view ( 'footer' );
    }

    //创建子分类
    public function addsubcategory($model_type){
        $subcategoryname=$this->input->post('subcategoryname');
        if (!empty($subcategoryname)) {
            $d = array('company_code' => $this->_logininfo['company_code'],'type'=>$model_type, 'name' => $subcategoryname);
            if($this->companyabilitysubcategory_model->get_count($d)>0){//已存在
                echo -1;
                return false;
            }
            $id = $this->companyabilitysubcategory_model->create($d);
            echo $id;
            return;
        }
        echo 0;
    }

    //编辑子分类
    public function savesubcategory($model_type,$subcategoryid){
        $subcategoryname = $this->input->post('subcategoryname');
        if (!empty($subcategoryname)) {
            if($this->companyabilitysubcategory_model->get_count("company_code='".$this->_logininfo['company_code']."' and type='$model_type' and name = '$subcategoryname' and id <> $subcategoryid ")>0){//已存在
                echo -1;
                return false;
            }
            $d = array('name' => $subcategoryname);
            $this->companyabilitysubcategory_model->update($d, $subcategoryid);
            echo $subcategoryid;
            return;
        }
        echo 0;
    }

    //删除子分类
    public function delsubcategory($subcategoryid){
        if (empty($subcategoryid)) {
            echo 1;//无参数
            return;
        } elseif ($this->companyabilitymodel_model->get_count(array('ability_subcategory_id' => $subcategoryid ,'isdel'=>2)) > 0) {
            echo 2;//子部门含有能力词条
            return;
        }
        $this->companyabilitysubcategory_model->del($subcategoryid);
        echo 0;
    }

    //创建能力词条
    public function createabilityentry($model_type=1,$subcategoryid=null){
        $act=$this->input->post('act');
        if(!empty($act)){
            $entry=array('company_code'=>$this->_logininfo['company_code'],
                'name'=>$this->input->post('name'),
                'type'=>$this->input->post('type'),
                'info'=>$this->input->post('info'),
                'level'=>$this->input->post('level'),
                'level_info1'=>$this->input->post('level_info1'),
                'level_info2'=>$this->input->post('level_info2'),
                'level_info3'=>$this->input->post('level_info3'));
            for ($i=3;$i<=$entry['level'];$i++){
                $entry['level_info'.$i]=$this->input->post('level_info'.$i);
            }
            $ability_subcategory_id=$this->input->post('ability_subcategory_id');
            if(!empty($ability_subcategory_id)){
                $entry['ability_subcategory_id']=$ability_subcategory_id;
            }
            $id=$this->companyabilitymodel_model->create($entry);
            redirect(site_url('abilityentry/index/'.$model_type.'/'.$subcategoryid));
        }
        $subcategories=$this->companyabilitysubcategory_model->get_all(array('company_code'=>$this->_logininfo['company_code'],'type'=>$model_type));
        $types=array('1'=>'专业能力','2'=>'通用能力','3'=>'领导力','4'=>'个性','5'=>'经验');
        $this->load->view ( 'header' );
        $this->load->view ( 'ability_entry/entry_edit',compact('model_type','types','subcategories','subcategoryid'));
        $this->load->view ( 'footer' );
    }

    //编辑词条
    public function editentry($model_type,$entryid){
        $act=$this->input->post('act');
        if(!empty($act)){
            $entry=array('company_code'=>$this->_logininfo['company_code'],
                'name'=>$this->input->post('name'),
                'type'=>$this->input->post('type'),
                'info'=>$this->input->post('info'),
                'level'=>$this->input->post('level'),
                'level_info1'=>$this->input->post('level_info1'),
                'level_info2'=>$this->input->post('level_info2'),
                'level_info3'=>$this->input->post('level_info3'));
            for ($i=3;$i<=$entry['level'];$i++){
                $entry['level_info'.$i]=$this->input->post('level_info'.$i);
            }
            $ability_subcategory_id=$this->input->post('ability_subcategory_id');
            if(!empty($ability_subcategory_id)){
                $entry['ability_subcategory_id']=$ability_subcategory_id;
            }else{
                $entry['ability_subcategory_id']=null;
            }
            $this->companyabilitymodel_model->update($entry,$entryid);
            echo '<script>history.go(-2);</script>';
        }
        $entry=$this->companyabilitymodel_model->get_row(array('id'=>$entryid,'company_code'=>$this->_logininfo['company_code']));
        $subcategories=$this->companyabilitysubcategory_model->get_all(array('company_code'=>$this->_logininfo['company_code'],'type'=>$model_type));
        $types=array('1'=>'专业能力','2'=>'通用能力','3'=>'领导力','4'=>'个性','5'=>'经验');
        $this->load->view ( 'header' );
        $this->load->view ( 'ability_entry/entry_edit',compact('model_type','types','subcategories','subcategoryid','entry'));
        $this->load->view ( 'footer' );
    }

    //获取子目录
    public function getcategories(){
        $type=$this->input->post('type');
        if(!empty($type)){
            $categories=$this->companyabilitysubcategory_model->get_all(array('company_code'=>$this->_logininfo['company_code'],'type'=>$type));
            echo json_encode($categories);
        }
    }

    //删除能力词条
    public function delentry($entryid){
        if (empty($entryid)) {
            echo 1;//无参数
            return;
        }
        $sql="select count(entry.id) as num from ".$this->db->dbprefix('company_ability_model') . " entry "
            . " left join " . $this->db->dbprefix('company_ability_job_model') . " ajm on ajm.ability_model_id = entry.id "
            . " left join " . $this->db->dbprefix('company_ability_job') . " aj on ajm.ability_job_id=aj.id "
            ." where entry.id = $entryid and aj.company_code=".$this->_logininfo['company_code']." and aj.isdel = 2 ";
        $query=$this->db->query($sql);
        $res=$query->row_array();
        if ($res['num'] > 0) {
            echo 2;//能力词条正在使用中
            return;
        }
        $this->companyabilitymodel_model->del($entryid);
        echo 0;
    }

    //是否是本公司的子类
    public function isAllowSubcategoryid($subcategoryid,$redirect=true){
        if($this->companyabilitysubcategory_model->get_count(array('id' => $subcategoryid,'company_code'=>$this->_logininfo['company_code']))<=0){
            if($redirect){redirect(site_url('abilityentry/index/1'));}
            return false;
        }else{
            return true;
        }

    }


}
