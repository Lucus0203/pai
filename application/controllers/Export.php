<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Export extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form', 'url','download'));
        $this->load->model(array('user_model', 'useractionlog_model', 'department_model','student_model','course_model'));

        $this->_logininfo = $this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            redirect('login', 'index');
        } else {
            $roleInfo = $this->session->userdata('roleInfo');
            $this->useractionlog_model->create(array('user_id' => $this->_logininfo['id'], 'url' => uri_string()));
            $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));
        }

    }

    //报名名单
    public function applylist($courseid)
    {
        if($this->isAllowCourseid($courseid)){
            $course=$this->course_model->get_row(array('id'=>$courseid));
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '姓名')
                ->setCellValue('B1', '工号')
                ->setCellValue('C1', '职务')
                ->setCellValue('D1', '部门')
                ->setCellValue('E1', '手机')
                ->setCellValue('F1', '申请原因')
                ->setCellValue('G1', '报名时间')
                ->setCellValue('H1', '状态');
            $sql = "select s.name,s.job_code,s.job_name,s.mobile,d.name as department,a.id as apply_id,a.status as apply_status,a.note,a.created "
                . "from " . $this->db->dbprefix('course_apply_list') . " a left join " . $this->db->dbprefix('student') . " s on a.student_id=s.id "
                . "left join " . $this->db->dbprefix('department') . " d on s.department_id = d.id "
                . "where a.course_id=$courseid ";
            $query = $this->db->query($sql . " order by a.id desc ");
            $applys = $query->result_array();
            foreach($applys as $k => $a){
                $num=$k+2;
                if($a['apply_status']==1){
                    $status='审核通过';
                }elseif($a['apply_status']==2){
                    $status='审核不通过';
                }else{
                    $status='待审核';
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$num, $a['name'])
                    ->setCellValue('B'.$num, $a['job_code'])
                    ->setCellValue('C'.$num, $a['job_name'])
                    ->setCellValue('D'.$num, $a['department'])
                    ->setCellValue('E'.$num, $a['mobile'])
                    ->setCellValue('F'.$num, $a['note'])
                    ->setCellValue('G'.$num, date("m-d H:i",strtotime($a['created'])))
                    ->setCellValue('H'.$num, $status);
            }

            $objPHPExcel->getActiveSheet()->setTitle('报名名单');
            $objPHPExcel->setActiveSheetIndex(0);
            $name='《'.$course['title'].'》报名名单';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }else{
            echo '数据错误,导出失败';
        }

    }

    //签到名单
    public function signinlist($courseid){
        if($this->isAllowCourseid($courseid)){
            $course=$this->course_model->get_row(array('id'=>$courseid));
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '姓名')
                ->setCellValue('B1', '工号')
                ->setCellValue('C1', '职务')
                ->setCellValue('D1', '部门')
                ->setCellValue('E1', '手机')
                ->setCellValue('F1', '签到时间')
                ->setCellValue('G1', '签退时间');
            $sql = "select s.*,d.name as department,siginlist.id as siginlist_id,siginlist.signin_time,siginlist.signout_time "
                . "from " . $this->db->dbprefix('course_signin_list') . " siginlist left join " . $this->db->dbprefix('student') . " s on siginlist.student_id=s.id "
                . "left join " . $this->db->dbprefix('department') . " d on s.department_id = d.id "
                . "where siginlist.course_id=$courseid ";
            $query = $this->db->query($sql . " order by siginlist.id desc ");
            $siginlist = $query->result_array();
            foreach($siginlist as $k => $s){
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$num, $s['name'])
                    ->setCellValue('B'.$num, $s['job_code'])
                    ->setCellValue('C'.$num, $s['job_name'])
                    ->setCellValue('D'.$num, $s['department'])
                    ->setCellValue('E'.$num, $s['mobile'])
                    ->setCellValue('F'.$num, date("m-d H:i",strtotime($s['signin_time'])))
                    ->setCellValue('G'.$num, !empty($h['signout_time'])?date("m-d H:i",strtotime($s['signout_time'])):'');
            }

            $objPHPExcel->getActiveSheet()->setTitle('签到名单');
            $objPHPExcel->setActiveSheetIndex(0);
            $name='《'.$course['title'].'》签到名单';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }else{
            echo '数据错误,导出失败';
        }
    }

    //课前调研名单
    public function surveylist($courseid){
        if($this->isAllowCourseid($courseid)){
            $course=$this->course_model->get_row(array('id'=>$courseid));
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '姓名')
                ->setCellValue('B1', '工号')
                ->setCellValue('C1', '职务')
                ->setCellValue('D1', '部门')
                ->setCellValue('E1', '手机')
                ->setCellValue('F1', '提交时间');
            $listsql = "select * from " . $this->db->dbprefix('course_survey_list') . " h where course_id=$courseid group by student_id order by created desc ";
            $sql = "select h.*,s.name,s.job_code,s.job_name,d.name as department,s.mobile from ($listsql) h left join " . $this->db->dbprefix('student') . " s on h.student_id = s.id "
                . "left join " . $this->db->dbprefix('department') . " d on s.department_id = d.id ";
            $query = $this->db->query($sql . " order by h.created desc ");
            $surveylist = $query->result_array();
            foreach($surveylist as $k => $s){
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$num, $s['name'])
                    ->setCellValue('B'.$num, $s['job_code'])
                    ->setCellValue('C'.$num, $s['job_name'])
                    ->setCellValue('D'.$num, $s['department'])
                    ->setCellValue('E'.$num, $s['mobile'])
                    ->setCellValue('F'.$num, date("m-d H:i",strtotime($s['created'])));
            }

            $objPHPExcel->getActiveSheet()->setTitle('调研名单');
            $objPHPExcel->setActiveSheetIndex(0);
            $name='《'.$course['title'].'》调研名单';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }else{
            echo '数据错误,导出失败';
        }
    }

    //课程反馈名单
    public function ratingslist($courseid){
        if($this->isAllowCourseid($courseid)){
            $course=$this->course_model->get_row(array('id'=>$courseid));
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 8, 'Some value');
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '姓名')
                ->setCellValue('B1', '工号')
                ->setCellValue('C1', '职务')
                ->setCellValue('D1', '部门')
                ->setCellValue('E1', '手机')
                ->setCellValue('F1', '提交时间');
            $listsql = "select h.* from " . $this->db->dbprefix('course_ratings_list') . " h "
                . "left join " . $this->db->dbprefix('course_ratings') . " rats on h.ratings_id=rats.id where h.course_id=$courseid and rats.num=1 ";
            $sql = "select h.*,s.name,s.job_code,s.job_name,d.name as department,s.mobile from ($listsql) h left join " . $this->db->dbprefix('student') . " s on h.student_id = s.id "
                . "left join " . $this->db->dbprefix('department') . " d on s.department_id = d.id ";
            $query = $this->db->query($sql . " order by h.created desc ");
            $ratingslist = $query->result_array();
            //反馈问题
            $ratsql="select * from ". $this->db->dbprefix('course_ratings')." rat where course_id=$courseid order by id asc ";
            $query = $this->db->query($ratsql);
            $ratques = $query->result_array();
            foreach ($ratques as $k=>$q){
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow(($k+6), 1, $q['title']);
            }
            foreach($ratingslist as $k => $r){
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$num, $r['name'])
                    ->setCellValue('B'.$num, $r['job_code'])
                    ->setCellValue('C'.$num, $r['job_name'])
                    ->setCellValue('D'.$num, $r['department'])
                    ->setCellValue('E'.$num, $r['mobile'])
                    ->setCellValue('F'.$num, date("m-d H:i",strtotime($r['created'])));

                    $ratanswersql="select ratans.*,ratque.type from ". $this->db->dbprefix('course_ratings_list')." ratans left join ". $this->db->dbprefix('course_ratings') ." ratque on ratans.ratings_id=ratque.id where course_id=$courseid and student_id='".$r['student_id']."' order by id asc ";
                    $query = $this->db->query($ratanswersql);
                    $ratanswer = $query->result_array();
                    foreach ($ratanswer as $k=>$ans){
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValueByColumnAndRow(($k+6), $num, ($ans['type']==2)?$ans['content']:$ans['star']);
                    }
            }

            $objPHPExcel->getActiveSheet()->setTitle('课程反馈名单');
            $objPHPExcel->setActiveSheetIndex(0);
            $name='《'.$course['title'].'》课程反馈名单';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }else{
            echo '数据错误,导出失败';
        }
    }

    //是否是自己公司下的课程
    private function isAllowCourseid($courseid){
        if(empty($courseid)||$this->course_model->get_count(array('id' => $courseid,'company_code'=>$this->_logininfo['company_code']))<=0){
            return false;
        }else{
            return true;
        }
    }


}
