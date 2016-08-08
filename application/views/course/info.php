<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css" />
<script type="text/javascript">
    $(function(){$('.shareBtn').click(function(){return confirm('确定发布吗?');});});
</script>
<div class="wrap">
        <div class="titCom clearfix"><span class="titSpan"><?php echo $course['title'] ?>  </span><a href="javascript:void(0);" class="<?php echo $course['status_class']; ?>"><?php echo $course['status_str']; ?></a></div>
        <div class="topNaviKec">
                <?php $this->load->view ( 'course/top_navi' ); ?>

        </div>
        <div class="comBox">
                <p class="opBtn">
<?php if($loginInfo['role']==1||$roleInfo['courseedit']==1){ ?>
                    <a href="<?php echo site_url('course/courseedit/'.$course['id']);?>" class="editBtn"><i class="iedit"></i>编辑课程</a><a href="<?php echo site_url('course/coursedel/'.$course['id']);?>" class="delBtn"><i class="idel"></i>删除课程</a>
<?php } ?><?php if($course['ispublic']!=1){ ?><a href="<?php echo site_url('course/coursepublic/'.$course['id']);?>" class="shareBtn"><i class="ishar"></i>发布</a></p><?php } ?>

                <div class="listBox">
                        <div class="listCont listContGray">
                            <div class="imgBox"><img src="<?php echo empty($course['page_img'])?base_url().'images/course_default_img.jpg':base_url('uploads/course_img/'.$course['page_img']) ?>" alt="" width="160"></div>
                                <div class="listText">
                                        <p class="titp"><?php echo $course['title'] ?></p>
                                        <p>开课时间：<?php echo $course['time_start'] ?> 至 <?php echo $course['time_end'] ?></p>
                                        <p>课程地点：<?php echo $course['address'] ?></p>
                                        <p>课程讲师：<a href="<?php echo site_url('teacher/teacherinfo/'.$teacher['id']) ?>" class="blue"><?php echo $teacher['name'] ?></a></span></p>

                                        <p>培训对象：<?php echo $course['target'] ?></p>
                                </div>
                        </div>

                </div>
                <dl class="kecDl">
                        <?php if(!empty($course['info'])){ ?>
                        <dt>课程介绍</dt>
                        <dd class="noborder">
                                <?php echo nl2br($course['info']) ?>
                        </dd>
                        <?php } ?>
                        <dt>课程收益</dt>
                        <dd><?php echo nl2br($course['income']) ?></dd>
                        <dt>课程大纲</dt>
                        <dd class="noborder">
                                <?php echo nl2br($course['outline']) ?>
                        </dd>
                </dl>

        </div>
</div>