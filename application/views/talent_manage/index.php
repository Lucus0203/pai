<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css?<?php echo $this->config->item('version');?>"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css?<?php echo $this->config->item('version');?>"/>
<div class="wrap">
    <div class="textureCont width100">
        <div class="texturetip p15 clearfix"><span class="fLeft pt5">人才管理</span>
        </div>
        <div class="clearfix textureBox noborder">
            <div class="textureSide">
                <div class="fnavi">
                    <a class="flink mb10 <?php echo empty($current_department['id'])?'on':'' ?>" href="<?php echo site_url('talentmanage/index') ?>">所有学员</a>
                </div>
                <?php foreach ($departments as $d) { ?>
                    <div class="fnavi">
                        <a href="<?php echo site_url('talentmanage/index/' . $d['id']) ?>" class="flink <?php echo $current_department['id'] == $d['id'] ? 'on' : '' ?>"><i class="iup fa fa-angle-right fa-lg"></i><?php echo $d['name'] ?></a>
                        <ul class="clink departChildren<?php echo $d['id'] ?>">
                            <?php if (!empty($d['departs'])) {
                                foreach ($d['departs'] as $dp) { ?>
                                    <li class="<?php echo $current_department['id'] == $dp['id'] ? 'on' : '' ?>"><a href="<?php echo site_url('talentmanage/index/' . $dp['id']) ?>"><?php echo $dp['name'] ?></a>
                                    </li>
                                <?php }
                            } ?>
                        </ul>
                    </div>
                <?php } ?>
            </div>
            <div class="textureCont">
                <div class="texturetip textureWite pt10 clearfix mr20">
                    <p class="fLeft clearfix f14"><?php echo !empty($current_department['name'])?$current_department['name']:'所有学员' ?>(共有<?php echo round($total) ?>人)</p>
                </div>
                <div class="clearfix mr20" style="min-height: 450px;">
                    <?php if (!empty($students)) { ?>
                        <table cellspacing="0" class="listTable">
                            <colgroup>
                                <col width="20%">
                                <col width="20%">
                                <col width="20%">
                                <col width="20%">
                                <col width="20%">
                            </colgroup>
                            <tbody>
                            <tr>
                                <th class="aLeft">姓名</th>
                                <th class="aCenter">课程记录</th>
<!--                                <th class="aCenter">考试记录</th>-->
                                <th class="aCenter">评估记录</th>
                                <th class="aCenter">年度调研</th>
                                <th class="aCenter">操作</th>
                            </tr>
                            <?php foreach ($students as $s) { ?>
                                <tr>
                                    <td class="aLeft"><a class="blue" href="<?php echo site_url('talentmanage/courserecords/'.$s['id']) ?>"><?php echo $s['name'] ?></a></td>
                                    <td class="aCenter"><?php echo $s['course_num'] ?></td>
                                    <td class="aCenter"><?php echo $s['evaluation_num'] ?></td>
                                    <td class="aCenter"><?php echo $s['annual_num'] ?></td>
                                    <td class="aCenter">
                                        <a class="blue" href="<?php echo site_url('talentmanage/courserecords/'.$s['id']) ?>">查看</a>
                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>
                        <?php echo $links ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>