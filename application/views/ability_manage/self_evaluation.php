<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css?1128"/>
<script type="text/javascript" src="<?php echo base_url(); ?>js/Chart.bundle.min.js"></script>
<script type="text/javascript"  src="<?php echo base_url() ?>js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-scrolltofixed-min.js"></script>
<style>
    canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }
</style>
<script>
    $(function(){
        $(document).tooltip({show: { delay: 500 }});
        var config = {
            type: 'radar',
            data: {
                labels: [<?php if(array_key_exists(1,$abilities)){?>"专业/技能",<?php } ?> <?php if(array_key_exists(3,$abilities)){?>"领导力",<?php } ?><?php if(array_key_exists(5,$abilities)){?>"经验",<?php } ?><?php if(array_key_exists(4,$abilities)){?>"个性",<?php } ?><?php if(array_key_exists(2,$abilities)){?>"通用",<?php } ?>],
                datasets: [{
                    label:'<?php echo $student['name'] ?>',
                    backgroundColor: "rgba(255, 206, 73,0.5)",
                    pointBackgroundColor: "rgba(255, 206, 73,1)",
                    data: [<?php if(array_key_exists('1',$levelradar)){ echo round($levelradar[1]['point']/$levelradar[1]['level_total']*5,1); ?>,<?php } if(array_key_exists('3',$levelradar)){ echo round($levelradar[3]['point']/$levelradar[3]['level_total']*5,1); ?>,<?php } if(array_key_exists('5',$levelradar)){ echo round($levelradar[5]['point']/$levelradar[5]['level_total']*5,1); ?>,<?php } if(array_key_exists('4',$levelradar)){ echo round($levelradar[4]['point']/$levelradar[4]['level_total']*5,1); ?>,<?php } if(array_key_exists('2',$levelradar)){ echo round($levelradar[2]['point']/$levelradar[2]['level_total']*5,1); } ?>]
                },{
                    label:'<?php echo $abilityjob['name'] ?>',
                    backgroundColor: "rgba(156,224,234,0.7)",
                    pointBackgroundColor: "rgba(220,220,220,1)",
                    data: [<?php if(array_key_exists('1',$levelradar)){ echo round($levelradar[1]['level_standard']/$levelradar[1]['level_total']*5,1); ?>,<?php } if(array_key_exists('3',$levelradar)){ echo round($levelradar[3]['level_standard']/$levelradar[3]['level_total']*5,1); ?>,<?php } if(array_key_exists('5',$levelradar)){ echo round($levelradar[5]['level_standard']/$levelradar[5]['level_total']*5,1); ?>,<?php } if(array_key_exists('4',$levelradar)){ echo round($levelradar[4]['level_standard']/$levelradar[4]['level_total']*5,1); ?>,<?php } if(array_key_exists('2',$levelradar)){ echo round($levelradar[2]['level_standard']/$levelradar[2]['level_total']*5,1); } ?>]
                }]
            },
            options: {
                legend: {
                    //display:false
                    labels:{boxWidth: 20}
                },
                scale: {
                    ticks: {
                        beginAtZero: true,
                        backdropColor:'rgba(255, 255, 255, 0)'
                    }
                }
            }
        };
        window.myRadar = new Chart(document.getElementById("canvas"), config);


        $('.nengliRight,.sideLeft').scrollToFixed({
            marginTop: $('.nengli').offsetTop + 10,
            limit: function() {
                var limit = $('.footer').offset().top - $(this).outerHeight(true) - 30;
                return limit;
            },
            zIndex: 999
        });
        var sidenaviClick=false;
        $('.sideLnavi li').click(function(){
            sidenaviClick=true;
            var i=$('.sideLnavi li').index($(this));
            $(this).addClass('cur').find('i').show();
            $(this).siblings().removeClass('cur').find('i').hide();
            $('html body').stop().animate({scrollTop:$('.model').eq(i).offset().top},500,function(){
                sidenaviClick=false;
            });
            return false;
        });
        $('ul.star li').live('click',function(){
            var i=$(this).parent().find('li').index($(this));
            $(this).addClass('cur').siblings().removeClass('cur');
            var starBox=$(this).parent().parent();
            starBox.find('.starTxt').hide().eq(i).show();
            return false;
        });

        $(window).scroll(function(){
            if(!sidenaviClick){
                $('.model').each(function(i){
                    if($(this).offset().top-$(window).scrollTop()<100){
                        $('.sideLnavi li').eq(i).addClass('cur').find('i').show();
                        $('.sideLnavi li').eq(i).siblings().removeClass('cur').find('i').hide();
                    }
                });
            }
        });
    });

</script>
<div class="wrap">
    <div class="titCom clearfix">
        <span class="titSpan"><?php echo $student['name'] ?>&nbsp;<?php echo $abilityjob['name'] ?></span>
        <div class="fRight">
            <a class="borBlueH37 mr5" href="<?php echo site_url($returnevaluationlisturl) ?>" >返回列表</a>
        </div>
    </div>
    <div class="topNaviKec01">
        <?php $this->load->view ( 'ability_manage/report_top_navi' ); ?>
    </div>
    <div class="comBox">
        <div class="baoming" style="padding: 0 20px;">
            <div class="sideLeft" style="margin-top: 10px;">
                <ul class="sideLnavi">
                    <?php foreach ($types as $k=>$t){ ?>
                        <li class="<?php if($k==1){echo 'cur';} ?>" style="padding-left: 20px;"><a href="#"><?php echo $t ?><i style="margin-left: <?php if($k<3){echo '20';}else{echo $k>3?'48':'34';} ?>px;<?php if($k>1){echo 'display:none;';} ?>" class="fa fa-angle-right fa-lg"></i></a></li>
                    <?php } ?>
                </ul>
            </div>
            <div class="contRight" style="width:82%;">
                <div class="nengli" style="padding:0;">
                    <div class="nengliRight pt10" style="width: 28%;">
                        <div style="width: 425px;height:300px; margin:0 0 0 -100px;">
                            <canvas id="canvas"></canvas>
                        </div>
                    </div>
                    <div class="nengliLeft" style="padding-top: 10px;">
                        <?php foreach ($abilities as $key=>$abily) { ?>
                            <div class="model">
                                <p class="blueline"><span><?php echo $abily['type'] ?></span></p>
                                <?php foreach ($abily['abilities'] as $k=>$a){ ?>
                                    <p class="txt">
                                        <span><?php echo ($k+1)?>、<?php echo$a['name'] ?></span>
                                        <?php echo nl2br($a['info']) ?>
                                    </p>
                                    <div class="starBox mb20">
                                        <ul class="star starType<?php echo $a['type'] ?>">
                                            <?php for($i=1;$i<=$a['level'];$i++){?>
                                                <li class="<?php if($a['point']==$i){ echo 'cur yellow'; }elseif($a['level_standard']==$i){echo 'blue';} ?>" >
                                                    <a href="#"><i class="fa fa-star fa-3x"></i><span class="num"><?php echo $i ?></span></a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                        <?php for($i=1;$i<=$a['level'];$i++){?>
                                            <p class="starTxt" <?php if($i!=$a['point']){ ?>style="display: none;"<?php } ?> ><?php echo nl2br($a['level_info'.$i]) ?></p>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>