<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css"/>
<script type="text/javascript">
    $(document).ready(function(){
        $('#open_status').change(function(){
            var url='<?php echo site_url('annualplan/course/'.$plan['id'])?>';
            window.location=url+'?typeid='+$('#typeid').val()+'&openstatus='+$('#open_status').val();
        });
        $('#typeid').change(function(){
            var url='<?php echo site_url('annualplan/course/'.$plan['id'])?>';
            window.location=url+'?typeid='+$('#typeid').val()+'&openstatus='+$('#open_status').val();
        });
        $('a.cancel').click(function(){
            return confirm('确定取消开课?');
        });
    });
</script>
<div class="wrap">
    <div class="titCom clearfix">
        <span class="titSpan"><?php echo $plan['title'] ?></span>
    </div>

    <div class="topNaviKec01">
        <?php $this->load->view ( 'annual_plan/top_navi' ); ?>
        <ul class="fRight proPrint">
            <li>
                <a href="#" class="borBlueH37 aCenter">导出到课程管理</a><!--?已开设的课程将被添加到课程管理中-->
            </li>
        </ul>
    </div>

    <div class="clearfix textureBox">
        <div class="p15">

            <div class="clearfix">
                <p class="clearfix f14 mb20">共有<?php echo $total ?>个课程,其中<?php echo $total_open ?>个开课
                    <select id="open_status" class="iptH37 fRight">
                        <option value="">全部状态</option>
                        <option value="1" <?php if($parm['openstatus']==1){?>selected<?php } ?> >开课</option>
                        <option value="2" <?php if($parm['openstatus']==2){?>selected<?php } ?> >未开课</option>
                    </select>
                    <select id="typeid" class="iptH37 fRight mr10">
                        <option value="">全部类型</option>
                        <?php foreach ($typies as $t){?>
                            <option value="<?php echo $t['id']?>" <?php if($parm['typeid']==$t['id']){?>selected<?php } ?> ><?php echo $t['name']?></option>
                        <?php } ?>
                    </select>
                </p>

                <table cellspacing="0" class="listTable">
                    <colgroup>
                        <col width="40%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="5%">
                        <col width="15%">

                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="aLeft">课程名称</th>
                        <th>课程类型</th>
                        <th>内训/公开</th>
                        <th>课程预算</th>
                        <th>天数</th>
                        <th>选择人数</th>
                        <th>操作</th>

                    </tr>
                    <?php foreach ($courses as $c){ ?>
                    <tr>
                        <td class="blue wordBreak"><?php echo !empty($c['title'])?$c['title']:$c['course_title'] ?></td>
                        <td class="aCenter"><?php echo $c['type_name'] ?></td>
                        <td class="aCenter"><?php if(!empty($c['external'])){echo ($c['external']=='1')?'公开':'内训';} ?></td>
                        <td class="aCenter"><?php echo $c['price'] ?></td>
                        <td class="aCenter"><?php echo !empty($c['day'])?$c['day']:1 ?>天</td>
                        <td class="aCenter"><?php echo $c['num'] ?></td>
                        <td class="aCenter">
                            <?php if($c['openstatus']==1){?>
                                <a href="<?php echo site_url('annualplan/opencourse/'.$plan['id'].'/'.$c['id'])?>" class="blue mr10">编辑</a><a href="<?php echo site_url('annualplan/closecourse/'.$plan['id'].'/'.$c['id'])?>" class="blue cancel">取消开课</a>
                            <?php }else{ ?>
                                <a href="<?php echo site_url('annualplan/opencourse/'.$plan['id'].'/'.$c['id'])?>" class="blue">开课</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>

                    </tbody>
                </table>
                <div class="pageNavi">
                    <?php echo $links ?>
                </div>
            </div>

        </div>
    </div>
</div>