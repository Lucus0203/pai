<script type="text/javascript">
        $(document).ready(function(){
                $( "#editForm" ).validate( {
                        rules: {
                                signin_start: {
                                        required: true
                                },
                                signin_end: {
                                        required: true
                                }
                        },
                        messages: {
                                signin_start: {
                                        required: "请输入签到开始时间"
                                },
                                signin_end: {
                                        required: "请输入签到结束时间"

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
                
                $('.Wdate').eq(1).focus(function(){
                    if($('.Wdate').eq(1).val()==''){
                        $('.Wdate').eq(1).val($('.Wdate').eq(0).val());
                    }
                });
                $('.Wdate').eq(3).focus(function(){
                    if($('.Wdate').eq(3).val()==''){
                        $('.Wdate').eq(3).val($('.Wdate').eq(2).val());
                    }
                });
        });
</script>
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css" />
<div class="wrap">
        <div class="titCom clearfix"><span class="titSpan"><?php echo $course['title'] ?>  </span><a href="javascript:void(0)" class="<?php echo $course['status_class']; ?>"><?php echo $course['status_str']; ?></a></div>
        <div class="topNaviKec">
                <?php $this->load->view ( 'course/top_navi' ); ?>

        </div>
        <div class="comBox clearfix">
                <div class="baoming">

                        <div class="sideLeft">
                                <ul class="sideLnavi">
<?php if($loginInfo['role']==1||$roleInfo['signinset']==1){ ?>
                                        <li class="cur"><a href="<?php echo site_url('course/signinset/'.$course['id']) ?>">签到设置<i></i></a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['signinlist']==1){ ?>
                                        <li><a href="<?php echo site_url('course/signinlist/'.$course['id']) ?>">签到名单<i></i></a></li>
<?php } ?>
                                </ul>

                        </div>
                        <div class="contRight">
                        <form id="editForm" method="post" action="">
                            <input name="act" type="hidden" value="act" />
                                <table cellspacing="0" class="comTable">
                                        <colgroup>
                                                <col width="100">
                                        </colgroup>
                                        <tbody>
                                                <tr>
                                                        <th><span class="red">*</span>开启签到</th>
                                                        <td>
                                                                <ul class="lineUl">
                                                                        <li>
                                                                            <input name="issignin_open" checked="" value="1" type="radio">开启</li>
                                                                        <li>
                                                                            <input name="issignin_open" <?php if($course['issignin_open']==2){echo 'checked="checked"';} ?> value="2" type="radio">关闭</li>
                                                                </ul>

                                                        </td>
                                                </tr>
                                                <tr>
                                                        <th><span class="red">*</span>签到时段</th>
                                                        <td>
                                                            <input type="text" name="signin_start" value="<?php echo $course['signin_start'] ?>" class="iptH37 Wdate" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" autocomplete="off"> 至 <input name="signin_end" value="<?php echo $course['signin_end'] ?>" type="text" class="iptH37 Wdate" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" autocomplete="off">

                                                        </td>
                                                </tr>
                                                <tr>
                                                        <th>签到二维码</th>
                                                        <td><a href="<?php echo site_url('course/downloadqrcode/'.$course['id']) ?>?type=signin" target="_blank" ><img src="<?php echo base_url().'uploads/course_qrcode/'.$course['signin_qrcode'].'.png' ?>" height="120" /><p class="aCenter" style="width:120px">下载</p></a>
                                                        </td>
                                                </tr>
                                                <tr><td colspan="2"><p class="red">签退无需求可不设置</p></td></tr>
                                                <tr>
                                                        <th>签退时段</th>
                                                        <td>
                                                            <input type="text" name="signout_start" value="<?php echo $course['signout_start'] ?>" class="iptH37 Wdate" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" autocomplete="off"> 至 <input name="signout_end" value="<?php echo $course['signout_end'] ?>" type="text" class="iptH37 Wdate" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" autocomplete="off">
                                                            

                                                        </td>
                                                </tr>
                                                <tr>
                                                        <th>签退二维码</th>
                                                        <td><a href="<?php echo site_url('course/downloadqrcode/'.$course['id']) ?>?type=signout" target="_blank" ><img src="<?php echo base_url().'uploads/course_qrcode/'.$course['signout_qrcode'].'.png' ?>" height="120" /><p class="aCenter" style="width:120px">下载</p></a>
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <th></th>
                                                        <td>
                                                                <input type="submit" class="coBtn" value="保存">
                                                        </td>
                                                </tr>
                                        </tbody>
                                </table>
                            </form>
                        </div>

                </div>

        </div>
</div>