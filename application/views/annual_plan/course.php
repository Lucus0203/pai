<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css"/>
<script type="text/javascript"  src="<?php echo base_url() ?>js/jquery-ui.js"></script>
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
        $('#syncourse').click(function(){
            var flag=true;
            <?php if($total_syncoursed>0){?>
            flag=confirm('此操作将更新课程管理中的课程信息,确认同步吗?');
            <?php } ?>
            if(flag){
                $(this).text('同步中,请稍后..');
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url('annualplan/syncourse/'.$plan['id']) ?>',
                    async: false,
                    success: function (res) {
                        if (res == 1) {
                            $('.alert-success').show();
                            setTimeout(function(){$('.alert-success').fadeOut(500);},2000);
                            $('#syncourse').text('同步到课程管理');
                        }
                    }
                });
            }
            return false;
        });
        $('a.approvedstart,a.approvedgoon').click(function(){
            var msg=($(this).hasClass('approvedgoon'))?'确认继续审核并通知吗':'确认开启审核并通知吗?';
            if(confirm(msg)){
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url('annualplan/approvedstart/'.$plan['id']) ?>',
                    async: false,
                    dataType : 'json',
                    success: function (res) {
                        if (res.err=='approvaling'){
                            $('.alert-danger').html('<span class="alert-msg">'+res.msg+'</span><a href="javascript:;" class="alert-remove">X</a>').show();
                        }else if (res.err*1 > 0) {
                            var department='';
                            $.each(res.department,function(i,item){
                                department+=item.name+'、';
                            });
                            var href='<?php echo base_url() ?>department/index/'+res.department[0].department_id+'.html';
                            $('.alert-danger').html('<span class="alert-msg">以下部门尚未指定部门经理<a href="'+href+'" class="departmentUrl blue ml20">去完善</a></span><a href="javascript:;" class="alert-remove">X</a><br><br><span class="alert-msg black department">'+department+'</span>').show();
                        }else{
                            $('.approvedstart,.approvedgoon').hide();
                            $('.approvedpause,.alert-success').show();
                            setTimeout(function(){$('.alert-success').fadeOut(500);},2000);
                        }
                    }
                });
            }
            return false;
        });
        $('.approvedpause').click(function(){
            if(confirm('确定要暂停审核吗?')){
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url('annualplan/approvedpause/'.$plan['id']) ?>',
                    async: false,
                    dataType : 'json',
                    success: function (res) {
                        if (res==1) {
                            $('.approvedstart,.approvedpause').hide();
                            $('.approvedgoon,.alert-success').show();
                            setTimeout(function(){$('.alert-success').fadeOut(500);},2000);
                        }
                    }
                });
            }
            return false;
        });
        $('#cancelsyncourse').click(function(){
            if(confirm('确定取消同步课程吗?')){
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url('annualplan/cancelsyncourse/'.$plan['id']) ?>',
                    async: false,
                    dataType : 'json',
                    success: function (res) {
                        if (res==1) {
                            $('.alert-success').show();
                            setTimeout(function(){$('.alert-success').fadeOut(500);},2000);
                        }
                    }
                });
            }
            return false;
        });
        $(document).tooltip();
        clearTimeout(alertBoxTimeSet);
    });
</script>
<div class="wrap">
    <div class="titCom clearfix">
        <span class="titSpan"><?php echo $plan['title'] ?></span>
    </div>
    <p style="display: none;" class="alertBox alert-danger mb20">
        <span class="alert-msg">以下部门尚未指定部门经理<a href="#" class="departmentUrl blue ml20">去完善</a></span>
        <a href="javascript:;" class="alert-remove">X</a>
        <br><br><span class="alert-msg black department"></span>
    </p>
    <p style="display: none;" class="alertBox alert-success mb20">
        <span class="alert-msg">操作成功</span>
        <a href="javascript:;" class="alert-remove">X</a>
    </p>
    <div class="topNaviKec01">
        <?php $this->load->view ( 'annual_plan/top_navi' ); ?>
        <?php if($total_open>0){ ?>
        <ul class="fRight proPrint">
            <li>
                <a <?php if($plan['approval_status']!=3){ ?>style="display: none;"<?php } ?> href="#" class="approvedstart borBlueH37 f13" title="部门经理将收到学员选课通知" >开启审核并通知</a>
                <a <?php if($plan['approval_status']!=1){ ?>style="display: none;"<?php } ?> href="<?php echo site_url('annualplan/approvedpause/'.$plan['id']); ?>" class="approvedpause borBlueH37 f13" >暂停审核</a>
                <a <?php if($plan['approval_status']!=2){ ?>style="display: none;"<?php } ?> href="#" class="approvedgoon borBlueH37 f13" title="部门经理将收到学员选课通知">继续审核并通知</a>
            </li>
            <li>
                <a id="syncourse" href="#" class="borBlueH37 f13" title="开设了的课程将被添加到课程管理中" >同步到课程管理</a>
            </li>
            <li>
                <a id="cancelsyncourse" href="#" class="borBlueH37 f13" title="清除课程管理中该计划被同步的课程" >取消同步课程</a>
            </li>
        </ul>
        <?php } ?>
    </div>

    <div class="clearfix textureBox">
        <div class="p15">

            <div class="clearfix">
                <p class="clearfix f14 mb20">共有<?php echo $total ?>个课程,其中<?php echo $total_open ?>个开课
                    <select id="open_status" class="iptH37 fRight">
                        <option value="">全部状态</option>
                        <option value="1" <?php if($parm['openstatus']==1){?>selected<?php } ?> >已开设</option>
                        <option value="2" <?php if($parm['openstatus']==2){?>selected<?php } ?> >未开设</option>
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
                        <col width="5%">
                        <col width="5%">
                        <col width="15%">

                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="aLeft">课程名称</th>
                        <th>课程类型</th>
                        <th>课程预算</th>
                        <th>课时</th>
                        <th>选课人数</th>
                        <th>通过名单</th>
                        <th>操作</th>

                    </tr>
                    <?php foreach ($courses as $c){ ?>
                    <tr>
                        <td class="wordBreak"><?php if($c['openstatus']==1){?><a href="<?php echo site_url('annualplan/opencourse/'.$plan['id'].'/'.$c['id'])?>" class="blue mr10"><?php echo !empty($c['title'])?$c['title']:$c['course_title'] ?><i class="fa fa-edit fa-lg ml10"></i></a><?php }else{ echo !empty($c['title'])?$c['title']:$c['course_title']; } ?></td>
                        <td class="aCenter"><?php echo $c['type_name'] ?></td>
                        <td class="aCenter"><?php echo !empty($c['price'])?$c['price']:'未填写' ?></td>
                        <td class="aCenter"><?php echo !empty($c['day'])?$c['day']:'未填写' ?></td>
                        <td class="aCenter"><?php echo round($c['num']) ?></td>
                        <td class="aCenter"><?php echo round($c['list_num']) ?></td>
                        <td class="aCenter">
                            <?php if($c['openstatus']==1){?>
                                <a href="<?php echo site_url('annualplan/courselist/'.$plan['id'].'/'.$c['id']); ?>" class="blue mr10">审核</a><a href="<?php echo site_url('annualplan/closecourse/'.$plan['id'].'/'.$c['id'])?>" class="blue cancel">取消</a>
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