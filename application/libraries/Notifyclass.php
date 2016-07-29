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
        $config['mailtype'] = 'text';
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
        //短信通知
        if($course['notice_type_msg']==1){
            $studentsarr = explode(',', $course['targetstudent']);
            $studentmobiles=$user['mobile'];
            $students=array();
            foreach ($studentsarr as $s) {
                $student = $this->CI->student_model->get_row(array('id' => $s));
                $studentmobiles.=!empty($student['mobile'])?','.$student['mobile']:'';
                if(!empty($student['email'])){
                    $students[]=$student;
                }
            }
            if(!empty($studentmobiles)){
                $this->CI->load->library('chuanlansms');
                $msg="依据公司培训计划安排，《{$course['title']}》将于" . date('Y年m月d日H时', strtotime($course['time_start'])) . "举行。现已启动报名工作，报名将在" . date('Y年m月d日', strtotime($course['apply_end'])) . "截止，点击下面的链接报名吧。
" . $this->CI->config->item('web_url') . 'course/info/' . $course['id'] . ".html
{$ischeckmsg}
为了大家的共同进步，请积极参与！

".$company['name'];
                if($company['code']=='100276'){
                    $msg.="
人力资源部";
                }
                    $msg.="
". date("Y年m月d日");
               $this->CI->chuanlansms->sendSMS($studentmobiles, $msg);
            }
        }

        //mail
        if($course['notice_type_email']==1){
            $tomail = $user['email'];
            $subject = "《{$course['title']}》开启报名";
            $studentname="亲爱的{$user['real_name']}：";
            $t1 = date('Y年m月d日H时', strtotime($course['time_start']));
            $t2 = date('Y年m月d日', strtotime($course['apply_end']));
            $link = '{unwrap}' . $this->CI->config->item('web_url') . 'course/info/'.$course['id'].'html{/unwrap}';
            $message = <<< EOF
            依据公司培训计划安排，《{$course['title']}》将于{$t1}举行。现已启动报名工作，报名将在{$t2}截止，点击下面的链接报名吧。
{$link}
{$ischeckmsg}
为了大家的共同进步，请积极参与！

{$company['name']}EOF;
            if($company['code']=='100276'){
                $message.='
人力资源部';
            }
            $message.='
'. date("Y年m月d日");
            $this->CI->email->from('service@trainingpie.com', '培训派');
            $this->CI->email->to($tomail);//
            $this->CI->email->subject($subject);
            $this->CI->email->message($studentname.$message);
            $this->CI->email->send();//发送给创建者
            $this->CI->email->clear();
            //发送给学员
            foreach ($students as $student) {
                if(!empty($student['email'])){
                    $studentname="亲爱的{$student['name']}同仁：";
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

        //短信通知
        if (!empty($student['mobile'])&&$course['notice_type_msg']==1) {
            $this->CI->load->library('chuanlansms');
            $msg = "亲爱的{$student['name']}：
你已成功报名参加《{$course['title']}》，该课程将于" . date('Y年m月d日H时', strtotime($course['time_start'])) . "在" . $course['address'] . "举行，请提前安排好工作或出差行程，准时参加培训。
上课前请先完成课前调研表（" . $this->CI->config->item('web_url') .'course/survey/' . $course['id'] . ".html）和课前作业并提交给我们。
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
            你已成功报名参加《{$course['title']}》，该课程将于" . date('Y年m月d日H时', strtotime($course['time_start'])) . "在" . $course['address'] . "举行，请提前安排好工作或出差行程，准时参加培训。
上课前请先完成课前调研表（" . $this->CI->config->item('web_url') .'course/survey/' . $course['id'] . ".html）和课前作业并提交给我们。
预祝学习愉快，收获满满！

".$company['name'];
            if($company['code']=='100276'){
                $message.="
人力资源部";
            }
            $message.="
". date("Y年m月d日");
            $this->CI->email->from('service@trainingpie.com', '培训派');
            $this->CI->email->to($tomail);//
            $this->CI->email->subject($subject);
            $this->CI->email->message($message);
            $this->CI->email->send();

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
