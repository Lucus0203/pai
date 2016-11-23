<script type="text/javascript">
    $(document).ready(function(){
        $('.logTabUl li').click(function() {
                var i = $(this).index();
                var lf = parseInt($(this).offset().left - $('.logTabUl').offset().left);
                var w = parseInt($(this).css('width'));
                $('.tabLine').animate({
                        'left': lf,
                        'width': w
                });
                $('.noteBox').hide().eq(i).show();
        })
    })
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
<?php if($loginInfo['role']==1||$roleInfo['notifyset']==1){ ?>
                                    <li><a href="<?php echo site_url('course/notifyset/'.$course['id']) ?>">通知设置<i></i></a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['notifycustomize']==1){ ?>
                                    <li style="display:none;" class="cur"><a href="<?php echo site_url('course/notifycustomize/'.$course['id']) ?>">自定义发送<i></i></a></li>
<?php } ?>
                            </ul>

                    </div>
                    <div class="contRight">
                        <div class="logTab">
                                <ul class="logTabUl">
                                        <li>短信通知</li>
                                        <li>邮件通知</li>
                                </ul>
                                <p class="tabLine" style="left: 0px; width: 82px;"></p>
                        </div>
                        <div class="noteBox">
                                <div class="grayTipBox">
                                        <p>您剩余可发短信0条<br>提示：为了保障短信正常送达，请注意用词勿发送营销类信息</p>
                                </div>
                                <table cellspacing="0" class="comTable">
                                        <colgroup>
                                                <col width="100">
                                        </colgroup>
                                        <tbody>
                                                <tr>
                                                        <th>发送学员</th>
                                                        <td>
                                                                <input type="text" class="iptH37 w250"><a class="borBlueH37 ml20" href="javascript:void(0)"><i class="addQuan"></i>选择学员</a>

                                                        </td>
                                                </tr>
                                                <tr>
                                                        <th>发送内容</th>
                                                        <td>
                                                                <textarea class="iptare"></textarea>
                                                                <p class="gray9 mt10">已输入0/72个字</p>
                                                        </td>
                                                </tr>

                                                <tr>
                                                        <th></th>
                                                        <td>
                                                                <input type="submit" class="coBtn" value="保存设置">
                                                        </td>
                                                </tr>
                                        </tbody>
                                </table>
                        </div>

                        <div class="noteBox" style="display: none;">
                                <table cellspacing="0" class="comTable">
                                        <colgroup>
                                                <col width="100">
                                        </colgroup>
                                        <tbody>
                                                <tr>
                                                        <th>收件人邮箱</th>
                                                        <td>
                                                                <input type="text" class="iptH37 w250">

                                                        </td>
                                                </tr>
                                                <tr>
                                                        <th>发送内容</th>
                                                        <td>
                                                                <textarea class="iptare"></textarea>
                                                                <p class="gray9 mt10">已输入0/72个字</p>
                                                        </td>
                                                </tr>

                                                <tr>
                                                        <th></th>
                                                        <td>
                                                                <input type="submit" class="coBtn" value="保存设置">
                                                        </td>
                                                </tr>
                                        </tbody>
                                </table>
                        </div>
                </div>
        </div>

</div>
</div>