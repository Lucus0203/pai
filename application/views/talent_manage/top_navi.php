<ul class="topNaviUlKec">
    <li <?php if(strpos(current_url(),'talentmanage/courserecords')){ echo 'class="cur"'; } ?>>
        <a href="<?php echo site_url('talentmanage/courserecords/'.$student['id']) ?>">课程记录</a>
    </li>
    <li <?php if(strpos(current_url(),'talentmanage/evaluaterecords')){ echo 'class="cur"'; } ?>>
        <a href="<?php echo site_url('talentmanage/evaluaterecords/'.$student['id']) ?>">评估记录</a>
    </li>
    <li <?php if(strpos(current_url(),'talentmanage/annualrecords')){ echo 'class="cur"'; } ?>>
        <a href="<?php echo site_url('talentmanage/annualrecords/'.$student['id']) ?>">年度调研</a>
    </li>
</ul>