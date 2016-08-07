<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css"/>
<script type="text/javascript" src="<?php echo base_url(); ?>js/Chart.bundle.min.js"></script>
<style>
    canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }
</style>
<script>
    $(function(){
        var config = {
            type: 'radar',
            data: {
                labels: [<?php if(array_key_exists(1,$abilities)){?>"专业/技能",<?php } ?> <?php if(array_key_exists(3,$abilities)){?>"领导力",<?php } ?><?php if(array_key_exists(5,$abilities)){?>"经验",<?php } ?><?php if(array_key_exists(4,$abilities)){?>"个性",<?php } ?><?php if(array_key_exists(2,$abilities)){?>"通用",<?php } ?>],
                datasets: [{
                    label:'<?php echo $abilityjob['name'] ?>',
                    backgroundColor: "rgba(156,224,234,0.7)",
                    pointBackgroundColor: "rgba(220,220,220,1)",
                    data: [<?php if(array_key_exists(1,$chartArr)){ echo $chartArr[1]['point']/$chartArr[1]['level']*5 ?>,<?php } ?>
                        <?php if(array_key_exists(3,$chartArr)){ echo $chartArr[3]['point']/$chartArr[3]['level']*5 ?>,<?php } ?>

                        <?php if(array_key_exists(5,$chartArr)){ echo $chartArr[5]['point']/$chartArr[5]['level']*5 ?>,<?php } ?>

                        <?php if(array_key_exists(4,$chartArr)){ echo $chartArr[4]['point']/$chartArr[4]['level']*5 ?>,<?php } ?>

                        <?php if(array_key_exists(2,$chartArr)){ echo $chartArr[2]['point']/$chartArr[2]['level']*5 ?>,<?php } ?>]
                },]
            },
            options: {
                legend: {
                    //display:false
                },
                scale: {
                    gridLines: {
                        color: ['#d8d8d8']
                    },
                    ticks: {
                        beginAtZero: true
                    }
                }
            }
        };
        window.myRadar = new Chart(document.getElementById("canvas"), config);
    });
</script>
<div class="wrap">
    <div class="textureCont w960">

        <div class="texturetip clearfix"><span class="fLeft mr10"><?php echo $student['name'].'《'.$abilityjob['name'] ?>》能力评估</span>
            <div class="fRight">
                <a class="borBlueBtnH28" href="<?php echo site_url('ability/index') ?>">返回</a>
            </div>
        </div>

        <div class="nengli">
            <div class="nengliRight">
                <div style="width: 500px;height:500px; margin:40px 0 0 -100px;">
                    <canvas id="canvas"></canvas>
                </div>
            </div>
            <div class="nengliLeft">
                <?php foreach ($abilities as $key=>$abilies) {?>
                    <?php if($key==1){
                        echo '<p class="blueline"><span>专业/技能</span></p>';
                    }elseif($key==2){
                        echo '<p class="blueline"><span>通用能力</span></p>';
                    }elseif($key==3){
                        echo '<p class="blueline"><span>领导力</span></p>';
                    }elseif($key==4){
                        echo '<p class="blueline"><span>个性</span></p>';
                    }elseif($key==5){
                        echo '<p class="blueline"><span>经验</span></p>';
                    } ?>
                        <?php foreach ($abilies as $k=>$a){ ?>
                            <p class="txt"><span><?php echo ($k+1).'、'.$a['name'] ?></span><?php echo $a['info'] ?></p>

                            <div class="starBox">
                                <ul class="star">
                                    <?php for($i=1;$i<=$a['level'];$i++){?>
                                    <li <?php if($a['point']==$i){ ?>class="cur"<?php } ?>>
                                        <a href="#"><?php echo $i ?></a>
                                    </li>
                                    <?php } ?>
                                </ul>
                                <?php for($i=1;$i<=$a['level'];$i++){
                                    if($a['point']==$i){ ?>
                                <p class="starTxt"><?php echo $a['level_info'.$i] ?></p>
                                <?php }} ?>
                            </div>
                        <?php } ?>
                <?php } ?>
            </div>
        </div>

    </div>
</div>