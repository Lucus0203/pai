<div class="wrap">
    <div class="titCom clearfix"><span class="titSpan">年度培训计划</span><a href="<?php echo site_url('annualplan/create') ?>" class="fRight borBlueH37 aCenter">创建培训计划</a></div>
    <div class="comBox">
        <div class="listBox">
            <?php if(count($plans)>0){?>
                <?php foreach ($plans as $p){ ?>
                    <div class="listCont">
                        <p class="operaBtn">
                            <a href="<?php echo site_url('annualplan/edit/'.$p['id']);?>" class="editBtn"><i class="fa fa-edit fa-lg mr5"></i>编辑</a><a href="<?php echo site_url('annualplan/del/'.$p['id']);?>" class="delBtn"><i class="fa fa-trash-o fa-lg mr5"></i>删除</a></p>
                        <div class="listText">
                            <p class="titp">
                                <a href="<?php echo site_url('annualplan/course/'.$p['id']);?>" class="blue"><?php echo $p['title'] ?></a>
                            </p>
                            <p>调研问卷：<span class="blue"><?php echo $p['survey_title']; ?></span></p>
                            <p>创建时间：<?php echo date("Y-m-d H:i",strtotime($p['created'])); ?> </p>
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