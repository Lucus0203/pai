<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css?<?php echo $this->config->item('version');?>"/>
<script>
    $(document).ready(function(){
        $('.delBtnErr').click(function(){
            alert('删除失败,已有学员提交了评估记录,请先删除评估记录');
            return false;
        });
    });
</script>
<div class="wrap">
    <div class="titCom clearfix"><span class="titSpan" ><?php echo $abilityjob['name'] ?></span>
        <?php $this->load->view ( 'ability_manage/top_tit' ); ?>
    </div>
    <div class="topNaviKec01">
        <div class="fRight">
            <a id="editEntryBtn" class="borBlueH37 mt5 mr5" href="<?php echo site_url('abilitymanage/createevaluation/'.$abilityjob['id'])?>">发起评估</a>
        </div>
        <?php $this->load->view ( 'ability_manage/top_navi' ); ?>
    </div>
    <div class="comBox">
        <div class="clearfix p20" style="min-height: 450px;">
            <?php if (!empty($abilityjobrecords)) { ?>
                <table cellspacing="0" class="listTable">
                    <colgroup>
                        <col width="30%">
                        <col width="25%">
                        <col width="15%">
                        <col width="15%">
                        <col width="15%">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="aLeft">评估名称</th>
                        <th class="aCenter">结束时间</th>
                        <th class="aCenter">评估人数</th>
                        <th class="aCenter">提交人数</th>
                        <th class="aCenter">操作</th>
                    </tr>
                    <?php foreach ($abilityjobrecords as $r) { ?>
                        <tr>
                            <td><a class="blue" href="<?php echo site_url('abilitymanage/evaluationlist/'.$r['id']) ?>"><?php echo $r['name'] ?></a></td>
                            <td class="aCenter"><?php echo date('Y-m-d H:i',strtotime($r['time_end'])) ?></td>
                            <td class="aCenter"><?php echo $r['evaluation_num'] ?></td>
                            <td class="aCenter"><?php echo $r['submit_num'] ?></td>
                            <td class="aCenter">
                                <a class="blue mr5" href="<?php echo site_url('abilitymanage/evaluationlist/'.$r['id']) ?>">名单</a>
                                <a class="blue mr5" href="<?php echo site_url('abilitymanage/editevaluation/'.$r['id']) ?>">编辑</a>
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