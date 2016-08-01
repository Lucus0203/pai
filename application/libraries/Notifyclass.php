<?php

/**
 * 培训派通知类
 *
 * @version        1.0
 */
class Notifyclass
{
    protected $CI;

    function __construct($config = array())
    {

        $this->CI =& get_instance();
        $this->CI->load->library(array('wechat'));
        $this->CI->load->helper(array('form', 'url'));
        $this->CI->load->model(array('user_model', 'company_model', 'course_model', 'teacher_model', 'homework_model', 'survey_model', 'ratings_model', 'student_model', 'department_model'));

        $config['protocol'] = 'smtp';
        $config['smtp_host'] = '127.0.0.1';
        $config['smtp_user'] = 'mailservice';
        $config['smtp_pass'] = 'service';
        $config['smtp_port'] = '25';
        $config['charset'] = 'utf-8';
        $config['mailtype'] = 'html';
        $config['smtp_timeout'] = '5';
        $config['newline'] = "\r\n";
        $this->CI->load->library('email', $config);
    }


    //开启报名 //人工触发
    public function applyopen($courseid)
    {
        $course = $this->CI->course_model->get_row(array('id' => $courseid));
        if($course['isnotice_open']!=1){//自动通知关闭
            return false;
        }
        $user = $this->CI->user_model->get_row(array('id' => $course['user_id']));
        $company = $this->CI->company_model->get_row(array('code'=>$user['company_code']));
        $ischeckmsg=($course['apply_check']==1)?'报名经你的上级领导审核通过后，我们将另行发送报名成功通知。':'';
        $t1 = date('Y年m月d日H时', strtotime($course['time_start']));//举行时间
        $t2 = date('Y年m月d日H时', strtotime($course['apply_end']));//截止时间
        $link = $this->CI->config->item('web_url') . 'course/info/'.$course['id'].'html';//链接
        //短信通知
        $this->CI->load->library('chuanlansms');
        if($course['notice_type_msg']==1){
            $studentsarr = explode(',', $course['targetstudent']);
            $studentmobiles=$user['mobile'];
            $students=array();
            foreach ($studentsarr as $s) {
                $student = $this->CI->student_model->get_row(array('id' => $s));

                //$studentmobiles.=!empty($student['mobile'])?','.$student['mobile']:'';
                if(!empty($student['email'])){
                    $students[]=$student;
                }
                if($student['status']==1){
                    $pass=rand(100000,999999);
                    $accountmsg='账号：'.$student['mobile'].'
初始密码：'.$pass.'
首次登录后记得修改你的初始密码。';
                    $this->CI->student_model->update(array('user_pass'=>md5($pass)),$student['id']);
                }else{
                    $accountmsg='';
                }
                $msg="亲爱的{$student['name']}：
依据公司培训计划安排，《{$course['title']}》将于{$t1}举行。现已启动报名工作，报名将在{$t2}截止，点击下面的链接报名吧。
{$link}
{$accountmsg}
{$ischeckmsg}
为了大家的共同进步，请积极参与！

".$company['name'];
                if($company['code']=='100276'){
                    $msg.="
人力资源部";
                }
                $msg.="
". date("Y年m月d日");
                $this->CI->chuanlansms->sendSMS($student['mobile'], $msg);
            }
        }

        //mail
        if($course['notice_type_email']==1){
            $tomail = $user['email'];
            $subject = "《{$course['title']}》开启报名";
            $studentname="亲爱的{$user['real_name']}：";
            $message = <<< EOF
<p style="text-indent:40px">依据公司培训计划安排，《{$course['title']}》将于{$t1}举行。现已启动报名工作，报名将在{$t2}截止，点击下面的链接报名吧。
<br><a href='{$link}' target='_blank'>{$link}</a></p>
<p style="text-indent:40px">{$ischeckmsg}</p>
<p style="text-indent:40px">为了大家的共同进步，请积极参与！</p>

<br><p style="text-align: right;margin-right: 40px;">{$company['name']}</p>
EOF;
            if($company['code']=='100276'){
                $message.='<p style="text-align: right;margin-right: 40px;">人力资源部</p>';
            }
            $message.='<p style="text-align: right;margin-right: 40px;">'. date("Y年m月d日").'</p>';
            $this->CI->email->from('service@trainingpie.com', '培训派');
            $this->CI->email->to($tomail);//
            $this->CI->email->subject($subject);
            $this->CI->email->message($studentname.$message);
            $this->CI->email->send();//发送给创建者
            $this->CI->email->clear();
            //发送给学员
            foreach ($students as $student) {
                if(!empty($student['email'])){
                    $studentname="亲爱的{$student['name']}：";
                    $this->CI->email->from('service@trainingpie.com', '培训派');
                    $this->CI->email->to($student['email']);
                    $this->CI->email->subject($subject);
                    $this->CI->email->message($studentname.$message);
                    $this->CI->email->send();
                    $this->CI->email->clear();
                }
            }
        }

    }


    //报名成功 人员触发 人工审核
    public function applysuccess($courseid, $studentid)
    {
        $course = $this->CI->course_model->get_row(array('id' => $courseid));
        if($course['isnotice_open']!=1){//自动通知关闭
            return false;
        }
        $student = $this->CI->student_model->get_row(array('id' => $studentid));
        $company = $this->CI->company_model->get_row(array('code' => $student['company_code']));

        $t = date('Y年m月d日H时', strtotime($course['time_start']));//举行时间
        $link = $this->CI->config->item('web_url') .'course/survey/' . $course['id'] . '.html';//链接
        //短信通知
        if (!empty($student['mobile'])&&$course['notice_type_msg']==1) {
            $this->CI->load->library('chuanlansms');
            $msg = "亲爱的{$student['name']}：
你已成功报名参加《{$course['title']}》，该课程将于{$t}在{$course['address']}举行，请提前安排好工作或出差行程，准时参加培训。
上课前请先完成课前调研表（{$link}）和课前作业并提交给我们。
预祝学习愉快，收获满满！

" . $company['name'];
            if($company['code']=='100276'){
                $msg.="
人力资源部";
            }
            $msg.="
". date("Y年m月d日");
            $this->CI->chuanlansms->sendSMS($student['mobile'], $msg);
        }

        //mail
        if (!empty($student['email'])&&$course['notice_type_email']==1) {

            $tomail = $student['email'];
            $subject = "《{$course['title']}》报名成功";
            $message = "亲爱的{$student['name']}：
<p style='text-indent:40px'>你已成功报名参加《{$course['title']}》，该课程将于{$t}在{$course['address']}举行，请提前安排好工作或出差行程，准时参加培训。</p>
<p style='text-indent:40px'>上课前请先完成课前调研表（<a href='{$link}' target='_blank'>{$link}</a>）和课前作业并提交给我们。</p>
<p style='text-indent:40px'>预祝学习愉快，收获满满！</p>

<p style=\"text-align: right;margin-right: 40px;\">".$company['name'].'</p>';
            if($company['code']=='100276'){
                $message.='<p style="text-align: right;margin-right: 40px;">人力资源部</p>';
            }
            $message.='<p style="text-align: right;margin-right: 40px;">'. date("Y年m月d日").'</p>';
            $this->CI->email->from('service@trainingpie.com', '培训派');
            $this->CI->email->to($tomail);//
            $this->CI->email->subject($subject);
            $this->CI->email->message($message);
            $this->CI->email->send();
            $this->CI->email->clear();

        }
        //微信通知
        if (!empty($student['openid'])&&$course['notice_type_wx']==1) {
            $wxdata = array(
                'first' => array(
                    'value' => '您好,' . $student['name'] . '
您已成功报名参加' . $course['title'],
                    'color' => "#173177"
                ),
                'class' => array(
                    'value' => $course['title'],
                    'color' => "#173177"
                ),
                'time' => array(
                    'value' => date('m月d日', strtotime($course['time_start'])),
                    'color' => "#173177"
                ),
                'add' => array(
                    'value' => $course['address'],
                    'color' => "#173177"
                ),
                'remark' => array(
                    'value' => "请提前安排好工作或出差行程，准时参加培训。上课前请先完成课前调研表和课前作业并提交给我们。
预祝学习愉快，收获满满！",
                    'color' => "#173177"
                )
            );
            $res = $this->CI->wechat->templateSend($student['openid'], 'yFfIfh1EPvvpyeNplv5n6xBEyn5Em4r5ZYAHoLFnM9E', $this->CI->config->item('web_url') . 'course/survey/' . $course['id'] . '.html', $wxdata);
        }
    }


}

/* end of file */
