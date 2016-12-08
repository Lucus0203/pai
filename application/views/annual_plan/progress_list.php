<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css?1228"/>
<script type="text/javascript">
    $(document).ready(function () {
        $('.traceBtn').click(function(){
            return confirm('确定隐藏追踪吗?')
        });
    });
</script>
<div class="wrap">
    <div class="textureCont width100">
        <div class="texturetip p1524 clearfix">
            <div class="fLeft"><span class="pt5">计划进度</span>
                <?php if(!$isAccessAccount){ ?><p class="clearfix gray9">您正免费体验该功能,有5个体验名额,如需开通请联系<a class="blue" href="tel:021-61723727">021-61723727</a>,辛老师</p><?php } ?>
            </div>
        </div>
        <div class="listBox">
            <?php if(count($plans)>0){?>
                <?php foreach ($plans as $p){ ?>
                    <div class="listCont">
                        <p class="operaBtn">
                            <a class="traceBtn" href="<?php echo site_url('annualplan/progressuntrace/'.$p['id']);?>"><i class="fa fa-eye-slash fa-lg"></i>隐藏追踪</a></p>
                        <div class="listText">
                            <p class="titp">
                                <a href="<?php echo site_url('annualplan/progressdetail/'.$p['id']);?>" class="blue"><?php echo $p['title'] ?></a>
                            </p>
                            <?php if( $p['start']=='.' && $p['end']=='.' ){ ?>
                                <p>该计划暂无课程安排</p>
                            <?php }else{ ?>
                                <p>课程进度：<?php echo $p['progress_course'].'%' ?></p>
                                <p>预算进度：<?php echo $p['progress_price'].'%' ?></p>
                                <p>执行时间：<?php echo $p['start'].' 至 '.$p['end']; ?> </p>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?php }else{
                echo '<div class="listCont"><div class="listText"><p>暂无年度培训计划</p></div></div>';
            } ?>
        </div>
        <div class="pageNavi">
            <?php echo $links ?>
        </div>
    </div>
</div>