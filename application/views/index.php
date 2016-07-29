<div class="wrap clearfix">
    <div class="main">
        <ul class="listDetail clearfix">
            <li><i><?php echo $courses_num; ?></i>课程总数</li>
            <li><i><?php echo $teachers_num; ?></i>讲师总数</li>
            <li><i><?php echo $students_num ?></i>学员总数</li>
            <li><i><?php echo $adms_num ?></i>分级管理员</li>
        </ul>

        <?php if (count($courses) > 0) { ?>
            <div class="listBox listBox02">
                <div class="ttl01">最近的课程项目</div>
                <?php foreach ($courses as $c) { ?>
                    <div class="listCont">
                        <div class="imgBox"><img
                                src="<?php echo empty($c['page_img']) ? base_url() . 'images/course_default_img.jpg' : base_url('uploads/course_img/' . $c['page_img']) ?>"
                                alt="" width="130"></div>
                        <div class="listText">
                            <p class="titp"><a
                                    href="<?php echo site_url('course/courseinfo/' . $c['id']); ?>"><?php echo $c['title'] ?></a>
                            </p>
                            <?php if (!empty($c['teacher'])) { ?>
                                <p>课程讲师：<a class="blue"
                                           href="<?php echo site_url('teacher/teacherinfo/' . $c['teacher_id']); ?>"><?php echo $c['teacher'] ?></a>
                                </p><?php } ?>
                            <p>开课时间：<?php echo $c['time_start'] ?> 至 <?php echo $c['time_end'] ?></p>
                            <?php echo !empty($c['apply_end']) ? '<p>报名截止：' . $c['apply_end'] . '</p>' : '' ?>
                        </div>
                    </div>
                <?php } ?>

            </div>
        <?php } ?>
    </div>
    <div class="sideBar">
        <div class="sideBox">
            <p class="ttl02"><?php echo $company['name'] ?></p>
            <dl class="dl01">
                <dt>管理账号：</dt>
                <dd><?php echo $loginInfo['user_name'] ?>
                    <br><?php echo $loginInfo['real_name'] . ' ' . $loginInfo['mobile'] ?></dd>
                <dt>公司编号：</dt>
                <dd><?php echo $loginInfo['company_code'] ?></dd>
            </dl>
        </div>
        <div class="sideBox">
            <p class="ttl02">常用功能</p>
            <ul class="listFunction clearfix">
                <li><a href="<?php echo site_url('course/coursecreate') ?>"><i class="ico_01"></i>创建课程</a></li>
                <li><a href="<?php echo site_url('department/index') ?>"><i class="ico_05"></i>组织管理</a></li>
                <li><a href="javascript:void(0)"><i class="ico_04"></i>消息通知</a></li>
            </ul>
        </div>
        <div class="sideBox">
            <p class="ttl02">学员登录扫一扫</p>
            <p class="aCenter"><img width="70%" src="<?php echo base_url() ?>uploads/login_qrcode/<?php echo $loginInfo['company_code'] ?>.png" alt="">
                <br><a class="blue aCenter" href="<?php echo site_url('index/loginqrcode') ?>" target="_blank">下载二维码</a>
            </p>

        </div>

    </div>
</div>
<?php $this->load->view ( 'walkthrough' ); ?>