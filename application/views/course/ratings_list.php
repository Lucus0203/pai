<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css" />
<div class="wrap">
        <div class="titCom clearfix"><span class="titSpan"><?php echo $course['title'] ?>  </span><span class="<?php echo $course['status_class']; ?> ml20"><?php echo $course['status_str']; ?></span></div>
        <div class="topNaviKec">
                <?php $this->load->view ( 'course/top_navi' ); ?>

        </div>
        <div class="comBox clearfix">
                <div class="baoming">

                        <div class="sideLeft">
                                <ul class="sideLnavi">
<?php if($loginInfo['role']==1||$roleInfo['ratingsedit']==1){ ?>
                                        <li><a href="<?php echo site_url('course/ratingsedit/'.$course['id']) ?>">问题设置<i></i></a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['ratingslist']==1){ ?>
                                        <li class="cur"><a href="<?php echo site_url('course/ratingslist/'.$course['id']) ?>">反馈结果<i class="ml10 fa fa-angle-right fa-lg"></i></a></li>
<?php } ?>
                                </ul>

                        </div>
                        <div class="contRight">
                                <p class="clearfix f14 mb20">共有<?php echo $total ?>对本次进行评价，综合评分<span class="startBox"><i style="width: <?php echo $avgstar/5*100 ?>%;"></i></span></p>
                                <table cellspacing="0" class="listTable">
                                        <tbody>
                                                <tr>
                                                        <th>姓名</th>
                                                        <th>工号</th>
                                                        <th>职务</th>
                                                        <th>部门</th>
                                                        <th>手机</th>
                                                        <th>评价星值</th>
                                                        <th>操作</th>
                                                </tr>
                                                <?php foreach ($ratingslist as $h) { ?>
                                                <tr>
                                                        <td class="blue aCenter"><?php echo $h['name'] ?></td>
                                                        <td class="aCenter"><?php echo $h['job_code'] ?></td>
                                                        <td class="aCenter"><?php echo $h['job_name'] ?></td>
                                                        <td class="aCenter"><?php echo $h['department'] ?></td>
                                                        <td class="aCenter"><?php echo $h['mobile'] ?></td>
                                                        <td><span class="startBox"><i style="width: <?php echo $h['star']/5*100 ?>%;"></i></span></td>
                                                        <td class="aCenter"><a href="<?php echo site_url('course/ratingsdetail/'.$h['course_id'].'/'.$h['student_id']) ?>" class="blue" target="_blank">查看反馈</a></td>
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