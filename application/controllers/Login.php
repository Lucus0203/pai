<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Login extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form', 'url','captcha'));
        $this->load->model(array('user_model','student_model','department_model','userloginlog_model', 'company_model', 'purview_model', 'industries_model'));
    }


    public function index()
    {
        $act = $this->input->post('act');
        $error_msg = '';
        if ($act == 'act1') {
            $user = $this->input->post('user_name');
            $pass = $this->input->post('user_pass');
            $userinfo = $this->user_model->get_row(" role=1 and (email = ".$this->db->escape($user)." or mobile=".$this->db->escape($user)." or user_name=".$this->db->escape($user)." ) ");
            if (count($userinfo) > 0 && is_array($userinfo)) {
                $pwd = $userinfo ['user_pass'];
                if ($pwd == md5($pass)) {
                    $company = $this->company_model->get_row(array('code' => $userinfo['company_code']));
                    $userinfo['logo'] = $company['logo'];
                    $this->session->set_userdata('loginInfo', $userinfo);
                    //登录记录
                    $this->userloginlog_model->create(array('user_id' => $userinfo['id']));
                    redirect('index', 'index');
                } else {
                    $error_msg = "密码错误!";
                }
            } else {
                $error_msg = "账号或密码错误!";
            }
        } else if ($act == 'act2') {
            $user = $this->input->post('user_name');
            $pass = $this->input->post('user_pass');
            $company_code = $this->input->post('company_code');
            $userinfo = $this->user_model->get_row(" user_name = '$user' and company_code = '$company_code' and role <> 1 ");
            if (count($userinfo) > 0 && is_array($userinfo)) {
                $pwd = $userinfo ['user_pass'];
                if ($pwd == md5($pass)) {
                    $company = $this->company_model->get_row(array('code' => $userinfo['company_code']));
                    $userinfo['logo'] = $company['logo'];
                    $this->setpurview($userinfo['company_code'], $userinfo['role']);//权限全局化
                    $this->session->set_userdata('loginInfo', $userinfo);
                    //登录记录
                    $this->userloginlog_model->create(array('user_id' => $userinfo['id']));
                    redirect('index', 'index');
                } else {
                    $error_msg = "密码错误!";
                }
            } else {
                $error_msg = "账号或密码错误!";
            }
        }
        $this->load->view('login/login', array('error_msg' => $error_msg, 'act' => $act));
        $this->load->view('footer');

    }

    //注册
    public function register()
    {
        $res = array();
        $act = $this->input->post('act');
        if (!empty($act)) {
            $pass = $this->input->post('user_pass');
            $company_name = $this->input->post('company_name');
            $industry_parent_id = $this->input->post('industry_parent_id');
            $industry_id = $this->input->post('industry_id');
            $real_name = $this->input->post('real_name');
            $email = $this->input->post('email');
            $mobile = $this->input->post('mobile');
            $mobile_code = $this->input->post('mobile_code');
            $invitation_code = $this->input->post('invitation_code');
            $user = array('user_name' => $email,
                'user_pass' => $pass,
                'real_name' => $real_name,
                'email' => $email,
                'mobile' => $mobile,
                'mobile_code' => $mobile_code);
            $res['user'] = $user;
            $res['user_company_name']=$company_name;
            $res['user_industry_parent']=$this->industries_model->get_row(array('id'=>$industry_parent_id));
            $res['user_industrys']=$this->industries_model->get_all(array('parent_id'=>$industry_parent_id));
            $res['user_industry_id']=$industry_id;
            $repeatuser = $this->user_model->get_row("role=1 and user_name = ".$this->db->escape($email)." and mobile != ".$this->db->escape($mobile));
            if (!empty($repeatuser)) {
                $res['msg'] = '邮箱已被使用';
            }elseif ($invitation_code != 8367) {//邀请码
                $res['msg'] = '邀请码不正确';
            } else {
                $userinfo = $this->user_model->get_row(array('mobile' => $mobile,'role'=>1));
                if (!empty($userinfo) && $userinfo['mobile_code'] == $mobile_code) {
                    $code = rand(1000, 9999);//换个验证码
                    $this->load->database();
                    $this->db->trans_start();//事务开始
                    $last_company = $this->company_model->get_last_company();
                    $company_code = empty($last_company) ? '100001' : $last_company['code'] * 1 + 1;
                    $user['company_code'] = $company_code;
                    $user['user_pass'] = md5($pass);
                    $user['mobile_code'] = $code;
                    $user['status'] = 2;
                    $user['role'] = 1;
                    $user['register_flag'] = 2;
                    $this->user_model->update($user, $userinfo['id']);
                    $userinfo = $this->user_model->get_row(array('mobile' => $mobile,'role'=>1));
                    //创建人力资源部
                    $d = array('company_code' => $company_code, 'name' => '人力资源');
                    $departmentid = $this->department_model->create($d);
                    //管理员学员账号
                    $student = array('company_code' => $company_code,
                        'sex' => 1,
                        'name' => $real_name,
                        'mobile' => $mobile,
                        'email' => $email,
                        'user_name' => $mobile,
                        'user_pass' => md5(substr($mobile,-6)),
                        'department_parent_id'=>$departmentid,
                        'department_id'=>$departmentid,
                        'role' => 9);
                    $this->student_model->create($student);
                    //其他部门
                    $d = array('company_code' => $company_code, 'name' => '其他');
                    $this->department_model->create($d);
                    //公司信息
                    $this->company_model->create(array('code' => $company_code,
                        'name' => $company_name,
                        'contact' => $real_name,
                        'mobile' => $mobile,
                        'industry_parent_id' => $industry_parent_id,
                        'industry_id' => $industry_id,
                        'email' => $email));

                    $this->initpurview($company_code);//初始化权限
                    $this->db->trans_complete();//事务结束
                    $this->setpurview($company_code, $userinfo['role']);//权限全局化
                    $this->session->set_userdata('loginInfo', $userinfo);
                    $this->userloginlog_model->create(array('user_id' => $userinfo['id']));
                    redirect('index', 'index');
                    //redirect(base_url('login/register_success'));
                } else {
                    $res['msg'] = '验证码错误,请重新获取';
                }
            }
        }
        $res['cap']=$this->getCaptcha();
        $res['industry_parent'] = $this->industries_model->get_all("parent_id = '' or parent_id is null ");

        $this->load->view('login/register', $res);
        $this->load->view('footer');
    }

    public function forgot($sacount='')
    {
        $res = array();
        $act = $this->input->post('act');
        if (!empty($act)) {
            $post = array('mobile' => $this->input->post('mobile'),
                'user_pass' => $this->input->post('user_pass'),
                'company_code' => $this->input->post('company_code'),
                'mobile_code' => $this->input->post('mobile_code'));
            $res['user']=$post;
            $res['company'] = $company = $this->company_model->get_row(array('code' => $post['company_code']));
            if (!empty($sacount)&&empty($company)) {
                $msg = '公司编号错误';
            } else{
                $where=array('mobile' => $post['mobile'],'register_flag' => 2);
                if(!empty($sacount)){
                    $where['company_code']=$post['company_code'];
                }
                $userinfo = $this->user_model->get_row($where);
                if (!empty($userinfo) && $userinfo['mobile_code'] == $post['mobile_code']) {
                    $mobile_code = rand(1000, 9999);//换个验证码
                    $user_pass = md5($post['user_pass']);
                    $this->user_model->update(array('mobile_code'=>$mobile_code,'user_pass'=>$user_pass), $userinfo['id']);
                    $msg = '密码修改成功,请返回登录';
                    $res['success']='ok';
                    unset($res['user']);
                } else {
                    $msg = '短信验证码错误';
                }
            }
            $res['msg']=$msg;
        }
        $res['cap']=$this->getCaptcha();
        $res['sacount'] = $sacount;
        $this->load->view('login/forgot', $res);
        $this->load->view('footer');
    }

    public function fromauth($userid, $token)
    {//管理员登录用户数据
        $this->load->database();
        $query = $this->db->query('select login_token from ' . $this->db->dbprefix('admin'));
        $row = $query->first_row();
        if (!empty($token) && $row->login_token == $token) {
            $userinfo = $this->user_model->get_row(array('id' => $userid));
            $company = $this->company_model->get_row(array('code' => $userinfo['company_code']));
            $userinfo['logo'] = $company['logo'];
            if ($userinfo['role'] != 1) {
                $this->setpurview($userinfo['company_code'], $userinfo['role']);//权限全局化
            }
            $this->session->set_userdata('loginInfo', $userinfo);
            $this->db->query('update ' . $this->db->dbprefix('admin') . ' set login_token = NULL ');
            redirect('index', 'index');
        } else {
            echo '<script>alert("登录失败,请重新连接");window.close();</script>';
        }
    }

    //初始化权限
    /**
     * @param $company_code
     */
    private function initpurview($company_code)
    {
        $sql = "INSERT INTO `pai_purview` (`company_code`, `key`, `value`, `role`) VALUES
            ('$company_code', 'courselist', '1', '2'),
            ('$company_code', 'courseinfo', '1', '2'),
            ('$company_code', 'coursecreate', '1', '2'),
            ('$company_code', 'courseedit', '1', '2'),
            ('$company_code', 'coursedel', '1', '2'),
            ('$company_code', 'applyset', '1', '2'),
            ('$company_code', 'applylist', '1', '2'),
            ('$company_code', 'signinset', '1', '2'),
            ('$company_code', 'signinlist', '1', '2'),
            ('$company_code', 'surveyedit', '1', '2'),
            ('$company_code', 'surveylist', '1', '2'),
            ('$company_code', 'ratingsedit', '1', '2'),
            ('$company_code', 'ratingslist', '1', '2'),
            ('$company_code', 'notifyset', '1', '2'),
            ('$company_code', 'notifycustomize', '1', '2'),
            ('$company_code', 'teacherlist', '1', '2'),
            ('$company_code', 'teacherinfo', '1', '2'),
            ('$company_code', 'teachercreate', '1', '2'),
            ('$company_code', 'teacheredit', '1', '2'),
            ('$company_code', 'teacherdel', '1', '2'),
            ('$company_code', 'department', '1', '2'),
            ('$company_code', 'student', '1', '2'),
            ('$company_code', 'courselist', '1', '3'),
            ('$company_code', 'courseinfo', '1', '3'),
            ('$company_code', 'applylist', '1', '3'),
            ('$company_code', 'signinlist', '1', '3'),
            ('$company_code', 'surveylist', '1', '3'),
            ('$company_code', 'ratingslist', '1', '3'),
            ('$company_code', 'teacherlist', '1', '3'),
            ('$company_code', 'teacherinfo', '1', '3');
            ";
        $this->load->database();
        $this->db->query($sql);
    }

    //设置权限
    private function setpurview($company_code, $role)
    {
        if ($role != 1) {
            $purviews = $this->purview_model->get_all(array('company_code' => $company_code, 'role' => $role));
            $roledata = array();
            foreach ($purviews as $p) {
                $roledata[$p['key']] = $p['value'];
            }
            $this->session->set_userdata('roleInfo', $roledata);
        }
    }

    //注册成功
    public function register_success()
    {
        $this->load->view('login/register_success');
    }

    //获取验证码
    public function getcode($forgot='')
    {
        $s=$this->input->server(array('HTTP_REFERER'));
        if(empty($s['HTTP_REFERER'])){
            show_404();
            return false;
        }
        $company_code = $this->input->post('company_code');
        $email = $this->input->post('email');
        $mobile = $this->input->post('mobile');
        $captcha = $this->input->post('captcha');
        if(empty($mobile)){
            echo '请输入手机号';
            return false;
        }elseif($this->session->userdata('captcha')=='999999'){
            echo '验证码已过期';
            return false;
        }elseif(strtolower($captcha)!=$this->session->userdata('captcha')){
            echo '验证码错误';
            return false;
        }
        $code = rand(1000, 9999);
        if($forgot=='forgot'){
            if($company_code=='sacount'){
                $userinfo = $this->user_model->get_row(array('mobile' => $mobile,'register_flag'=>2,'role'=>1));
                if(empty($userinfo)){
                    echo '此手机号还未注册';
                    return false;
                }
            }else{
                $userinfo = $this->user_model->get_row(array('mobile' => $mobile,'company_code'=>$company_code,'register_flag'=>2,'role'=>2));
                if(empty($userinfo)){
                    echo '此手机号还未注册';
                    return false;
                }
            }
        }else {
            $userinfo = $this->user_model->get_row(array('mobile' => $mobile));
            if ($userinfo['register_flag'] == 2) {
                echo '此手机号已注册';
                return false;
            }
            $repeatname = $this->user_model->get_row(" user_name = " . $this->db->escape($email) . " and mobile != " . $this->db->escape($mobile));
            if (!empty($repeatname)) {
                echo '邮箱已被使用';
                return false;
            }
        }
        if (!empty($userinfo['id'])) {
            $this->user_model->update(array('mobile_code' => $code), $userinfo['id']);
        } else {
            $this->user_model->create(array('mobile' => $mobile, 'mobile_code' => $code, 'created' => date("Y-m-d H:i:s"), 'status' => 1,'ip_address'=>$this->input->ip_address()));
        }
        $this->load->library('zhidingsms');
        $this->zhidingsms->sendTPSMS($mobile,'@1@='.$code,'ZD30018-0001');
        $this->session->set_userdata('captcha', '999999');
        echo 1;
    }

    public function updateCaptcha(){
        $cap=$this->getCaptcha();
        echo $cap['image'];
    }

    private function getCaptcha(){
        $vals = array(
            'img_width' => '100',
            'img_height'    => 30,
            'word_length'   => 4,
            'font_size' => 16,
            'img_path'  => './uploads/captcha/',
            'img_url'   => base_url().'uploads/captcha/',
            'pool'      => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ'
        );
        $cap = create_captcha($vals);
        $this->session->set_userdata('captcha', strtolower($cap['word']));
        return $cap;
    }

    public function loginout()
    {
        $this->session->sess_destroy();
        redirect("login", "index");
    }

}
