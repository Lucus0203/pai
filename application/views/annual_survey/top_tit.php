<span class="titSpan"><?php echo $survey['title'] ?></span>
<?php if(strtotime($survey['time_start'])<time()&&time()<strtotime($survey['time_end'])){ ?>
    <span class="greenH25 ml20">进行中</span>
<?php }elseif(time()<strtotime($survey['time_start'])){ ?>
    <span class="orangeH25 ml20">未开始</span>
<?php }elseif(time()>strtotime($survey['time_end'])){ ?>
    <span class="grayH25 ml20">已结束</span>
<?php } ?>
<?php if(time()>strtotime($survey['time_end'])){ ?>
    <a href="<?php echo site_url('annualplan/create/'.$survey['id']) ?>" class="fRight borBlueH37 aCenter">生成年度计划</a>
<?php }else{ ?>
    <a href="javascript:alert('需求调研未结束,无法生成年度计划');" class="fRight borBlueH37 aCenter" style="border: none;background-color: #ccc; color:#fff;">生成年度计划</a>
<?php } ?>