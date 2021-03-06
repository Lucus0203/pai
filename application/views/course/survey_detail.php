<!DOCTYPE html>
<html>

    <head>
            <meta charset="UTF-8">
            <title></title>
            <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/common.css?<?php echo $this->config->item('version');?>" />
            <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css?<?php echo $this->config->item('version');?>" />
            <script type="text/javascript" src="<?php echo base_url();?>js/jquery1.83.js"></script>
    </head>

    <body class="nobg">
    <div class="headerCom">
        <div class="inner">
            <div class="log">
                <a href="javascript:void(0)"><img src="<?php echo base_url();?>images/logo01.png" alt="培训派"></a>
            </div>
        </div>
    </div>
    <div class="wrap">
        <p class="f24 aCenter">课前调研详情</p>
        <p class="askbox">调研人:<span class="fbold">&nbsp;<?php echo $student['name'] ?>&nbsp;</span>
            <?php echo $student['job_name'].'/'.$student['department'].'/'.$student['mobile']; ?><span class="ml20">提交时间：<?php echo date("Y.m.d H:i:s",strtotime($survey[0]['created'])) ?></span></p>


        <div class="comBox p40">

            <dl class="askDl">
                <?php foreach ($survey as $no=>$h){ ?>
                    <dt>问题<?php echo ($no*1+1) ?>：<?php echo $h['title'] ?></dt>
                    <dd><?php echo nl2br($h['content']) ?></dd>
                <?php } ?>
            </dl>

        </div>
    </div>
    <div class="footer">
        <p><a href="http://www.trainingpie.com/about_us.html" target="_blank">关于培训派</a></p>
        <p>Copyright &copy;2016 peixunpai.com All Rights Reserved</p>
    </div>
    </body>

</html>