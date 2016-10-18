<div class="wrap">
    <div class="titCom clearfix"><span class="titSpan">年度培训计划</span><a href="<?php echo site_url('annualplan/create') ?>" class="fRight borBlueH37 aCenter">创建培训计划</a></div>
    <div class="comBox">
        <div class="listBox">
            <?php if(count($surveies)>0){?>
                <?php foreach ($surveies as $c){ ?>
                    <div class="listCont">
                        <p class="operaBtn">
                            <a href="<?php echo site_url('annualsurvey/edit/'.$c['id']);?>" class="editBtn"><i class="fa fa-edit fa-lg mr5"></i>编辑</a><a href="<?php echo site_url('annualsurvey/del/'.$c['id']);?>" class="delBtn"><i class="fa fa-trash-o fa-lg mr5"></i>删除</a><a href="<?php echo site_url('annualsurvey/copy/'.$c['id']);?>" class="shareBtn"><i class="fa fa-copy fa-lg mr5"></i>复制</a></p>
                        <div class="listText">
                            <p class="titp"><a class="blue" href="<?php echo site_url('annualsurvey/info/'.$c['id']);?>"><?php echo $c['title'] ?></a></p>
                            <p class="titp">
                                <?php if($c['status']==1){ ?>
                                    <span class="greenH25">进行中</span>
                                <?php }elseif($c['status']==2){ ?>
                                    <span class="orangeH25">未开始</span>
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
                echo '<div class="listCont"><div class="listText"><p>暂无年度培训计划</p></div></div>';
            } ?>
        </div>
        <div class="pageNavi">
            <?php echo $links ?>
        </div>
    </div>
</div>