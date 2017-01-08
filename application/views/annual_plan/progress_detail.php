<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css?<?php echo $this->config->item('version');?>"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css?<?php echo $this->config->item('version');?>"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/print.css"/>
<script>
    $(document).ready(function(){
        $('.teacherInfoToggle').click(function(){
            var obj=$(this).parent().parent();
            if(obj.find('.teacherInfo').eq(0).is(':hidden')){
                obj.find('.teacherInfo').eq(0).show().next().hide();
            }else{
                obj.find('.teacherInfo').eq(0).hide().next().show();
            }
            return false;
        });
    });
</script>
<div class="wrap">
    <div class="titCom clearfix">
        <span class="titSpan"><?php echo $plan['title'] ?>执行进度 </span>
    </div>

    <div class="topNaviKec01">
        <ul class="fRight proPrint">
            <li>
                <a href="<?php echo site_url('export/planprogress/'.$plan['id']) ?>" target="_blank" class="blue"><i class="fa fa-file-excel-o fa-lg mr5"></i>导出</a>
            </li>
            <li>
                <a href="javascript:window.print();" class="blue"><i class="fa fa-print fa-lg mr5"></i>打印</a>
            </li>
        </ul>
    </div>

    <div class="clearfix textureBox">
        <div class="p15">

            <div class="clearfix">
                <p class="f24 aCenter mb20"><?php echo $plan['title'] ?>执行进度<br><span class="f14 gray9">截止至：<?php echo date("Y-m-d H:i:s"); ?></span></p>

                <div class="mb20">
                    <table class="tableC">
                        <tbody>
                        <tr>
                            <th colspan="7"  class="blueTxt">课程进度</th>
                        </tr>
                        <tr>
                            <th>时间</th>
                            <th>计划开课</th>
                            <th>调出课程</th>
                            <th>调入课程</th>
                            <th>取消课程</th>
                            <th>加开课程</th>
                            <th>实际开课</th>
                        </tr>
                        <?php $i=$plan_num_total=$actual_num_total=$change_out_num_total=$change_in_num_total=$cancel_num_total=$add_num_total=0;
                        foreach ($dataym as $k=>$d){$i++; ?>
                            <tr <?php if($i%2==0){?>class="bgGrayBlue"<?php } ?> >
                                <td><?php echo $k ?></td>
                                <td><?php echo round($d['plan_num']);$plan_num_total+=$d['plan_num']; ?></td>
                                <td><?php echo round($d['change_out_num']);$change_out_num_total+=$d['change_out_num']; ?></td>
                                <td><?php echo round($d['change_in_num']);$change_in_num_total+=$d['change_in_num']; ?></td>
                                <td><?php echo round($d['cancel_num']);$cancel_num_total+=$d['cancel_num']; ?></td>
                                <td><?php echo round($d['add_num']);$add_num_total+=$d['add_num']; ?></td>
                                <td><?php echo round($d['actual_num']);$actual_num_total+=$d['actual_num']; ?></td>
                            </tr>
                        <?php } ?>
                        <?php $i++;if(count($dataym)>1){ ?>
                            <tr <?php if($i%2==0){?>class="bgGrayBlue"<?php } ?> >
                                <td style="border-bottom: none;">总计</td>
                                <td style="border-bottom: none;"><?php echo round($plan_num_total) ?></td>
                                <td style="border-bottom: none;"><?php echo round($change_out_num_total) ?></td>
                                <td style="border-bottom: none;"><?php echo round($change_in_num_total) ?></td>
                                <td style="border-bottom: none;"><?php echo round($cancel_num_total) ?></td>
                                <td style="border-bottom: none;"><?php echo round($add_num_total) ?></td>
                                <td style="border-bottom: none;"><?php echo round($actual_num_total) ?></td>
                            </tr>
                        <?php } ?>

                        </tbody>
                    </table>
                </div>

                <div class="mb20">
                    <table class="tableC">
                        <tbody>
                        <tr>
                            <th colspan="4"  class="blueTxt">预算总览</th>
                        </tr>
                        <tr>
                            <th>时间</th>
                            <th>计划预算</th>
                            <th>实际支出</th>
                            <th>结余预算</th>
                        </tr>
                        <?php $i=$plan_price_total=$actual_price_total=$expend_total=0;
                        foreach ($dataym as $k=>$d){$i++; ?>
                            <tr <?php if($i%2==0){?>class="bgGrayBlue"<?php } ?>>
                                <td><?php echo $k ?></td>
                                <td><?php echo round($d['plan_price']);$plan_price_total+=$d['plan_price']; ?></td>
                                <td><?php echo round($d['actual_price']);$actual_price_total+=$d['actual_price']; ?></td>
                                <td><?php echo round($d['plan_price']-$d['actual_price']);$expend_total+=($d['plan_price']-$d['actual_price']); ?></td>
                            </tr>
                        <?php } ?>
                        <?php $i++;if(count($dataym)>1){ ?>
                            <tr <?php if($i%2==0){?>class="bgGrayBlue"<?php } ?>>
                                <td style="border-bottom: none;">全部</td>
                                <td style="border-bottom: none;"><?php echo round($plan_price_total) ?></td>
                                <td style="border-bottom: none;"><?php echo round($actual_price_total) ?></td>
                                <td style="border-bottom: none;"><?php echo round($expend_total) ?></td>
                            </tr>
                        <?php } ?>

                        </tbody>
                    </table>
                </div>

                <div class="mb20">
                    <table class="tableC">
                        <tbody>
                        <tr>
                            <th colspan="6"  class="blueTxt">进度详情</th>
                        </tr>
                        <tr>
                            <th>时间</th>
                            <th>课程名称</th>
                            <th>执行情况</th>
                            <th>开课时间</th>
                            <th>计划预算</th>
                            <th>实际支出</th>
                        </tr>
                        <?php $i=0;
                        $dataymIndex=0;
                        foreach ($dataym as $k=>$d){
                            ++$dataymIndex;
                            if(count($d['courses'])>0){
                                foreach ($d['courses'] as $ck=>$c){$i++; ?>
                                    <tr <?php if($i%2==0){?>class="bgGrayBlue"<?php } ?> >
                                        <?php if($ck==0){?><td rowspan="<?php echo count($d['courses']) ?>" style="background-color: #ffffff;"><?php echo $k ?></td><?php } ?>
                                        <td <?php if($ck!=0){ ?>style="border-left:1px solid #dbdbdb;" <?php } ?> >
                                            <?php $title = empty($c['plan_course_title'])?$c['title']:$c['plan_course_title'] ?>
                                            <?php if(!empty($c['cid'])){ ?><a class="blue" href="<?php echo site_url('course/courseedit/'.$c['cid']); ?>?progress=<?php echo $plan['id'] ?>"><?php echo $title ?></a>
                                            <?php }else{
                                                echo $title;
                                            } ?>
                                        </td>
                                        <td><?php
                                            if(empty($c['pc_id'])||$c['annual_plan_id']!=$plan['id']){
                                                echo '加课';
                                            }elseif(empty($c['course_id']) || $c['isdel']==1 || $c['ispublic']==2){
                                                echo count($dataym)==$dataymIndex?'未开':'取消';
                                            }elseif($c['annual_plan_id']==$plan['id']&&($c['plan_year'].'.'.$c['plan_month']!=$k)){
                                                echo '调入';
                                            }elseif($c['isdel']==2&&$c['ispublic']==1&&(date("Y.m",strtotime($c['time_start']))!=$k)){
                                                echo '调出';
                                            }else{
                                                echo '开课';
                                            }
                                            ?></td>
                                        <td><?php echo (!empty($c['time_start']))?date("m.d H:i",strtotime($c['time_start'])):''; ?></td>
                                        <td><?php echo round($c['price']); ?></td>
                                        <td><?php echo round($c['expend']); ?></td>
                                    </tr>
                                <?php }
                            }else{$i++; ?>
                                <tr <?php if($i%2==0){?>class="bgGrayBlue"<?php } ?> >
                                    <td style="background-color: #ffffff;"><?php echo $k; ?></td>
                                    <td colspan="5">暂无课程记录</td>
                                </tr>
                            <?php }
                        }?>
                        </tbody>
                    </table>
                </div>


            </div>

        </div>
    </div>
</div>