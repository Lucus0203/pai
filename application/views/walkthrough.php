<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/jquery.pagewalkthrough.css"/>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.pagewalkthrough-1.1.0.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

        //$('#walkthrough').pagewalkthrough({
        $('body').pagewalkthrough({

            steps: [
                {
                    wrapper: '#naviCourse',
                    margin: '0',
                    popup: {
                        content: '#navi-course',
                        type: 'tooltip',
                        position: 'bottom',
                        offsetHorizontal: 0,
                        offsetVertical: 0,
                        width: '800'
                    }
                }

            ],
            name: 'Walkthrough',
            onLoad: true

        });


        /***
         * NAVIGATION
         */

        $('.prev-step').live('click', function (e) {
            $.pagewalkthrough('prev', e);
            return false;
        });

        $('#navi-department,#navi-teacher,.next-step').live('click', function (e) {
            $.pagewalkthrough('next', e);
            return false;
        });

        $('#navi-course,.close-step').live('click', function (e) {
            $.pagewalkthrough('close');
            $.ajax({
                type: "post",
                url: '<?php echo site_url('index/guidReaded') ?>',
                success: function (res) {
                }
            });
            return false;
        });


    });
</script>
<!-- walkthrough -->
<div id="walkthrough">
<!--    <div id="navi-department" style="display:none;">-->
<!--        <p class="tooltipTitle">第一步：创建学员</p>-->
<!--        <p><img src="--><?php //echo base_url();?><!--images/walkthrough01.png" width="80%" /></p>-->
<!--        <p style="text-align: left;padding: 0 25px;">为您的课程创建第一个学员.</p>-->
<!--        <br>-->
<!--        <a href="javascript:;" class="next-step" style="float:right;">下一步</a>-->
<!--    </div>-->
<!--    <div id="navi-teacher" style="display:none;">-->
<!--        <p class="tooltipTitle">第二步：创建讲师</p>-->
<!--        <p><img src="--><?php //echo base_url();?><!--images/walkthrough02.png" width="80%" /></p>-->
<!--        <p style="text-align: left;padding: 0 25px;">谁来上课呢?</p>-->
<!--        <br>-->
<!--        <a href="javascript:;" class="prev-step" style="float:left;">上一步</a>-->
<!--        <a href="javascript:;" class="next-step" style="float:right;">下一步</a>-->
<!--    </div>-->
    <div id="navi-course" style="display:none;">
        <p class="tooltipTitle">您的高效培训管理<br>从创建第一个课程开始</p>
        <p><img src="<?php echo base_url();?>images/walkthrough03.png" width="80%" /></p>
        <a href="javascript:;" class="close-step" style="float:right;">清楚了</a>
    </div>
</div>