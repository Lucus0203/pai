<ul class="topNaviUlKec">
    <li <?php if(strpos(current_url(),'detailabilityjob')){ echo 'class="cur"'; } ?>>
        <a href="<?php echo site_url('abilitymanage/detailabilityjob/'.$abilityjob['id']) ?>">岗位模型</a>
    </li>
    <li <?php if(strpos(current_url(),'abilityjobrecords')||strpos(current_url(),'evaluationlist')){ echo 'class="cur"'; } ?>>
        <a href="<?php echo site_url('abilitymanage/abilityjobrecords/'.$abilityjob['id']) ?>">评估记录</a>
    </li>
</ul>