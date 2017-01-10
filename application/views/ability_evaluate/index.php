<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css?<?php echo $this->config->item('version');?>"/>
<script>
    $(document).ready(function(){
        $('.delBtnErr').click(function(){
            alert('删除失败,已有学员提交了评估记录,请先删除评估记录');
            return false;
        });
        $('.editDisabled').click(function(){
            alert('模型已被删除,此评估无法再次编辑');
            return false;
        });
    });
</script>
<div class="wrap">
    <div class="textureCont width100">
        <div class="texturetip p2015 clearfix"><span class="fLeft pt5">能力评估</span>
            <div class="fRight">
                <a class="borBlueH37" href="<?php echo site_url('abilitymanage/createevaluation') ?>">发起能力评估</a>
            </div>
        </div>
        <div class="p15" style="min-height: 450px;">
            <?php if (!empty($abilityjobrecords)) { ?>
                <table cellspacing="0" class="listTable">
                    <colgroup>
                        <col width="20%">
                        <col width="20%">
                        <col width="15%">
                        <col width="15%">
                        <col width="15%">
                        <col width="15%">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="aLeft">评估名称</th>
                        <th class="aCenter">模型名称</th>
                        <th class="aCenter">结束时间</th>
                        <th class="aCenter">评估人数</th>
                        <th class="aCenter">提交人数</th>
                        <th class="aCenter">操作</th>
                    </tr>
                    <?php foreach ($abilityjobrecords as $r) { ?>
                        <tr>
                            <td><a class="blue" href="<?php echo site_url('abilityevaluate/evaluationlist/'.$r['id']) ?>"><?php echo $r['name'] ?></a></td>
                            <td class="aCenter"><?php echo $r['ability_name'] ?></td>
                            <td class="aCenter"><?php echo date('Y-m-d H:i',strtotime($r['time_end'])) ?></td>
                            <td class="aCenter"><?php echo $r['evaluation_num'] ?></td>
                            <td class="aCenter"><?php echo $r['submit_num'] ?></td>
                            <td class="aCenter">
                                <a class="blue mr5" href="<?php echo site_url('abilityevaluate/evaluationlist/'.$r['id']) ?>">名单</a>
                                <a class="blue <?php echo $r['abilityjob_delstatus']==1?'editDisabled':'' ?> mr5" href="<?php echo site_url('abilitymanage/editevaluation/'.$r['id']) ?>">编辑</a>
                                <?php if($r['submit_num']<=0){?>
                                    <a class="blue delBtn" href="<?php echo site_url('abilitymanage/delevaluation/'.$r['id']) ?>">删除</a>
                                <?php }else{ ?>
                                    <a class="blue delBtnErr" href="#">删除</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>
                <?php echo $links ?>
            <?php }else{ ?>
                <p class="clearfix f14 p2015">暂未发起评估</p>
            <?php } ?>
        </div>

    </div>
</div>

<div id="conWindow" style="z-index: 99999;display:none;" class="popWinBox">
    <div class="pop_div" style="z-index: 100001;">
        <div class="title_div"><a class="closeBtn" id="popConClose" href="javascript:;"><i class="fa fa-close fa-lg"></i></a><span id="title_divSpan"
                                                                                                   class="title_divText">请选择学员</span>
        </div>
        <div id="conMessage" class="pop_txt01">
            <div class="secBox">
                <ul class="oneUl">
                    <?php
                    foreach ($deparone as $k => $d) { ?>
                        <li class="deparone <?php if ($k == 0) {
                            echo 'secIpt';
                        } ?>"><input class="deparonecheckbox" type="checkbox" value="<?php echo $d['id']; ?>"/><?php echo $d['name']; ?>
                        </li>
                    <?php } ?>
                </ul>

                <ul class="twoUl">
                    <?php
                    foreach ($departwo as $k => $d) { ?>
                        <li class="departwo <?php if ($k == 0) {
                            echo 'secIpt';
                        } ?>"><input class="departwocheckbox" type="checkbox" value="<?php echo $d['id']; ?>"/><?php echo $d['name']; ?>
                        </li>
                    <?php } ?>
                </ul>
                <ul class="threeUl">
                    <?php
                    foreach ($students as $k => $s) { ?>
                        <li class="students"><input class="studentscheckbox" type="checkbox" value="<?php echo $s['id']; ?>"/><?php echo $s['name']; ?>
                        </li>
                    <?php } ?>

                </ul>
            </div>
            <ul class="com_btn_list clearfix">
                <li><a class="okBtn" href="javascript:void(0);" jsBtn="okBtn">确定</a></li>
            </ul>
        </div>

    </div>
    <div class="popmap" style="z-index: 100000;"></div>
</div>