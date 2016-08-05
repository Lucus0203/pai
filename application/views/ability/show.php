<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css"/>
<script type="text/javascript" src="<?php echo base_url(); ?>js/Chart.bundle.js"></script>
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
                labels: [<?php if(array_key_exists(1,$abilities)){?>"专业/技能",<?php } ?> <?php if(array_key_exists(3,$abilities)){?>"领导力",<?php } ?><?php if(array_key_exists(5,$abilities)){?>"领导力",<?php } ?><?php if(array_key_exists(4,$abilities)){?>"个性",<?php } ?><?php if(array_key_exists(2,$abilities)){?>"通用",<?php } ?>],
                datasets: [{
                    label:'能力图',
                    backgroundColor: "rgba(156,224,234,0.7)",
                    pointBackgroundColor: "rgba(220,220,220,1)",
                    data: [10, 10, 10, 10, 10]
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

        $('ul.star li').click(function(){
            var i=$(this).parent().find('li').index($(this));
            $(this).addClass('cur').siblings().removeClass('cur');
            $(this).parent().parent().find('.starTxt').hide().eq(i).show();
            return false;
        });
    });
</script>
<div class="wrap">
    <div class="textureCont w960">

        <div class="texturetip clearfix"><span class="fLeft mr10">采购专员</span>
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
                                    <li <?php if($i==1){ ?>class="cur"<?php } ?>>
                                        <a href="#"><?php echo $i ?></a>
                                    </li>
                                    <?php } ?>
                                </ul>
                                <?php for($i=1;$i<=$a['level'];$i++){?>
                                <p class="starTxt" <?php if($i>1){ ?>style="display: none;"<?php } ?> ><?php echo $a['level_info'.$i] ?></p>
                                <?php } ?>
                            </div>
                        <?php } ?>
                <?php } ?>
            </div>
        </div>

    </div>
</div>