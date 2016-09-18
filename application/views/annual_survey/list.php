<script type="text/javascript">
    $(document).ready(function(){
        $(function(){$('.shareBtn').click(function(){return confirm('确定发布吗?');});});
    });
</script>
<div class="wrap">
    <div class="comBox">
        <div class="topNavi">
                <a href="<?php echo site_url('annualsurvey/create') ?>" class="fRight borBlueH37 aCenter">创建调研问卷</a>
            <ul class="topNaviUl">
                <li <?php if(empty($parm['status'])){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('annualsurvey/index') ?>">全部调研问卷</a></li>
                <li <?php if($parm['status']==2){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('annualsurvey/index').'?status=2' ?>">待发布</a></li>
                <li <?php if($parm['status']==1){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('annualsurvey/index').'?status=1' ?>">已发布</a></li>
                <li <?php if($parm['status']==3){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('annualsurvey/index').'?status=3' ?>">已结束</a></li>
            </ul>

        </div>

        <div class="seachBox clearfix bgGray">
            <form method="get" action="">
                <ul>
                    <li class="w250 mr60">
                        <input name="keyword" type="text" value="<?php echo $parm['keyword'] ?>" class="ipt w250" placeholder="关键字">
                    </li>
                    <li class="w496 btn"><span class="mr20">开课时间</span><input name="time_start" type="text" value="<?php echo $parm['time_start'] ?>" class="ipt w156 mr10 DTdate" autocomplete="off">至
                        <input name="time_end" type="text" value="<?php echo $parm['time_end'] ?>" class="ipt w156 ml10 DTdate" autocomplete="off">
                    </li>

                    <li class="btn fRight"><input type="submit" class="borBlueH37 w100 mt3" value="搜索" /></li>
                </ul>
            </form>
        </div>
        <div class="listBox">
            <?php if(count($surveies)>0){?>
                <?php foreach ($surveies as $c){ ?>
                    <div class="listCont">
                        <p class="operaBtn">
                            <a href="<?php echo site_url('annualsurvey/edit/'.$c['id']);?>" class="editBtn"><i class="iedit"></i>编辑</a><a href="<?php echo site_url('annualsurvey/del/'.$c['id']);?>" class="delBtn"><i class="idel"></i>删除</a><?php if($c['status']==2){ ?><a href="<?php echo site_url('annualsurvey/public/'.$c['id']);?>" class="shareBtn"><i class="ishar"></i>发布</a><?php } ?></p>
                        <div class="listText">
                            <p class="titp"><a class="blue" href="<?php echo site_url('annualsurvey/info/'.$c['id']);?>"><?php echo $c['title'] ?></a></p>
                            <p class="titp">
                                <?php if($c['status']==1){ ?>
                                    <span class="greenH25">已发布</span>
                                <?php }elseif($c['status']==2){ ?>
                                    <span class="greenH25">未发布</span>
                                <?php }elseif($c['status']==3){ ?>
                                    <span class="grayH25">已结束</span>
                                <?php } ?>
                            </p>
                            <p><span class="mr30">开始时间：<?php echo date('Y-m-d H:i',strtotime($c['time_start'])) ?>&nbsp;至&nbsp;<?php echo date('Y-m-d H:i',strtotime($c['time_end'])) ?></span></p>
                            <?php if(!empty($c['info'])){ ?><p>问卷备注：<?php echo mb_strlen($c['info'],'utf-8')>30?mb_substr($c['info'],0,30,'utf-8').'……':mb_substr($c['info'],0,30,'utf-8') ?></p><?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?php }else{
                echo '<div class="listCont"><div class="listText"><p>暂无符合条件的调研问卷</p></div></div>';
            } ?>
        </div>
        <div class="pageNavi">
            <?php echo $links ?>
        </div>
    </div>
</div>