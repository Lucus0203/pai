<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css" />
<div class="wrap">
        <div class="titCom clearfix"><span class="titSpan"><?php echo $course['title'] ?>  </span><a href="javascript:void(0)" class="<?php echo $course['status_class']; ?>"><?php echo $course['status_str']; ?></a></div>
        <div class="topNaviKec">
                <?php $this->load->view ( 'course/top_navi' ); ?>

        </div>
        <div class="comBox clearfix">
                <div class="baoming">

                        <div class="sideLeft">
                                <ul class="sideLnavi">
<?php if($loginInfo['role']==1||$roleInfo['surveyedit']==1){ ?>
                                        <li><a href="<?php echo site_url('course/surveyedit/'.$course['id']) ?>">作业编辑<i></i></a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['surveylist']==1){ ?>
                                        <li class="cur"><a href="<?php echo site_url('course/surveylist/'.$course['id']) ?>">提交名单<i></i></a></li>
<?php } ?>
                                </ul>

                        </div>
                        <div class="contRight">
                                <p class="clearfix f14 mb20">共有<?php echo $total ?>人提交了课前作业</p>
                                <table cellspacing="0" class="listTable">
                                        <tbody>
                                                <tr>
                                                        <th>姓名</th>
                                                        <th>工号</th>
                                                        <th>职务</th>
                                                        <th>部门</th>
                                                        <th>手机</th>
                                                        <th>提交时间</th>
                                                        <th>操作</th>
                                                </tr>
                                                <?php foreach ($surveylist as $h) { ?>
                                                <tr>
                                                        <td class="blue"><?php echo $h['name'] ?></td>
                                                        <td><?php echo $h['job_code'] ?></td>
                                                        <td><?php echo $h['job_name'] ?></td>
                                                        <td><?php echo $h['department'] ?></td>
                                                        <td><?php echo $h['mobile'] ?></td>
                                                        <td><?php echo date("Y-m-d H:i",strtotime($h['created'])) ?></td>
                                                        <td><a href="<?php echo site_url('course/surveydetail/'.$h['course_id'].'/'.$h['student_id']) ?>" class="blue" target="_blank">查看作业</a></td>
                                                </tr>
                                                <?php } ?>

                                        </tbody>
                                </table>

                                <div class="pageNavi">
                                        <?php echo $links ?>
                                </div>
                        </div>

                </div>

        </div>
</div>