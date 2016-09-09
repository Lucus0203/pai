<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-powerSwitch.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $(".cricalList li").powerSwitch({
            classAdd: "cur",
            animation: "fade",
            eventType: "hover",
            autoTime: 3000
        });
        $('.guideBtn,.closeBtn').click(function (e) {
            $('.noviceGuide').hide();
            $.ajax({
                type: "post",
                url: '<?php echo site_url('index/guidReaded') ?>',
                success: function (res) {
                }
            });
            return true;
        });

    });
</script>
<div class="noviceGuide">
    <div class="bg"></div>
    <div class="noviceGuideInner">
        <a href="javascript:;" class="closeBtn"><i class="fa fa-close fa-lg"></i></a>
        <div class="guideBox" id="slide_tab1">
            <div class="boxL pt20">
                <div class="guideTtl">快速创建课程</div>
                <p>加班、熬夜排课简直OUT了，1分钟创建一个测试课程，体验啥叫高效的培训管理。</p>
                <a href="<?php echo site_url('course/courselist') ?>" class="guideBtn">立即体验</a>
            </div>
            <div class="boxR"><img src="<?php echo base_url(); ?>images/guideImg.png" width="450" alt=""></div>
        </div>
        <div class="guideBox" style="display: none;" id="slide_tab2">
            <div class="boxL fRight pt20">
                <div class="guideTtl">手机通知学员</div>
                <p>EMAIL、电话、签到表、反馈表都省省了，一键发送全体学员，管他在不在工位，手机查收报名提醒、快速审批和秒速签到。</p>
                <a href="<?php echo site_url('course/courselist') ?>" class="guideBtn">立即体验</a>
            </div>
            <div class="boxR fLeft"><img src="<?php echo base_url(); ?>images/guideImg03.png" width="450" alt=""></div>
        </div>

        <div class="guideBox" style="display: none;" id="slide_tab3">
            <div class="boxL">
                <div class="guideTtl">数据一键导出</div>
                <p>EXCEL玩不转？哭晕在厕所？且看培训小白小手抖一抖，完成数据导出。</p>
                <a href="<?php echo site_url('course/courselist') ?>" class="guideBtn">立即体验</a>
            </div>
            <div class="boxR"><img src="<?php echo base_url(); ?>images/guideImg02.png" width="450" alt=""></div>
        </div>
        <ul class="cricalList">
            <li data-rel="slide_tab1" class="cur"></li>
            <li data-rel="slide_tab2"></li>
            <li data-rel="slide_tab3"></li>
        </ul>
    </div>

</div>