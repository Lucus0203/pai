<script type="text/javascript">
        $(document).ready(function(){
                $( "#editForm" ).validate( {
                        rules: {
                                apply_start: {
                                        required: true
                                },
                                apply_end: {
                                        required: true
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
                                        required: "请输入报名结束时间"

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
                        }
                });
                
                $('#selectSomeone').click(function(){
                    $('#conWindow').show();
                });
                $('a.calBtn,div.popmap,a.closeBtn').click(function(){
                    $('#conWindow').hide();
                });
                $('.Wdate').eq(1).focus(function(){
                    if($('.Wdate').eq(1).val()==''){
                        $('.Wdate').eq(1).val($('.Wdate').eq(0).val());
                    }
                });
        });
</script>
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css" />
<div class="wrap">
        <div class="titCom clearfix"><span class="titSpan"><?php echo $course['title'] ?>  </span><a href="javascript:void(0);" class="<?php echo $course['status_class']; ?>"><?php echo $course['status_str']; ?></a></div>
        <div class="topNaviKec">
                <?php $this->load->view ( 'course/top_navi' ); ?>
        </div>
        <div class="comBox clearfix">
                <div class="baoming">

                <div class="sideLeft">
                        <ul class="sideLnavi">
<?php if($loginInfo['role']==1||$roleInfo['applyset']==1){ ?>
                                <li class="cur"><a href="<?php echo site_url('course/applyset/'.$course['id']) ?>">报名设置<i></i></a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['applylist']==1){ ?>
                                <li><a href="<?php echo site_url('course/applylist/'.$course['id']) ?>">报名名单<i></i></a></li>
<?php } ?>
                        </ul>

                </div>
                <div class="contRight">
                    <form id="editForm" method="post" action="">
                        <input name="act" type="hidden" value="act" />
                        <table cellspacing="0" class="comTable">
                                <colgroup><col width="100">
                                </colgroup><tbody><tr>
                                        <th><span class="red">*</span>开启报名</th>
                                        <td>
                                                <ul class="lineUl">
                                                        <li>
                                                            <input name="isapply_open" value="1" checked="checked" type="radio">开启</li>
                                                        <li>
                                                            <input name="isapply_open" value="2" <?php if($course['isapply_open']==2){echo 'checked="checked"';} ?> type="radio">关闭</li>
                                                </ul>

                                        </td>
                                </tr>
                                <tr>
                                        <th><span class="red">*</span>报名时间</th>
                                        <td>
                                            <input type="text" name="apply_start" value="<?php echo $course['apply_start'] ?>" class="iptH37 Wdate" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" autocomplete="off"> 至 <input name="apply_end" value="<?php echo $course['apply_end'] ?>" type="text" class="iptH37 Wdate" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" autocomplete="off">

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
                                                <ul class="lineUl">
                                                        <li>
                                                            <input name="apply_check_type" <?php if($course['apply_check_type']==1){echo 'checked="checked"';} ?> value="1" type="radio">管理员审核</li>
                                                        <li>
                                                            <input name="apply_check_type" <?php if($course['apply_check_type']==2){echo 'checked="checked"';} ?> value="2" type="radio">部门经理审核(分级管理员)</li>
                                                </ul>

                                        </td>
                                </tr>
                                <tr>
                                        <th>报名提示</th>

                                        <td>
                                            <textarea name="apply_tip" class="iptare"><?php echo $course['apply_tip'] ?></textarea>

                                        </td>
                                </tr>
                                <tr>
                                        <th></th>
                                        <td>
                                                <input type="submit" class="coBtn" value="保存">
                                        </td>
                                </tr>
                        </tbody></table>
                    </form>
                </div>

                </div>

        </div>
</div>