<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Export extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form', 'url','download'));
        $this->load->model(array('user_model', 'useractionlog_model', 'department_model','student_model','course_model','annualplan_model','annualcoursetype_model'));

        $this->_logininfo = $this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            redirect('login', 'index');
        } else {
            $roleInfo = $this->session->userdata('roleInfo');
            $this->useractionlog_model->create(array('user_id' => $this->_logininfo['id'], 'url' => uri_string()));
            $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));
        }

    }

    //导出全部学员
    public function studentdata(){
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '姓名')
            ->setCellValue('B1', '手机号')
            ->setCellValue('C1', '邮箱')
            ->setCellValue('D1', '密码(保密安全需要,不被导出)')
            ->setCellValue('E1', '性别')
            ->setCellValue('F1', '工号')
            ->setCellValue('G1', '职位')
            ->setCellValue('H1', '一级部门')
            ->setCellValue('I1', '二级部门');
        $sql = "select student.*,department_parent.name as department_parent,department.name as department from " . $this->db->dbprefix('student') . " student left join " . $this->db->dbprefix('department') . " department_parent on student.department_parent_id=department_parent.id left join " . $this->db->dbprefix('department') . " department on student.department_id=department.id where student.user_name <> '' and student.isdel = 2 and student.company_code='{$this->_logininfo['company_code']}' ";
        $query = $this->db->query($sql . " order by student.department_parent_id,student.department_id,student.id ");
        $students = $query->result_array();
        foreach($students as $k => $s){
            $num=$k+2;
            $sex=!empty($s['sex'])?$s['sex']==1?'男':'女':'';
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$num, $s['name'])
                ->setCellValue('B'.$num, $s['mobile'])
                ->setCellValue('C'.$num, $s['email'])
                ->setCellValue('D'.$num, '')
                ->setCellValue('E'.$num, $sex)
                ->setCellValue('F'.$num, $s['job_code'])
                ->setCellValue('G'.$num, $s['job_name'])
                ->setCellValue('H'.$num, $s['department'])
                ->setCellValue('I'.$num, $s['department']);
        }

        $objPHPExcel->getActiveSheet()->setTitle('学员名单');
        $objPHPExcel->setActiveSheetIndex(0);
        $name='所有学员名单';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
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
            $ratsql="select * from ". $this->db->dbprefix('course_ratings')." rat where course_id=$courseid order by num asc ";
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

                    $ratanswersql="select ratans.*,ratque.type from ". $this->db->dbprefix('course_ratings_list')." ratans left join ". $this->db->dbprefix('course_ratings') ." ratque on ratans.ratings_id=ratque.id where ratans.course_id=$courseid and ratans.student_id='".$r['student_id']."' order by num asc ";
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

    //导出年度计划excel
    public function exportplan($planid){
        if($this->isAllowPlanid($planid)){
            $plan=$this->annualplan_model->get_row(array('id'=>$planid));
            //部门总览
            $countcourselist="select count(apcl.id) num,apc.annual_course_id,apc.title,apc.price,apc.people,parent_department.name as department,s.department_parent_id from ".$this->db->dbprefix('annual_plan_course_list')." apcl left join ".$this->db->dbprefix('student')." s on s.id = apcl.student_id ".
                " left join ".$this->db->dbprefix('department')." parent_department on parent_department.id = s.department_parent_id ".
                " left join ".$this->db->dbprefix('department')." department on department.id = s.department_id ".
                " left join ".$this->db->dbprefix('annual_plan_course')." apc on apcl.annual_course_id=apc.annual_course_id and apcl.annual_plan_id=apc.annual_plan_id where apc.annual_plan_id=".$planid." and apc.openstatus=1 and apcl.status=1 group by apc.annual_course_id,s.department_parent_id ";
            $ccsql="select cc.department_parent_id,cc.department,count(cc.annual_course_id) course_num,sum(cc.num) people_num,sum(cc.num * cc.price/cc.people) price_total from ($countcourselist) cc GROUP BY cc.department_parent_id order by cc.department_parent_id ";
            $query = $this->db->query($ccsql);
            $departmentcourse = $query->result_array();

            //课程总览及课程内容
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
            //讲师

            $teachersql="select teacher.* from ".$this->db->dbprefix('annual_plan_course')." plan_course left join ".$this->db->dbprefix('teacher')." teacher on plan_course.teacher_id=teacher.id where plan_course.annual_plan_id=$planid and plan_course.company_code='".$this->_logininfo['company_code']."' and plan_course.openstatus=1 and plan_course.teacher_id is not null group by teacher.id ";
            $query = $this->db->query($teachersql . " order by plan_course.id asc ");
            $teachers = $query->result_array();

            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objActSheet = $objPHPExcel->getActiveSheet()->setTitle('年度计划课程');
            //excel style
            $styleTitle = array(
                'font' => array('color' => array('argb' => 'FFffffff')),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('argb' => 'FF00bbd3')
                ),
            );
            $styleTh = array(
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER),
                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('argb' => 'FFf5f5f5')
                ),
            );
            $styleTd = array(
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER),
            );
            $styleTdLeft = array(
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER),
            );

            //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 8, 'Some value');
            $i=1;
            //部门总览
            if(count($departmentcourse)>0){
                $objActSheet->mergeCells('A'.$i.':I'.$i)->setCellValue('A'.$i, '部门总览')
                    ->mergeCells('A'.($i+1).':C'.($i+1))->setCellValue('A'.($i+1), '部门名称')
                    ->mergeCells('D'.($i+1).':E'.($i+1))->setCellValue('D'.($i+1), '开课数量')
                    ->mergeCells('F'.($i+1).':G'.($i+1))->setCellValue('F'.($i+1), '培训人次')
                    ->mergeCells('H'.($i+1).':I'.($i+1))->setCellValue('H'.($i+1), '培训预算');
                $objActSheet->getStyle('A'.$i)->applyFromArray($styleTitle);
                $objActSheet->getStyle('A'.($i+1))->applyFromArray($styleTh);
                $objActSheet->getStyle('D'.($i+1))->applyFromArray($styleTh);
                $objActSheet->getStyle('F'.($i+1))->applyFromArray($styleTh);
                $objActSheet->getStyle('H'.($i+1))->applyFromArray($styleTh);
                $count_total=0;$people_total=0;$price_total=0;
                $i+=2;$fi=$i;
                foreach ($departmentcourse as $dc){
                    $count_total+=$dc['course_num'];
                    $people_total+=$dc['people_num'];
                    $price_total+=$dc['price_total'];
                    $objActSheet->mergeCells('A'.$i.':C'.$i)->setCellValue('A'.$i,$dc['department']);
                    $objActSheet->mergeCells('D'.$i.':E'.$i)->setCellValue('D'.$i,round($dc['course_num']));
                    $objActSheet->mergeCells('F'.$i.':G'.$i)->setCellValue('F'.$i,round($dc['people_num']));
                    $objActSheet->mergeCells('H'.$i.':I'.$i)->setCellValue('H'.$i,round($dc['price_total']));
                    $objActSheet->getStyle('A'.$i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('D'.$i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('F'.$i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('H'.$i)->applyFromArray($styleTd);
                    ++$i;
                }
                if(count($res)>1) {
                    $objActSheet->mergeCells('A' . $i . ':C' . $i)->setCellValue('A' . $i, '全部');
                    $objActSheet->mergeCells('D' . $i . ':E' . $i)->setCellValue('D' . $i, "=SUM(D" . $fi . ":D" . ($i - 1) . ")");
                    $objActSheet->mergeCells('F' . $i . ':G' . $i)->setCellValue('F' . $i, "=SUM(F" . $fi . ":F" . ($i - 1) . ")");
                    $objActSheet->mergeCells('H' . $i . ':I' . $i)->setCellValue('H' . $i, "=SUM(H" . $fi . ":H" . ($i - 1) . ")");
                    $objActSheet->getStyle('A' . $i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('D' . $i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('F' . $i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('H' . $i)->applyFromArray($styleTd);
                    ++$i;
                }
            }
            //课程总览
            $objActSheet->mergeCells('A' . $i . ':I' . $i)->setCellValue('A' . $i, '');
            ++$i;
            $objActSheet->mergeCells('A'.$i.':I'.$i)->setCellValue('A'.$i, '课程总览')
                ->mergeCells('A'.($i+1).':C'.($i+1))->setCellValue('A'.($i+1), '课程类型')
                ->mergeCells('D'.($i+1).':E'.($i+1))->setCellValue('D'.($i+1), '开课数量')
                ->mergeCells('F'.($i+1).':G'.($i+1))->setCellValue('F'.($i+1), '培训人次')
                ->mergeCells('H'.($i+1).':I'.($i+1))->setCellValue('H'.($i+1), '培训预算');
            $objActSheet->getStyle('A'.$i)->applyFromArray($styleTitle);
            $objActSheet->getStyle('A'.($i+1))->applyFromArray($styleTh);
            $objActSheet->getStyle('D'.($i+1))->applyFromArray($styleTh);
            $objActSheet->getStyle('F'.($i+1))->applyFromArray($styleTh);
            $objActSheet->getStyle('H'.($i+1))->applyFromArray($styleTh);
            $count_total=0;$people_total=0;$price_total=0;
            $i+=2;$fi=$i;
            foreach ($res as $r){
                $count_total+=$r['total']['count_num'];
                $people_total+=$r['total']['people_num'];
                $price_total+=$r['total']['price_num'];
                $objActSheet->mergeCells('A'.$i.':C'.$i)->setCellValue('A'.$i,$r['name']);
                $objActSheet->mergeCells('D'.$i.':E'.$i)->setCellValue('D'.$i,round($r['total']['count_num']));
                $objActSheet->mergeCells('F'.$i.':G'.$i)->setCellValue('F'.$i,round($r['total']['people_num']));
                $objActSheet->mergeCells('H'.$i.':I'.$i)->setCellValue('H'.$i,round($r['total']['price_num']));
                $objActSheet->getStyle('A'.$i)->applyFromArray($styleTd);
                $objActSheet->getStyle('D'.$i)->applyFromArray($styleTd);
                $objActSheet->getStyle('F'.$i)->applyFromArray($styleTd);
                $objActSheet->getStyle('H'.$i)->applyFromArray($styleTd);
                ++$i;
            }
            if(count($res)>1){
                $objActSheet->mergeCells('A'.$i.':C'.$i)->setCellValue('A'.$i,'全部');
                $objActSheet->mergeCells('D'.$i.':E'.$i)->setCellValue('D'.$i,"=SUM(D".$fi.":D".($i-1).")");
                $objActSheet->mergeCells('F'.$i.':G'.$i)->setCellValue('F'.$i,"=SUM(F".$fi.":F".($i-1).")");
                $objActSheet->mergeCells('H'.$i.':I'.$i)->setCellValue('H'.$i,"=SUM(H".$fi.":H".($i-1).")");
                $objActSheet->getStyle('A'.$i)->applyFromArray($styleTd);
                $objActSheet->getStyle('D'.$i)->applyFromArray($styleTd);
                $objActSheet->getStyle('F'.$i)->applyFromArray($styleTd);
                $objActSheet->getStyle('H'.$i)->applyFromArray($styleTd);
                ++$i;
            }

            foreach ($res as $r){
                $objActSheet->mergeCells('A' . $i . ':I' . $i)->setCellValue('A' . $i, '');
                if($r['total']['count_num']>0){
                    ++$i;
                    $objActSheet->mergeCells('A'.$i.':I'.$i)->setCellValue('A'.$i, $r['name']);
                    $objActSheet->getStyle('A'.$i)->applyFromArray($styleTitle);
                    ++$i;
                    $objActSheet->setCellValue('A'.$i,'课程名称');
                    $objActSheet->setCellValue('B'.$i,'课程介绍');
                    $objActSheet->setCellValue('C'.$i,'内训/外训');
                    $objActSheet->setCellValue('D'.$i,'供应商');
                    $objActSheet->setCellValue('E'.$i,'讲师');
                    $objActSheet->setCellValue('F'.$i,'天数');
                    $objActSheet->setCellValue('G'.$i,'人次');
                    $objActSheet->setCellValue('H'.$i,'预算');
                    $objActSheet->setCellValue('I'.$i,'时间');
                    $objActSheet->getStyle('A'.$i)->applyFromArray($styleTh);
                    $objActSheet->getStyle('B'.$i)->applyFromArray($styleTh);
                    $objActSheet->getStyle('C'.$i)->applyFromArray($styleTh);
                    $objActSheet->getStyle('D'.$i)->applyFromArray($styleTh);
                    $objActSheet->getStyle('E'.$i)->applyFromArray($styleTh);
                    $objActSheet->getStyle('F'.$i)->applyFromArray($styleTh);
                    $objActSheet->getStyle('G'.$i)->applyFromArray($styleTh);
                    $objActSheet->getStyle('H'.$i)->applyFromArray($styleTh);
                    $objActSheet->getStyle('I'.$i)->applyFromArray($styleTh);
                    foreach ($r['courses'] as $c){
                        ++$i;
                        $objActSheet->setCellValue('A'.$i,$c['title']);
                        $objActSheet->setCellValue('B'.$i,$c['info']);
                        $objActSheet->setCellValue('C'.$i,$c['external']==1?'外训':'内训');
                        $objActSheet->setCellValue('D'.$i,$c['supplier']);
                        $objActSheet->setCellValue('E'.$i,$c['teacher']);
                        $objActSheet->setCellValue('F'.$i,$c['day']);
                        $objActSheet->setCellValue('G'.$i,$c['people']);
                        $objActSheet->setCellValue('H'.$i,$c['price']);
                        $objActSheet->setCellValue('I'.$i,$c['year'].'.'.$c['month']);
                        $objActSheet->getStyle('A'.$i)->applyFromArray($styleTdLeft);
                        $objActSheet->getStyle('B'.$i)->applyFromArray($styleTdLeft);
                        $objActSheet->getStyle('C'.$i)->applyFromArray($styleTd);
                        $objActSheet->getStyle('D'.$i)->applyFromArray($styleTd);
                        $objActSheet->getStyle('E'.$i)->applyFromArray($styleTd);
                        $objActSheet->getStyle('F'.$i)->applyFromArray($styleTd);
                        $objActSheet->getStyle('G'.$i)->applyFromArray($styleTd);
                        $objActSheet->getStyle('H'.$i)->applyFromArray($styleTd);
                        $objActSheet->getStyle('I'.$i)->applyFromArray($styleTd);
                    }
                    ++$i;
                }
            }
            if(count($teachers)>0) {
                $objActSheet->mergeCells('A' . $i . ':I' . $i)->setCellValue('A' . $i, '');
                ++$i;
                $objActSheet->mergeCells('A' . $i . ':I' . $i)->setCellValue('A' . $i, '讲师介绍');
                $objActSheet->getStyle('A' . $i)->applyFromArray($styleTitle);
                ++$i;
                $objActSheet->setCellValue('A'.$i,'讲师');
                $objActSheet->setCellValue('B'.$i,'工作形式');
                $objActSheet->setCellValue('C'.$i,'工作年限');
                $objActSheet->mergeCells('D'.$i.':I'.$i)->setCellValue('D'.$i,'简介');
                $objActSheet->getStyle('A'.$i)->applyFromArray($styleTh);
                $objActSheet->getStyle('B'.$i)->applyFromArray($styleTh);
                $objActSheet->getStyle('C'.$i)->applyFromArray($styleTh);
                $objActSheet->getStyle('D'.$i)->applyFromArray($styleTh);
                foreach ($teachers as $t){
                    ++$i;
                    $objActSheet->setCellValue('A'.$i,$t['name']);
                    $objActSheet->setCellValue('B'.$i,$t['work_type']==1?'专职':'兼职');
                    $objActSheet->setCellValue('C'.$i,!empty($t['years'])?$t['years'].'年':'');
                    $objActSheet->mergeCells('D'.$i.':I'.$i)->setCellValue('D'.$i,$t['info']);
                    $objActSheet->getStyle('A'.$i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('B'.$i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('C'.$i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('D'.$i)->applyFromArray($styleTdLeft);
                }
                ++$i;
            }
            if(!empty($plan['note'])){
                $objActSheet->mergeCells('A' . $i . ':I' . $i)->setCellValue('A' . $i, '');
                ++$i;
                $objActSheet->mergeCells('A' . $i . ':I' . $i)->setCellValue('A' . $i, '备注');
                $objActSheet->getStyle('A' . $i)->applyFromArray($styleTitle);
                ++$i;
                $objActSheet->mergeCells('A' . $i . ':I' . $i)->setCellValue('A' . $i, $plan['note']);
                $objActSheet->getStyle('A' . $i)->applyFromArray($styleTdLeft);
            }


            $name=$plan['title'];
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

    //导出年度计划进度excel
    public function planprogress($planid){
        if($this->isAllowPlanid($planid)){
            $plan=$this->annualplan_model->get_row(array('id'=>$planid));
            //计划课程数据
            $this->load->library(array('countdata'));
            $dataym=$this->countdata->progressdata($planid);

            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objActSheet = $objPHPExcel->getActiveSheet()->setTitle('计划进度');
            //excel style
            $styleTitle = array(
                'font' => array('color' => array('argb' => 'FFffffff')),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('argb' => 'FF00bbd3')
                ),
            );
            $styleTh = array(
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER),
                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('argb' => 'FFf5f5f5')
                ),
            );
            $styleTd = array(
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER),
            );
            $styleTdLeft = array(
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER),
            );

            //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 8, 'Some value');
            $i=1;
            //部门总览
            if(count($dataym)>0){
                $objActSheet->mergeCells('A'.$i.':G'.$i)->setCellValue('A'.$i, '课程进度')
                    ->setCellValue('A'.($i+1), '时间')
                    ->setCellValue('B'.($i+1), '计划开课')
                    ->setCellValue('C'.($i+1), '实际开课')
                    ->setCellValue('D'.($i+1), '调出课程')
                    ->setCellValue('E'.($i+1), '调入课程')
                    ->setCellValue('F'.($i+1), '取消课程')
                    ->setCellValue('G'.($i+1), '加开课程');
                $objActSheet->getStyle('A'.$i)->applyFromArray($styleTitle);
                $objActSheet->getStyle('A'.($i+1))->applyFromArray($styleTh);
                $objActSheet->getStyle('B'.($i+1))->applyFromArray($styleTh);
                $objActSheet->getStyle('C'.($i+1))->applyFromArray($styleTh);
                $objActSheet->getStyle('D'.($i+1))->applyFromArray($styleTh);
                $objActSheet->getStyle('E'.($i+1))->applyFromArray($styleTh);
                $objActSheet->getStyle('F'.($i+1))->applyFromArray($styleTh);
                $objActSheet->getStyle('G'.($i+1))->applyFromArray($styleTh);
                $i+=2;$fi=$i;
                foreach ($dataym as $k=>$d){
                    $objActSheet->setCellValueExplicit('A'.$i, $k,PHPExcel_Cell_DataType::TYPE_STRING);
                    $objActSheet->setCellValue('B'.$i,round($d['plan_num']));
                    $objActSheet->setCellValue('C'.$i,round($d['actual_num']));
                    $objActSheet->setCellValue('D'.$i,round($d['change_out_num']));
                    $objActSheet->setCellValue('E'.$i,round($d['change_in_num']));
                    $objActSheet->setCellValue('F'.$i,round($d['cancel_num']));
                    $objActSheet->setCellValue('G'.$i,round($d['add_num']));
                    $objActSheet->getStyle('A'.$i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('B'.$i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('C'.$i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('D'.$i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('E'.$i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('F'.$i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('G'.$i)->applyFromArray($styleTd);
                    ++$i;
                }
                if(count($dataym)>1) {
                    $objActSheet->setCellValue('A' . $i, '总计');
                    $objActSheet->setCellValue('B' . $i, "=SUM(B" . $fi . ":B" . ($i - 1) . ")");
                    $objActSheet->setCellValue('C' . $i, "=SUM(C" . $fi . ":C" . ($i - 1) . ")");
                    $objActSheet->setCellValue('D' . $i, "=SUM(D" . $fi . ":D" . ($i - 1) . ")");
                    $objActSheet->setCellValue('E' . $i, "=SUM(E" . $fi . ":E" . ($i - 1) . ")");
                    $objActSheet->setCellValue('F' . $i, "=SUM(F" . $fi . ":F" . ($i - 1) . ")");
                    $objActSheet->setCellValue('G' . $i, "=SUM(G" . $fi . ":G" . ($i - 1) . ")");
                    $objActSheet->getStyle('A' . $i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('B' . $i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('C' . $i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('D' . $i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('E' . $i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('F' . $i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('G' . $i)->applyFromArray($styleTd);
                    ++$i;
                }
            }
            //预算总览
            $objActSheet->mergeCells('A' . $i . ':G' . $i)->setCellValue('A' . $i, '');
            ++$i;
            $objActSheet->mergeCells('A'.$i.':G'.$i)->setCellValue('A'.$i, '预算总览')
                ->setCellValue('A'.($i+1), '时间')
                ->mergeCells('B'.($i+1).':C'.($i+1))->setCellValue('B'.($i+1), '计划预算')
                ->mergeCells('D'.($i+1).':E'.($i+1))->setCellValue('D'.($i+1), '实际支出')
                ->mergeCells('F'.($i+1).':G'.($i+1))->setCellValue('F'.($i+1), '结余预算');
            $objActSheet->getStyle('A'.$i)->applyFromArray($styleTitle);
            $objActSheet->getStyle('A'.($i+1))->applyFromArray($styleTh);
            $objActSheet->getStyle('B'.($i+1))->applyFromArray($styleTh);
            $objActSheet->getStyle('D'.($i+1))->applyFromArray($styleTh);
            $objActSheet->getStyle('F'.($i+1))->applyFromArray($styleTh);
            $i+=2;$fi=$i;
            foreach ($dataym as $k=>$d){
                $objActSheet->setCellValueExplicit('A'.$i, $k,PHPExcel_Cell_DataType::TYPE_STRING);
                $objActSheet->mergeCells('B'.$i.':C'.$i)->setCellValue('B'.$i,round($d['plan_price']));
                $objActSheet->mergeCells('D'.$i.':E'.$i)->setCellValue('D'.$i,round($d['actual_price']));
                $objActSheet->mergeCells('F'.$i.':G'.$i)->setCellValue('F'.$i,round($d['plan_price']-$d['actual_price']));
                $objActSheet->getStyle('A'.$i)->applyFromArray($styleTd);
                $objActSheet->getStyle('B'.$i)->applyFromArray($styleTd);
                $objActSheet->getStyle('D'.$i)->applyFromArray($styleTd);
                $objActSheet->getStyle('F'.$i)->applyFromArray($styleTd);
                ++$i;
            }
            if(count($dataym)>1){
                $objActSheet->setCellValue('A'.$i,'全部');
                $objActSheet->mergeCells('B'.$i.':C'.$i)->setCellValue('B'.$i,"=SUM(B".$fi.":B".($i-1).")");
                $objActSheet->mergeCells('D'.$i.':E'.$i)->setCellValue('D'.$i,"=SUM(D".$fi.":D".($i-1).")");
                $objActSheet->mergeCells('F'.$i.':G'.$i)->setCellValue('F'.$i,"=SUM(F".$fi.":F".($i-1).")");
                $objActSheet->getStyle('A'.$i)->applyFromArray($styleTd);
                $objActSheet->getStyle('B'.$i)->applyFromArray($styleTd);
                $objActSheet->getStyle('D'.$i)->applyFromArray($styleTd);
                $objActSheet->getStyle('F'.$i)->applyFromArray($styleTd);
                ++$i;
            }

            //进度详情
            $objActSheet->mergeCells('A' . $i . ':G' . $i)->setCellValue('A' . $i, '');
            ++$i;
            $objActSheet->mergeCells('A'.$i.':G'.$i)->setCellValue('A'.$i, '进度详情')
                ->setCellValue('A'.($i+1), '时间')
                ->mergeCells('B'.($i+1).':C'.($i+1))->setCellValue('B'.($i+1), '课程名称')
                ->setCellValue('D'.($i+1), '执行情况')
                ->setCellValue('E'.($i+1), '开课时间')
                ->setCellValue('F'.($i+1), '计划预算')
                ->setCellValue('G'.($i+1), '实际支出');
            $objActSheet->getStyle('A'.$i)->applyFromArray($styleTitle);
            $objActSheet->getStyle('A'.($i+1))->applyFromArray($styleTh);
            $objActSheet->getStyle('B'.($i+1))->applyFromArray($styleTh);
            $objActSheet->getStyle('D'.($i+1))->applyFromArray($styleTh);
            $objActSheet->getStyle('E'.($i+1))->applyFromArray($styleTh);
            $objActSheet->getStyle('F'.($i+1))->applyFromArray($styleTh);
            $objActSheet->getStyle('G'.($i+1))->applyFromArray($styleTh);
            $i+=2;
            $dataymIndex=0;
            foreach ($dataym as $k=>$d) {
                ++$dataymIndex;
                if (count($d['courses']) > 0) {
                    $objActSheet->mergeCells('A'.$i.':A'.($i+count($d['courses'])-1))->setCellValueExplicit('A'.$i, $k,PHPExcel_Cell_DataType::TYPE_STRING);
                    foreach ($d['courses'] as $ck => $c) {
                        $objActSheet->mergeCells('B'.$i.':C'.$i)->setCellValue('B'.$i, empty($c['plan_course_title'])?$c['title']:$c['plan_course_title']);
                        $status_txt='开课';
                        if(empty($c['pc_id'])||$c['annual_plan_id']!=$plan['id']){
                            $status_txt = '加课';
                        }elseif(empty($c['course_id']) || $c['isdel']==1 || $c['ispublic']==2){
                            $status_txt = count($dataym)==$dataymIndex?'未开':'取消';
                        }elseif($c['annual_plan_id']==$plan['id']&&($c['plan_year'].'.'.$c['plan_month']!=$k)){
                            $status_txt = '调入';
                        }elseif($c['isdel']==2&&$c['ispublic']==1&&(date("Y.m",strtotime($c['time_start']))!=$k)){
                            $status_txt = '调出';
                        }
                        $objActSheet->setCellValue('D'.$i, $status_txt);
                        $objActSheet->setCellValue('E'.$i,(!empty($c['time_start']))?date("m.d H:i",strtotime($c['time_start'])):'');
                        $objActSheet->setCellValue('F'.$i, $c['price']);
                        $objActSheet->setCellValue('G'.$i, $c['expend']);
                        $objActSheet->getStyle('A'.$i)->applyFromArray($styleTd);
                        $objActSheet->getStyle('D'.$i)->applyFromArray($styleTd);
                        $objActSheet->getStyle('E'.$i)->applyFromArray($styleTd);
                        $objActSheet->getStyle('F'.$i)->applyFromArray($styleTd);
                        $objActSheet->getStyle('G'.$i)->applyFromArray($styleTd);
                        ++$i;
                    }
                }else{
                    $objActSheet->setCellValueExplicit('A'.$i, $k,PHPExcel_Cell_DataType::TYPE_STRING);
                    $objActSheet->mergeCells('B'.$i.':G'.$i)->setCellValue('B'.$i, '暂无课程记录');
                    $objActSheet->getStyle('A'.$i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('B'.$i)->applyFromArray($styleTd);
                    ++$i;
                }
            }


            $name=$plan['title'].'执行进度'.date("YmdHis");
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

    //导出学员课程记录
    public function courserecords($studentid){
        $sql = "select course.title,course.time_start,teacher.name as teacher,count(DISTINCT survey.id) AS survey_num,count(DISTINCT ratings.id) AS ratings_num from " . $this->db->dbprefix('course_apply_list') . " a left join " . $this->db->dbprefix('course') . " course on a.course_id=course.id left join " . $this->db->dbprefix('teacher') . " teacher on course.`teacher_id`=teacher.id left join " . $this->db->dbprefix('course_survey_list') . " survey on survey.course_id=course.id left join ".$this->db->dbprefix('course_ratings_list')." ratings on ratings.course_id=course.id where course.`company_code`='{$this->_logininfo['company_code']}' and a.`student_id`='$studentid' ";
        $sql .= " group by course.id order by course.id desc ";
        $query=$this->db->query($sql);
        $courses=$query->result_array();
        $student=$this->student_model->get_row(array('id'=>$studentid));

        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '课程名称')
            ->setCellValue('B1', '开始时间')
            ->setCellValue('C1', '课程讲师')
            ->setCellValue('D1', '内训/公开');
        foreach($courses as $k => $c){
            $num=$k+2;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$num, $c['title'])
                ->setCellValue('B'.$num, date('Y-m-d H:i',strtotime($c['time_start'])))
                ->setCellValue('C'.$num, !empty($c['teacher'])?$c['teacher']:'无')
                ->setCellValue('D'.$num, ($c['external']==1)?'公开':'内训');
        }
        $objPHPExcel->getActiveSheet()->setTitle($student['name'].'课程记录');
        $objPHPExcel->setActiveSheetIndex(0);
        $name='《'.$student['name'].'》课程记录';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    //导出学员评估记录
    public function evaluaterecords($studentid){
        $sql = "select evaluation_student.ability_job_evaluation_id,evaluation.name as evaluation,evaluation_student.isdel,abilityjob.name as abilityjob,point,others_point,abilityjob.point_standard from " . $this->db->dbprefix('company_ability_job_evaluation_student') . " evaluation_student "
            . "left join " . $this->db->dbprefix('company_ability_job'). " abilityjob on evaluation_student.ability_job_id=abilityjob.id "
            . "left join " . $this->db->dbprefix('company_ability_job_evaluation'). " evaluation on evaluation_student.ability_job_evaluation_id=evaluation.id "
            . "where evaluation_student.company_code='".$this->_logininfo['company_code']."' and evaluation_student.student_id=$studentid ";
        $sql .= " order by evaluation_student.id desc ";
        $query=$this->db->query($sql);
        $evaluates = $query->result_array();
        $student=$this->student_model->get_row(array('id'=>$studentid));

        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '评估名称')
            ->setCellValue('B1', '能力模型')
            ->setCellValue('C1', '标准总分')
            ->setCellValue('D1', '自评总分')
            ->setCellValue('E1', '他评总分');
        foreach($evaluates as $k => $e){
            $num=$k+2;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$num, !empty($e['evaluation'])?$e['evaluation']:'无')
                ->setCellValue('B'.$num, $e['abilityjob'])
                ->setCellValue('C'.$num, $e['point_standard'])
                ->setCellValue('D'.$num, $e['point'])
                ->setCellValue('E'.$num, $e['others_point']);
        }
        $objPHPExcel->getActiveSheet()->setTitle($student['name'].'评估记录');
        $objPHPExcel->setActiveSheetIndex(0);
        $name='《'.$student['name'].'》评估记录';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    //导出学员年度调研记录
    public function annualrecords($studentid){
        $sql = "select annual_survey.*,annual_answer.id as answer_id from " . $this->db->dbprefix('annual_answer') . " annual_answer "
            . "left join " . $this->db->dbprefix('annual_survey'). " annual_survey on annual_answer.annual_survey_id=annual_survey.id "
            . "where annual_answer.company_code='".$this->_logininfo['company_code']."' and annual_answer.student_id=$studentid and annual_answer.step=5 ";
        $sql .= " order by annual_answer.id desc ";
        $query=$this->db->query($sql);
        $annuals = $query->result_array();
        $student=$this->student_model->get_row(array('id'=>$studentid));

        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '调研名称')
            ->setCellValue('B1', '开始时间');
        foreach($annuals as $k => $a){
            $num=$k+2;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$num, $a['title'])
                ->setCellValue('B'.$num, date('Y-m-d H:i',strtotime($a['time_start'])));
        }
        $objPHPExcel->getActiveSheet()->setTitle($student['name'].'年度调研记录');
        $objPHPExcel->setActiveSheetIndex(0);
        $name='《'.$student['name'].'》年度调研记录';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    //是否是自己公司下的课程
    private function isAllowCourseid($courseid){
        if(empty($courseid)||$this->course_model->get_count(array('id' => $courseid,'company_code'=>$this->_logininfo['company_code']))<=0){
            return false;
        }else{
            return true;
        }
    }

    //是否是自己公司下的计划
    private function isAllowPlanid($planid){
        if(empty($planid)||$this->annualplan_model->get_count(array('id' => $planid,'company_code'=>$this->_logininfo['company_code']))<=0){
            return false;
        }else{
            return true;
        }
    }


}
