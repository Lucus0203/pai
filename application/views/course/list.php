<script type="text/javascript">
$(document).ready(function(){
        $('.Wdate').eq(1).focus(function(){
            if($('.Wdate').eq(1).val()==''){
                $('.Wdate').eq(1).val($('.Wdate').eq(0).val());
            }
        });
});
</script>
<div class="wrap">
			<div class="comBox">
				<div class="topNavi">
<?php if($loginInfo['role']==1||$roleInfo['coursecreate']==1){ ?>
                                    <a href="<?php echo site_url('course/coursecreate') ?>" class="fRight blueH35"><i class="addQuan"></i>创建新课程</a>
<?php } ?>
					<ul class="topNaviUl">
                                                <li <?php if(empty($parm['status'])){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('course/courselist') ?>">全部课程</a></li>
                                                <li <?php if($parm['status']==4){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('course/courselist').'?status=4' ?>">待发布</a></li>
						<li <?php if($parm['status']==5){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('course/courselist').'?status=5' ?>">报名未开始</a></li>
						<li <?php if($parm['status']==1){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('course/courselist').'?status=1' ?>">报名中</a></li>
						<li <?php if($parm['status']==2){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('course/courselist').'?status=2' ?>">进行中</a></li>
						<li <?php if($parm['status']==3){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('course/courselist').'?status=3' ?>">已结束</a></li>
					</ul>

				</div>

				<div class="seachBox clearfix bgGray">
                                    <form method="get" action="">
					<ul>
						<li class="w250">
                                                    <input name="keyword" type="text" value="<?php echo $parm['keyword'] ?>" class="ipt w250" placeholder="关键字">
						</li>
						<li class="w496">
                                                    <input name="time_start" type="text" value="<?php echo $parm['time_start'] ?>" class="ipt w225 mr10 Wdate" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" autocomplete="off" placeholder="请选择查询开始时间">至
                                                    <input name="time_end" type="text" value="<?php echo $parm['time_end'] ?>" class="ipt w225 ml10 Wdate" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" autocomplete="off" placeholder="请选择查询结束时间">
						</li>

                                                <li class="btn"><input type="submit" class="grayH42" value="查询" /></li>
					</ul>
                                        </form>
				</div>
				<div class="listBox">
                                    <?php foreach ($courses as $c){ ?>
                                        <div class="listCont">
						<p class="operaBtn">
<?php if($loginInfo['role']==1||$roleInfo['courseedit']==1){ ?>
                                                    <a href="<?php echo site_url('course/courseedit/'.$c['id']);?>" class="editBtn"><i class="iedit"></i>编辑</a><a href="<?php echo site_url('course/coursedel/'.$c['id']);?>" class="delBtn"><i class="idel"></i>删除</a>
<?php } ?>
<!--                                                    <a href="javascript:;" class="shareBtn"><i class="ishar"></i>分享</a></p>-->

                                                <div class="imgBox"><a href="<?php echo site_url('course/courseinfo/'.$c['id']);?>"><img src="<?php echo empty($c['page_img'])?base_url().'images/course_default_img.jpg':base_url('uploads/course_img/'.$c['page_img']) ?>" alt="" width="160"></a></div>
						<div class="listText">
                                                    <p class="titp"><a class="blue" href="<?php echo site_url('course/courseinfo/'.$c['id']);?>"><?php echo $c['title'] ?></a>
                                                            <?php if($c['status']==1){//1报名中2进行中3结束4待发布5待开启报名9其他 ?>
                                                            <span class="greenH25">报名中</span>
                                                            <?php }elseif($c['status']==2){ ?>
                                                            <span class="orangeH25">进行中</span>
                                                            <?php }elseif($c['status']==3){ ?>
                                                            <span class="grayH25">已结束</span>
                                                            <?php }elseif($c['status']==4){ ?>
                                                            <span class="orangeH25">待发布</span>
                                                            <?php }elseif($c['status']==5){ ?>
                                                            <span class="orangeH25">报名未开始</span>
                                                            <?php } ?>
                                                        </p>
                                                        <?php if(!empty($c['teacher'])){ ?>
                                                        <p>课程讲师：<a class="blue" href="<?php echo site_url('teacher/teacherinfo/'.$c['teacher_id']); ?>"><?php echo $c['teacher'] ?></a> </p><?php } ?>
							<p><span class="mr30">开课时间：<?php echo $c['time_start'] ?></span><span class="mr30">结束时间：<?php echo $c['time_end'] ?></span><?php echo !empty($c['time_endregister_end'])?'报名截止：'.$c['time_endregister_end']:'' ?></p>
							<p>开课地点：<?php echo $c['address'] ?></p>
						</div>
					</div>
                                    <?php } ?>
				</div>
                                <div class="pageNavi">
					<?php echo $links ?>
				</div>
			</div>
		</div>