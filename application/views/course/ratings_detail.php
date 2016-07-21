<!DOCTYPE html>
<html>

    <head>
            <meta charset="UTF-8">
            <title></title>
            <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/common.css" />
            <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css" />
            <script type="text/javascript" src="<?php echo base_url();?>js/jquery1.83.js"></script>
    </head>

    <body class="nobg">




    <div class="headerCom">
                    <div class="inner">
                            <div class="log">
                                    <a href="javascript:void(0);"><img src="<?php echo base_url();?>images/logo01.png" alt="培训 派"></a><span class="logoTip">课程反馈详情</span>
                            </div>


                    </div>
            </div>
            <div class="wrap">
                <p class="askbox">答题人:<span class="fbold">&nbsp;<?php echo $student['name'] ?>&nbsp;</span>
                    <?php echo $student['job_name'].'/'.$student['department'].'/'.$student['mobile']; ?><span class="ml20">提交时间：<?php echo date("Y.m.d H:i:s",strtotime($ratings[0]['created'])) ?></span></p>
                    <div class="comBox p40">

                            <dl class="askDl">
                                    <?php foreach ($ratings as $h){ ?>
                                <dt><?php echo ($h['type']==1)?'评分题':'开放题' ?>&nbsp;<?php echo $h['title'] ?></dt>
                                    <dd>
                                        <?php if($h['type']==1){ ?>
                                        <span class="startBox"><i style="width: <?php echo $h['star']/5*100 ?>%;"></i></span><?php echo $h['star'].'分' ?>
                                        <?php }elseif($h['type']==2){
                                            echo nl2br($h['content']); 
                                        }?> 
                                    </dd>
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