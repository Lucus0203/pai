<div class="wrap">
			<div class="titCom clearfix">
<?php if($loginInfo['role']==1||$roleInfo['teachercreate']==1){ ?>
                            <a href="<?php echo site_url('teacher/teachercreate') ?>" class="fRight blueH35"><i class="addQuan"></i>新增讲师</a>
<?php } ?>
                        <span class="titSpan">讲师列表</span></div>
			<div class="comBox">
				<div class="seachBox clearfix">
                                    <form method="get" action="">
					<ul>
						<li>
                                                    <input name="keyword" value="<?php echo $parm['keyword'] ?>" type="text" class="ipt" placeholder="关键字">
						</li>
						<li>
                                                        <select name="type" class="ipt">
								<option value="">师资类型</option>
                                                                <option value="1" <?php if($parm['type']==1){ ?>selected=""<?php } ?>>内部</option>
								<option value="2" <?php if($parm['type']==2){ ?>selected=""<?php } ?>>外部</option>
							</select>
						</li>
						<li>
                                                    <input name="specialty" value="<?php echo $parm['specialty'] ?>" type="text" class="ipt" placeholder="擅长类型">
						</li>
						<li>
                                                        <select name="work_type" class="ipt">
								<option value="">工作形式</option>
								<option value="1" <?php if($parm['work_type']==1){ ?>selected=""<?php } ?>>专职</option>
								<option value="2" <?php if($parm['work_type']==2){ ?>selected=""<?php } ?>>兼职</option>
							</select>
						</li>
                                                <li class="btn"><input type="submit" value="查询" class="grayH42" /></li>
					</ul>
                                        </form>
				</div>
				<div class="listBox">
                                    <?php foreach ($teachers as $t) { ?>
					<div class="listCont">
                                                <?php if($loginInfo['role']==1||$roleInfo['teacheredit']==1){ ?>
						<p class="operaBtn">
                                                    <a href="<?php echo site_url('teacher/teacheredit/'.$t['id']); ?>" class="editBtn"><i class="iedit"></i>编辑</a><a href="<?php echo site_url('teacher/teacherdel/'.$t['id']);?>" class="delBtn"><i class="idel"></i>删除</a></p>
                                                <?php } ?>

                                                <div class="imgBox"><a href="<?php echo site_url('teacher/teacherinfo/'.$t['id']); ?>"><img src="<?php echo base_url();?>uploads/teacher_img/<?php echo $t['head_img'] ?>" alt="" width="110"></a></div>
						<div class="listText">
                                                    <p class="titp"><a class="blue" href="<?php echo site_url('teacher/teacherinfo/'.$t['id']); ?>"><?php echo $t['name'] ?></a></p>
							<p>讲师头衔：<?php echo $t['title'] ?></p>
							<p><span class="mr30">师资类型：<?php echo $t['type']==1?'内部':'外部' ?></span><span class="mr30">擅长类别：<?php echo $t['specialty'] ?></span>授课年限：<?php echo $t['years'] ?>年</p>
						</div>
					</div>
                                    <?php } ?>
				</div>
                                <div class="pageNavi">
					<?php echo $links ?>
				</div>
			</div>
		</div>