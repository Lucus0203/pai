<div class="wrap">
    <div class="titCom clearfix">
        <span class="titSpan"><?php echo empty($survey['id']) ? '创建培训计划' : '编辑培训计划'; ?></span>
        <a href="<?php echo site_url('annualplan/index') ?>" class="fRight borBlueH37 aCenter">返回列表</a>
    </div>
    <div class="comBox">
        <?php if (!empty($msg)) {?>
            <p class="alertBox alert-success"><span class="alert-msg"><?php echo $msg ?></span><a href="javascript:;" class="alert-remove">X</a></p>
        <?php } ?>
        <?php if (!empty($errmsg)) {?>
            <p class="alertBox alert-danger"><span class="alert-msg"><?php echo $errmsg ?></span><a href="javascript:;" class="alert-remove">X</a></p>
        <?php } ?>
        <div class="tableBox">
            <form id="editForm" method="post" action="">
                <input name="act" type="hidden" value="act"/>
                <table cellspacing="0" class="comTable">
                    <col width="20%"/>
                    <tr>
                        <th><span class="red">*</span>计划名称</th>
                        <td>
                            <span class="iptInner">
                            <input name="title" value="<?php echo $plan['title'] ?>" type="text" class="iptH37 w250" placeholder="请输入问卷名称" autocomplete="off">
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th>调查问卷</th>
                        <td>
                            <span class="iptInner"><select name="annual_survey_id" class="iptH37 w250"></select><span class="f14 gray9">（请选择相应的调研问卷）</span></span>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <span class="iptInner">
                            <input type="submit" value="保存" class="coBtn mr30">
                            </span>
                        </td>
                    </tr>
                </table>
        </div>
    </div>
</div>