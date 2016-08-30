<script type="text/javascript">
    $(document).ready(function(){
        $( "#editForm" ).validate( {
            rules: {
                apply_start: {
                    required: true
                },
                apply_end: {
                    required: true,
                    compareDate: "input[name=apply_start]"
                },
                apply_num: {
                    required: true
                }
            },
            messages: {
                apply_start: {
                    required: "请输入报名开始时间"
                },
                apply_end: {
                    required: "请输入报名结束时间",
                    compareDate: "结束时间不能早于开始时间"

                },
                apply_num: {
                    required: "请输入报名人数"

                }
            },
            errorPlacement: function ( error, element ) {
                error.addClass( "ui red pointing label transition" );
                error.insertAfter( element.parent() );
            },
            highlight: function ( element, errorClass, validClass ) {
                $( element ).parents( ".row" ).addClass( errorClass );
            },
            unhighlight: function (element, errorClass, validClass) {
                $( element ).parents( ".row" ).removeClass( errorClass );
            },
            submitHandler:function(form){
                $('input[type=submit]').val('请稍后..').attr('disabled','disabled');
                if($('#ispublic').val()!=1&&$('input[name=isapply_open]:checked').val()==1){
                    if(confirm('课程暂未发布,是否发布课程并开启报名')){
                        form.submit();
                    }else{
                        $('input[type=submit]').val('保存').removeAttr('disabled');
                    }
                }else{
                    form.submit();
                }
            }
        });

        $('#apply_end').focus(function(){
            $(this).val($.trim($(this).val())==''?$('#apply_start').val():$(this).val());
        });

        $('#selectSomeone').click(function(){
            $('#conWindow').show();
        });
        $('a.calBtn,div.popmap,a.closeBtn').click(function(){
            $('#conWindow').hide();
        });
        $('input[name=isapply_open]').change(function(){
            if($(this).val()==2){
                $('#notifysubmitBtn').css({'background-color':'#ccc'}).attr('disabled','disabled');
            }else{
                $('#notifysubmitBtn').css({'background-color':'#67d0de'}).removeAttr('disabled');
            }
        });
        $('#notifysubmitBtn').click(function(){
            $('#notify_check').val('1');
        });
        $('#submit').click(function(){
            $('#notify_check').val('');
        });
    });
</script>
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css" />
<div class="wrap">
        <div class="titCom clearfix"><span class="titSpan"><?php echo $course['title'] ?>  </span><span class="<?php echo $course['status_class']; ?> ml20"><?php echo $course['status_str']; ?></span></div>
        <div class="topNaviKec">
                <?php $this->load->view ( 'course/top_navi' ); ?>
        </div>
        <div class="comBox clearfix">
                <div class="baoming">

                <div class="sideLeft">
                        <ul class="sideLnavi">
<?php if($loginInfo['role']==1||$roleInfo['applyset']==1){ ?>
                                <li class="cur"><a href="<?php echo site_url('course/applyset/'.$course['id']) ?>">报名设置<i class="ml10 fa fa-angle-right fa-lg"></i></a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['applylist']==1){ ?>
                                <li><a href="<?php echo site_url('course/applylist/'.$course['id']) ?>">报名名单</a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['notifyset']==1){ ?>
                                <li ><a href="<?php echo site_url('course/notifyset/'.$course['id']) ?>">通知设置</a></li>
<?php } ?>
                        </ul>

                </div>
                <div class="contRight">
                    <?php if (!empty($msg)) {?>
                        <p class="alertBox alert-success"><span class="alert-msg"><?php echo $msg ?></span><a href="javascript:;" class="alert-remove">X</a></p>
                    <?php } ?>
                    <form id="editForm" method="post" action="">
                        <input name="act" type="hidden" value="act" />
                        <input id="ispublic" type="hidden" value="<?php echo $course['ispublic'] ?>" />
                        <table cellspacing="0" class="comTable">
                            <colgroup><col width="100">
                            </colgroup><tbody><tr>
                                <th><span class="red">*</span>开启报名</th>
                                <td>
                                    <ul class="lineUl">
                                        <li>
                                            <label><input name="isapply_open" value="1" checked="checked" type="radio">开启</label></li>
                                        <li>
                                            <label><input name="isapply_open" value="2" <?php if($course['isapply_open']==2){echo 'checked="checked"';} ?> type="radio">关闭</label></li>
                                    </ul>

                                </td>
                            </tr>
                            <tr>
                                <th><span class="red">*</span>报名时间</th>
                                <td><span class="iptInner">
                                            <input type="text" name="apply_start" id="apply_star" value="<?php echo !empty($course['apply_start'])?date("Y-m-d H:i",strtotime($course['apply_start'])):'' ?>" class="iptH37 DTdate" autocomplete="off"> 至 <input name="apply_end" id="apply_end" value="<?php echo !empty($course['apply_start'])?date("Y-m-d H:i",strtotime($course['apply_end'])):'' ?>" type="text" class="iptH37 DTdate" autocomplete="off">
                                                </span>

                                </td>
                            </tr>
                            <tr>
                                <th><span class="red">*</span>报名人数</th>
                                <td>
                                    <input type="text" name="apply_num" value="<?php echo $course['apply_num']>0?$course['apply_num']:0 ?>" class="iptH37 w157">人时，停止报名 <span class="gray9">(0表示不限人数)</span>


                                </td>
                            </tr>
                            <tr>
                                <th>其他设置</th>
                                <td>
                                    <label><input name="apply_check" value="1" <?php if($course['apply_check']==1){echo 'checked="checked"';} ?> type="checkbox" class="mr10" />报名需审核</label>
                                    <!--<ul class="lineUl">
                                                        <li>
                                                            <input name="apply_check_type" <?php if($course['apply_check_type']==1){echo 'checked="checked"';} ?> value="1" type="radio">管理员审核</li>
                                                        <li>
                                                            <input name="apply_check_type" <?php if($course['apply_check_type']==2){echo 'checked="checked"';} ?> value="2" type="radio">部门经理审核(分级管理员)</li>
                                                </ul>-->

                                </td>
                            </tr>
                            <tr>
                                <th>报名提示</th>

                                <td>
                                    <textarea name="apply_tip" class="iptare pt10"><?php echo $course['apply_tip'] ?></textarea>

                                </td>
                            </tr>
                            <tr>
                                <th></th>
                                <td>
                                    <input id="notify_check" type="hidden" name="notify_check" value="" />
                                    <input id="notifysubmitBtn" type="submit" class="coBtn mr20" <?php if($course['isapply_open']!=1){echo 'style="background-color:#ccc;color:#fff;" disabled="disabled;"';} ?> value="保存并发送通知">
                                    <input id="submit" type="submit" class="coBtn" value="仅保存">
                                </td>
                            </tr>
                        </tbody></table>
                    </form>
                </div>

                </div>

        </div>
</div>