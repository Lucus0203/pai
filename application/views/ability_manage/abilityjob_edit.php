<script type="text/javascript">
    jQuery.validator.addMethod("isexistedname", function(value, element,params) {
        var flag = false;
        var name = $('input[name=name]').val();
        $.ajax({
            type:"POST",
            url:'<?php echo site_url('abilitymanage/isexistedname/'.$abilityjob['id']); ?>',
            async:false,
            data:{'name':name},
            success: function(res){
                flag = res*1>0?false:true;
            }
        });
        return flag;
    }, "此名称已被使用");
    $(document).ready(function () {
        $("#editForm").validate({
            rules: {
                name: {
                    required: true,
                    isexistedname: "此名称已被使用"
                }
            },
            messages: {
                name: {
                    required: "请输入岗位名称"
                }
            },
            errorPlacement: function (error, element) {
                error.addClass("ui red pointing label transition");
                error.insertAfter(element.parent());
            },
            highlight: function (element, errorClass, validClass) {
                $(element).parents(".row").addClass(errorClass);
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents(".row").removeClass(errorClass);
            },
            submitHandler: function (form) {
                $('input[type=submit]').val('请稍后..').attr('disabled', 'disabled');
                form.submit();
            }
        });
    });
</script>
<div class="wrap">
    <div class="titCom clearfix"><span class="titSpan"><?php if(strpos(current_url(),'copyabilityjob')){echo '复制能力模型';}else{echo empty($abilityjob) ? '新增岗位模型' : '编辑岗位模型';} ?></span></div>
    <div class="comBox">
        <div class="tableBox">
            <form id="editForm" method="post" action="" enctype="multipart/form-data">
                <input name="act" type="hidden" value="act"/>
                <table cellspacing="0" class="comTable">
                    <col width="20%"/>
                    <tr>
                        <th>模型名称</th>
                        <td>
                            <span class="iptInner">
                            <input name="name" value="<?php echo $abilityjob['name'] ?>" type="text" class="iptH37 w156" placeholder="请输入模型名称">
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th>岗位系列</th>
                        <td>
                            &nbsp;<?php echo $series['name'] ?>
                        </td>
                    </tr>
                    <tr>
                        <th>职级</th>
                        <td>
                            <select name="ability_job_level_id" class="iptH37 w156">
                                <?php if(count($prolevels)>0){ ?>
                                <optgroup label="专业系">
                                <?php foreach ($prolevels as $level){ ?>
                                    <option <?php if($abilityjob['ability_job_level_id']==$level['id']) echo 'selected'; ?> value="<?php echo $level['id'] ?>"><?php echo $level['name'] ?></option>
                                <?php } ?>
                                </optgroup>
                                <?php } ?>
                                <?php if(count($maglevels)>0){ ?>
                                    <optgroup label="管理系">
                                        <?php foreach ($maglevels as $level){ ?>
                                            <option <?php if($abilityjob['ability_job_level_id']==$level['id']) echo 'selected'; ?> value="<?php echo $level['id'] ?>"><?php echo $level['name'] ?></option>
                                        <?php } ?>
                                    </optgroup>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>领导力</th>
                        <td>
                            <ul class="lineUl">
                                <li><label><input name="hasleadership" value="1" type="radio" checked="checked" >有</label></li>
                                <li><label><input name="hasleadership" value="2" type="radio" <?php if($abilityjob['hasleadership']==2){?>checked="checked"<?php } ?>>无</label></li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <span class="iptInner">
                                <input type="submit" value="保存" class="coBtn mr30">
                                <input type="button" value="返回" class="coBtn" onclick="history.back(-1);">
                            </span>
                        </td>
                    </tr>
                </table>
        </div>

    </div>
</div>
