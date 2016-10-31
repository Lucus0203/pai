<script type="text/javascript">
    jQuery.validator.addMethod("isexistsurvey", function(value, element,params) {
        var flag = false;
        var time_start = $('#time_start').val();
        var time_end = $('#time_end').val();
        $.ajax({
            type:"POST",
            url:'<?php echo strpos(current_url(),'copy')?site_url('annualsurvey/isExistSurvey'):site_url('annualsurvey/isExistSurvey/'.$survey['id']); ?>',
            async:false,
            data:{'time_start':time_start,'time_end':time_end},
            success: function(res){
                flag = res*1>0?false:true;
            }
        });
        return flag;
    }, "此时间段存在正在调研的问卷,请更换时间段");
    $(document).ready(function () {
        $("#editForm").validate({
            rules: {
                title: {
                    required: true
                },
                time_start: {
                    required: true
                },
                time_end: {
                    required: true,
                    compareDate: "input[name=time_start]",
                    isexistsurvey:true
                }
            },
            messages: {

                title: {
                    required: "请输入问卷名称"
                },
                time_start: {
                    required: "请输入开始时间"
                },
                time_end: {
                    required: "请输入结束时间",
                    compareDate: "结束时间不能早于开始时间",
                    isexistsurvey: "此时间段存在正在调研的问卷,请更换时间段"
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
    <div class="titCom clearfix">
        <span class="titSpan"><?php if(strpos(current_url(),'copy')){echo '复制调研问卷';}else{echo empty($survey['id']) ? '创建调研问卷' : '编辑调研问卷';} ?></span>
        <a href="<?php echo site_url('annualsurvey/index') ?>" class="fRight borBlueH37">返回列表</a>
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
                        <th><span class="red">*</span>问卷名称</th>
                        <td>
                            <span class="iptInner">
                            <input name="title" value="<?php echo $survey['title'] ?>" type="text" class="iptH37 w345" placeholder="请输入问卷名称" autocomplete="off">
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>调查时间</th>
                        <td>
                            <span class="iptInner">
                            <input placeholder="开始时间" name="time_start" id="time_start" value="<?php echo empty($survey['time_start'])?'':date("Y-m-d H:i",strtotime($survey['time_start'])) ?>" type="text" class="iptH37 mr10 DTdate w156" autocomplete="off" >至<input placeholder="结束时间" name="time_end" id="time_end" value="<?php echo empty($survey['time_end'])?'':date('Y-m-d H:i',strtotime($survey['time_end'])) ?>" type="text" class="iptH37 ml10 DTdate w156" autocomplete="off" >
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th>问卷备注</th>
                        <td>
                            <span class="iptInner">
                            <textarea name="info" class="iptare pt10" placeholder="请输入问卷备注"><?php echo $survey['info'] ?></textarea>
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <span class="iptInner">
                            <input type="submit" value="<?php echo empty($survey) ? '创建问卷' : '保存问卷' ?>" class="coBtn mr30">
                            </span>
                        </td>
                    </tr>
                </table>
        </div>
    </div>
</div>