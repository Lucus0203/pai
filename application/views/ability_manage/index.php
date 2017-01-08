<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css?<?php echo $this->config->item('version');?>"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css?<?php echo $this->config->item('version');?>"/>
<script>
    $(document).ready(function(){
        $('#delseries').click(function(){
            if(confirm('确认删除么?')) {
                var url = $(this).attr('href');
                $.ajax({
                    type: "post",
                    url: url,
                    datatype: 'jsonp',
                    success: function (res) {
                        if (res == 1) {
                            window.location = '<?php echo site_url('abilitymanage/index')?>';
                        } else {
                            alert('该岗位系列存在能力模型,无法删除')
                        }
                    }
                });
            }
            return false;
        });
        $('.delAbilityJob').click(function(){
            var num=$(this).prev().find('a').text();
            var str=num > 0 ? '此能力模型含有评估记录,确认删除吗?' : '确认删除吗?';
            if(confirm(str)){
                var url = $(this).attr('href');
                $.ajax({
                    type: "post",
                    url: url,
                    datatype: 'jsonp',
                    success: function (res) {
                        if (res == 1) {
                            window.location = '<?php echo site_url('abilitymanage/index/'.$series['id'])?>';
                        } else {
                            alert('删除失败')
                        }
                    }
                });
            }
            return false;
        });
    });
</script>
<div class="wrap">
    <div class="textureCont width100">
        <div class="texturetip p2015 clearfix"><span class="fLeft pt5">模型管理</span>
        </div>
        <div class="clearfix textureBox noborder">
            <div class="textureSide">
                <a id="addType" href="<?php echo site_url('abilitymanage/addjobseries') ?>" class="topbtn">新增岗位系列</a>
                <?php foreach ($serieses as $s) { ?>
                    <div class="fnavi mb10">
                        <a href="<?php echo site_url('abilitymanage/index/'.$s['id']) ?>" class="flink <?php echo $series['id'] == $s['id'] ? 'on' : '' ?>"><?php echo $s['name'] ?></a>
                    </div>
                <?php } ?>
            </div>
            <div class="textureCont">
                <div class="texturetip textureWite clearfix mb10 mr20">
                    <p class="fLeft clearfix f14">共有<?php echo round($total_rows) ?>个能力模型</p>
                    <?php if(!empty($series['id'])){ ?>
                    <div class="fRight">
                        <a class="borBlueBtnH28 w72 aCenter" href="<?php echo site_url('abilitymanage/editjobseries/'.$series['id']) ?>">编辑岗位系列</a>
                        <a class="borBlueBtnH28 w72 aCenter" href="<?php echo site_url('abilitymanage/delseries/'.$series['id']) ?>" id="delseries">删除岗位系列</a>
                        <a class="borBlueBtnH28 w72 aCenter" href="<?php echo site_url('abilitymanage/createabilityjob/'.$series['id']) ?>">创建能力模型</a>
                    </div>
                    <?php } ?>
                </div>
                <div class="clearfix mr20" style="min-height: 450px;">
                    <?php if (!empty($abilityjobs)) { ?>
                        <table cellspacing="0" class="listTable">
                            <colgroup>
                                <col width="20%">
                                <col width="20%">
                                <col width="20%">
                                <col width="20%">
                                <col width="20%">
                            </colgroup>
                            <tbody>
                            <tr>
                                <th class="aLeft">能力名称</th>
                                <th class="aCenter">职级</th>
                                <th class="aCenter">领导力</th>
                                <th class="aCenter">评估记录</th>
                                <th class="aCenter">操作</th>
                            </tr>
                            <?php foreach ($abilityjobs as $j) { ?>
                                <tr>
                                    <td><a class="blue" href="<?php echo site_url('abilitymanage/detailabilityjob/'.$j['id']) ?>"><?php echo $j['name'] ?></a></td>
                                    <td class="aCenter"><?php echo $j['joblevel'] ?></td>
                                    <td class="aCenter"><?php echo $j['hasleadership']==1?'有':'无' ?></td>
                                    <td class="aCenter"><a class="blue" href="<?php echo site_url('abilitymanage/abilityjobrecords/'.$j['id']) ?>"><?php echo $j['evaluation_num'] ?></a></td>
                                    <td class="aCenter">
                                        <a class="blue editCourse mr5" href="<?php echo site_url('abilitymanage/detailabilityjob/'.$j['id']) ?>">查看</a>
                                        <a class="blue mr5" href="<?php echo site_url('abilitymanage/editabilityjob/'.$series['id'].'/'.$j['id']) ?>">编辑</a>
                                        <a class="blue delAbilityJob mr5" href="<?php echo site_url('abilitymanage/delabilityjob/'.$j['id']) ?>">删除</a>
                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>
                        <?php echo $links ?>
                    <?php }else{ ?>
                        <p class="clearfix f14 p2015">暂未创建能力模型</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>