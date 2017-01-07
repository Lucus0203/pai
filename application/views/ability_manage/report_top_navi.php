<ul class="topNaviUlKec">
    <li class="<?php if(strpos(current_url(),'abilitymanage/reportevaluation/'.$evaluation['id'].'/'.$student['id'])){?>cur<?php } ?>">
        <a href="<?php echo site_url('abilitymanage/reportevaluation/'.$evaluation['id'].'/'.$student['id']);?>">评估报告</a>
    </li>
    <li class="<?php if(strpos(current_url(),'abilitymanage/selfevaluation/'.$evaluation['id'].'/'.$student['id'])){?>cur<?php } ?>">
        <?php if($evaluation_student['status']==2){?>
            <a href="<?php echo site_url('abilitymanage/selfevaluation/'.$evaluation['id'].'/'.$student['id']);?>">自评结果</a>
        <?php }else{ ?>
            <a style="color:#cccccc;cursor: no-drop;" href="javascript:;">自评结果</a>
        <?php } ?>
    </li>
    <li class="<?php if(strpos(current_url(),'abilitymanage/othersevaluation/'.$evaluation['id'].'/'.$student['id'])){?>cur<?php } ?>">
        <?php if($evaluation_student['others_status']==2){?>
            <a href="<?php echo site_url('abilitymanage/othersevaluation/'.$evaluation['id'].'/'.$student['id']);?>">他评结果</a>
        <?php }else{ ?>
            <a style="color:#cccccc;cursor: no-drop;" href="javascript:;">他评结果</a>
        <?php } ?>
    </li>
</ul>