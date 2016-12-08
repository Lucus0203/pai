<?php
/**
 * Created by PhpStorm.
 * User: lucus
 * Date: 2016/12/2
 * Time: 上午10:13
 * 数据统计类
 *
 */
class Countdata
{
    protected $CI;

    function __construct($config = array())
    {

        $this->CI =& get_instance();

        $this->CI->load->library(array('session', 'pagination'));
        $this->CI->load->model(array('user_model', 'company_model','annualplan_model'));
        $this->_logininfo = $this->CI->session->userdata('loginInfo');
    }

    public function progressdata($planid){
        $this->CI->load->database ();
        $dataym=$plancourse=array();
        $coursesql = "select pc.id,pc.price,concat(pc.year,pc.month) as ym from " . $this->CI->db->dbprefix('annual_plan_course') ." pc ".
            " where pc.annual_plan_id = $planid ".
            " and pc.company_code = '".$this->_logininfo['company_code']."' ".
            " and pc.openstatus=1 ";
        $coursesql = "select count(*) as count_num,sum(s.price) as price,s.ym from ($coursesql) s group by s.ym order by s.ym asc ";
        $query = $this->CI->db->query($coursesql);
        $data = $query->result_array();
        foreach ($data as $d){
            $plancourse[$d['ym']]['plan_num']=$d['count_num'];
            $plancourse[$d['ym']]['plan_price']=$d['price'];
        }
        if(count($data)>0){
            $first=$data[0];
            $last=end($data);
            $firsty=substr($first['ym'],0,4);
            $firstm=substr($first['ym'],-2);
            $lastym=$last['ym']>date("Ym",time())?date("Ym",time()):$last['ym'];
            $lasty=substr($lastym,0,4);
            $lastm=substr($lastym,-2);
            for($y=$firsty;$y<=$lasty;$y++){
                $fm=($firsty==$y)?$firstm*1:1;
                $lm=($lasty==$y)?$lastm*1:12;
                for($m=$fm;$m<=$lm;$m++){
                    $ym=$m<10?$y.'.'.'0'.$m:$y.'.'.$m;
                    $dataym[$ym]=$plancourse[$ym];
                    //计划课程
                    $mm=$m<10?'0'.$m:$m;
                    $plansql="select course.id as cid,course.title,course.time_start,course.time_end,course.price,course.expend,course.ispublic,course.isdel,pc.title as plan_course_title,pc.id as pc_id,pc.course_id,pc.year as plan_year,pc.month as plan_month,pc.price as plan_price,pc.annual_plan_id from ".$this->CI->db->dbprefix('annual_plan_course')." pc ".
                        " left join ".$this->CI->db->dbprefix('course')." course on course.id=pc.course_id ".
                        " where pc.openstatus=1 and pc.year='".$y."' and pc.month='".$mm."' and pc.annual_plan_id=$planid and pc.company_code = '".$this->_logininfo['company_code']."' ";
                    $query = $this->CI->db->query($plansql);
                    $plancourses=$query->result_array();
                    $dataym[$ym]['plan_num']=count($plancourses);
                    //计划预算
                    $sql="select sum(p.plan_price) as plan_price from($plansql) p ";
                    $query = $this->CI->db->query($sql);
                    $data = $query->row_array();
                    $dataym[$ym]['plan_price']=$data['plan_price'];
                    //调出课程
                    $sql="select count(*) as count_num from ($plansql) p ".
                        " where p.isdel = 2 and p.ispublic = 1 and (p.time_start < '".$y.'-'.$mm."-01 00:00:00' or p.time_start > '".$y.'-'.$mm."-31 23:59:59') ";
                    $query = $this->CI->db->query($sql);
                    $data = $query->row_array();
                    $dataym[$ym]['change_out_num']=$data['count_num'];
                    //取消或者未开课程
                    $sql="select count(*) as count_num from ($plansql) p ".
                        " where (p.course_id ='' or p.course_id is null or p.isdel=1 or p.ispublic = 2) ";
                    $query = $this->CI->db->query($sql);
                    $data = $query->row_array();
                    $dataym[$ym]['cancel_num']=$data['count_num'];
                    //实际开课,实际支出
                    $actualsql="select course.id as cid,course.title,course.time_start,course.time_end,course.price,course.expend,course.ispublic,course.isdel,pc.title as plan_course_title,pc.id as pc_id,pc.course_id,pc.year as plan_year,pc.month as plan_month,pc.price as plan_price,pc.annual_plan_id from ".$this->CI->db->dbprefix('course')." course ".
                        " left join ".$this->CI->db->dbprefix('annual_plan_course')." pc on course.id=pc.course_id and pc.openstatus=1 ".
                        " where course.company_code = '".$this->_logininfo['company_code']."' and course.isdel = 2 and course.ispublic = 1 and (course.time_start >= '".$y.'-'.$mm."-01 00:00:00' and course.time_start <= '".$y.'-'.$mm."-31 23:59:59') ";
                    //实际开课数,实际支出
                    $sql="select count(*) as count_num , sum(expend) as expend from ($actualsql) a ";
                    $query = $this->CI->db->query($sql);
                    $data = $query->row_array();
                    $dataym[$ym]['actual_num']=$data['count_num'];
                    $dataym[$ym]['actual_price']=$data['expend'];
                    //加开课程
                    $sql="select * from ($actualsql) a ".
                        " where a.pc_id is null or a.annual_plan_id <> $planid and a.isdel=2 and a.ispublic=1 ";
                    $query = $this->CI->db->query($sql);
                    $addcourse = $query->result_array();
                    $dataym[$ym]['add_num']=count($addcourse);
                    //调入课程
                    $sql="select * from ($actualsql) a ".
                        " where a.annual_plan_id = $planid and (a.plan_year<>'$y' or a.plan_month<>'$mm') ";
                    $query = $this->CI->db->query($sql);
                    $addplancourse = $query->result_array();
                    $dataym[$ym]['change_in_num']=count($addplancourse);

                    //课程内容$plancourses,$addcourse
                    $dataym[$ym]['courses']=array_merge($plancourses,$addplancourse,$addcourse);
                }
            }
        }
        return $dataym;
    }

    //是否是自己公司下的计划
    private function isAllowPlanid($planid){
        if(empty($planid)||$this->CI->annualplan_model->get_count(array('id' => $planid,'company_code'=>$this->_logininfo['company_code']))<=0){
            return false;
        }else{
            return true;
        }
    }

}
