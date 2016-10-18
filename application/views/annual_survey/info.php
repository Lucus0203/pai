<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css" />
<div class="wrap">
    <div class="titCom clearfix">
        <span class="titSpan"><?php echo $survey['title'] ?></span></div>

    <div class="topNaviKec01">
        <?php $this->load->view ( 'annual_survey/top_navi' ); ?>
    </div>

    <div class="comBox">
        <p class="opBtn pb0">
            <a href="#" class="editBtn"><i class="fa fa-copy fa-lg mr5"></i>复制问卷</a><a href="<?php echo site_url('annualsurvey/edit/'.$survey['id']);?>" class="editBtn"><i class="fa fa-edit fa-lg mr5"></i>编辑问卷</a><a href="<?php echo site_url('annualsurvey/del/'.$survey['id']);?>" class="delBtn"><i class="fa  fa-trash-o fa-lg mr5"></i>删除问卷</a>
        <div class="ewmBox">
            <div class="boxl">
                <p class="blue f18"><?php echo $survey['title'] ?></p>
                <p class="f14 gray6 mb10">开始时间：<?php echo $survey['time_start'] ?> 至 <?php echo $survey['time_end'] ?></p>
                <p class="borderTop f14 gray6 pt10">问卷备注：<?php echo nl2br($survey['info']) ?></p>
            </div>
            <div class="fRight"><img src="<?php echo base_url('uploads/annualqrcode/'.$survey['qrcode'].'.png') ?>" alt="" width="160"><p class="aCenter gray9">扫一扫预览问卷</p></div>

        </div>
        <dl class="kecDl">
        </dl>

    </div>
</div>