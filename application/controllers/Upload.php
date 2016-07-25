<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Upload extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form', 'url','download'));
        $this->load->model(array('user_model', 'useractionlog_model', 'department_model', 'student_model'));

        $this->_logininfo = $this->session->userdata('loginInfo');
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

    //上传学员
    public function uploadstudent()
    {
        $this->load->database ();
        $config['max_size'] = 80*1024;
        $config['upload_path'] = './uploads/studentdata/';
        $config['allowed_types'] = 'xls|xlsx';
        $config['file_name'] = $file_name = $this->_logininfo['id'] . date("YmdHis");
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('excelFile')) {
            $file = $this->upload->data();
            $excelfile = $file_name . $file['file_ext'];
            $this->load->library('PHPExcel');
            $objPHPExcel = PHPExcel_IOFactory::load($config['upload_path'] . $excelfile);
            $sheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $first_depart_array=$second_depart_array=array();//初始化部门临时数组
            for ($row = 2; $row <= $highestRow; $row++) {
                $name = $objPHPExcel->getActiveSheet()->getCell('A' . $row)->getValue();//姓名
                $mobile = $objPHPExcel->getActiveSheet()->getCell('B' . $row)->getValue();//手机
                $email = $objPHPExcel->getActiveSheet()->getCell('C' . $row)->getValue();//邮箱
                $pass = $objPHPExcel->getActiveSheet()->getCell('D' . $row)->getValue();//密码
                $sex = $objPHPExcel->getActiveSheet()->getCell('E' . $row)->getValue();//性别
                $jobcode = $objPHPExcel->getActiveSheet()->getCell('F' . $row)->getValue();//工号
                $jobname = $objPHPExcel->getActiveSheet()->getCell('G' . $row)->getValue();//职位
                $first_department = trim($objPHPExcel->getActiveSheet()->getCell('H' . $row)->getValue());//一级部门
                $second_department = trim($objPHPExcel->getActiveSheet()->getCell('I' . $row)->getValue());//二级部门


                $student = array('company_code' => $this->_logininfo['company_code'],
                    'sex' => trim($sex)=='男'?1:2,
                    'name' => trim($name),
                    'job_code' => trim($jobcode),
                    'job_name' => trim($jobname),
                    'mobile' => trim($mobile),
                    'email' => trim($email),
                    'user_name' => trim($mobile),
                    'user_pass' => !empty($pass)?md5($pass):'',
                    'role' => 1,
                    'isdel' => 2);
                //数据验证
                $flag=$this->validateStudent($student,$row,$first_department,$second_department);
                if(!$flag){//验证不通过则跳出程序
                    return false;
                }

                //获取部门id
                if(!empty($first_department)){
                    $student['department_parent_id'] = $this->searchDepartId($first_department,$first_depart_array);
                }
                if(!empty($second_department)) {
                    $student['department_id'] = $this->searchDepartId($second_department, $second_depart_array, $student['department_parent_id']);
                }else{
                    $student['department_id']=$student['department_parent_id'];
                }

                //判断学员是否存在
                $s=$this->student_model->get_row(array('company_code' => $this->_logininfo['company_code'], 'mobile' => $student['mobile'],'isdel'=>2));
                if (empty($s['id'])) {
                    $this->student_model->create($student);
                }else{
                    $this->student_model->update($student,$s['id']);
                }
            }
            unlink($config['upload_path'] . $excelfile);
        }

        redirect($_SERVER['HTTP_REFERER']);
    }
    //下载学员模板
    public function downloadstudentexample(){
        force_download('./uploads/studentdata/uploadExample.xlsx', NULL);
    }

    //学员数据验证
    private  function validateStudent($data,$row,$first_department,$second_department){
        $flag=true;
        $msg='';
        if(empty($data['name'])){
            $msg .= '上传失败!第'.$row.'行的姓名不能是空的<br/>';
            $flag=false;
        }
        if(empty($data['mobile'])){
            $msg .= '上传失败!第'.$row.'行的手机不能是空的<br/>';
            $flag=false;
        }
        if(empty($data['user_pass'])){
            $msg .= '上传失败!第'.$row.'行的密码不能是空的<br/>';
            $flag=false;
        }
        if(empty($first_department)&&!empty($second_department)){
            $msg .= '上传失败!第'.$row.'行的二级部门是['.$second_department.'], 一级部门的内容不能是空的<br/>';
            $flag=false;
        }
        echo $msg;
        return $flag;
    }
    //查找部门id,没有则创建
    private function searchDepartId($name,&$arr,$fdepart_id=null){
        $key=array_search($name,$arr);
        if(empty($key)){
            $depart=$this->department_model->get_row(array('company_code'=>$this->_logininfo['company_code'],'name'=>$name));
            if(empty($depart['id'])){
                $depart=array('company_code'=>$this->_logininfo['company_code'],'name'=>$name);
                if(!empty($fdepart_id)){
                    $p = $this->department_model->get_row(array('id' => $fdepart_id));
                    $depart['parent_id'] = $fdepart_id;
                    $depart['level'] = $p['level'] * 1 + 1;
                }
                $key=$this->department_model->create($depart);
            }else{
                $key=$depart['id'];
            }
            $arr[$key]=$depart['name'];
        }
        return $key;
    }


}
