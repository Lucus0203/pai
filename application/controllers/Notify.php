<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Notify extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library(array('wechat'));
		$this->load->helper(array('form','url'));
		$this->load->model(array('user_model','company_model','course_model','teacher_model','homework_model','survey_model','ratings_model','student_model','department_model'));
                
                $config['protocol']     = 'smtp';  
                $config['smtp_host']    = '127.0.0.1';  
                $config['smtp_user']    = 'mailservice';
                $config['smtp_pass']    = 'service';  
                $config['smtp_port']    = '25';  
                $config['charset']      = 'utf-8';  
                $config['mailtype']     = 'text';  
                $config['smtp_timeout'] = '5';  
                $config['newline'] = "\r\n";
                $this->load->library ('email', $config);
		
	}
	
	
        //开课前一天通知  //时间触发
	public function coursestart() {
                $courses=$this->course_model->get_all(" notice_trigger_one=1 and time_start >= '".date('Y-m-d',strtotime('+1 day'))." 00:00:00' and time_start <= '".date('Y-m-d',strtotime('+1 day'))." 23:59:59' ");
                foreach ($courses as $c) {
                    $subject="《{$c['title']}》即将开课";
                    $teacher=$this->teacher_model->get_row(array('id'=>$c['teacher_id']));
                    $this->load->database ();
                    $sql="select s.* from ".$this->db->dbprefix('student')." s left join ".$this->db->dbprefix('course_apply_list')." a on s.id=a.student_id where a.course_id=".$c['id']." and a.status=1 ";
                    $query = $this->db->query ($sql);
                    $students=$query->result_array();
                    foreach ($students as $s) {
                        
                        //mail
                        $tomail=$s['email'];
                        $message="
    亲爱的{$s['name']}
    你好！
    《{$c['title']}》课程将于".date('m月d日H点',strtotime($c['time_start']))."在{$c['address']}举行，请安排好工作，或做好出差计划，准时参加课程。
    上课前，请做好课前作业，提交给我们。
    签到在开课前2小时生效，别忘了签到哦，谢谢！

    培训派
    ".date("m月d日");
                        $this->email->from('service@trainingpie.com', '培训派');
                        $this->email->to($tomail);//
                        $this->email->subject($subject);
                        $this->email->message($message);
                        $this->email->send();
                        $this->email->clear();
                        //微信通知
                        $wxdata=array(
                            'userName'=>array(
                                'value'=>$s['name'],
                                'color'=>"#173177"
                            ),
                            'courseName'=>array(
                                'value'=>$c['title'],
                                'color'=>"#173177"
                            ),
                            'date'=>array(
                                'value'=>date('m月d日H点',strtotime($c['time_start']))."在".$c['address'],
                                'color'=>"#173177"
                            ),
                            'remark'=>array(
                                'value'=>"请安排好工作，或做好出差计划，准时参加课程。
上课前，请做好课前作业，提交给我们。
签到在开课前2小时生效，别忘了签到哦，谢谢！",
                                'color'=>"#173177"
                            )
                        );
                        $res=$this->wechat->templateSend($s['openid'],'5yxj6pEwlEw9xB0fFy-xUp6ec0azoAvPYA-tE-uBwDU',$this->config->item('web_url').'course/courseinfo/'.$c['id'].'.html',$wxdata);
                    }
                }
	}
        
        //课程跟进 //时间触发
        public function coursefollow($courseid){
            
        }
	
}
