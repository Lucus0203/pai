<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css?<?php echo $this->config->item('version');?>"/>
<div class="wrap">
    <div class="titCom clearfix">
        <span class="titSpan" ><?php echo $student['name'] ?></span>
        <div class="fRight">
            <a class="borBlueH37" href="<?php echo $returntalenturl ?>" >返回列表</a>
        </div>
    </div>
    <div class="topNaviKec01">
        <?php $this->load->view ( 'talent_manage/top_navi' ); ?>
        <div class="fRight p13">
            <?php if (!empty($evaluates)) { ?>
                <a class="blue" target="_blank" href="<?php echo site_url('export/evaluaterecords/'.$student['id']) ?>"><i class="fa fa-file-excel-o fa-lg mr5"></i>导出</a>
            <?php }else{ ?>
                <a class="grayC" href="javascript:;"><i class="fa fa-file-excel-o fa-lg mr5"></i>导出</a>
            <?php } ?>
        </div>
    </div>
    <div class="comBox">
        <div class="clearfix p20" style="min-height: 450px;">
            <?php if (!empty($evaluates)) { ?>
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
                        <th class="aCenter">能力模型</th>
                        <th class="aCenter">标准总分</th>
                        <th class="aCenter">自评总分</th>
                        <th class="aCenter">他评总分</th>
                        <th class="aCenter">操作</th>
                    </tr>
                    <?php foreach ($evaluates as $e) { ?>
                        <tr>
                            <td><a class="blue" href="<?php echo site_url('abilitymanage/reportevaluation/'.$e['ability_job_evaluation_id'].'/'.$student['id']) ?>"><?php echo !empty($e['evaluation'])?$e['evaluation']:'<span class="gray9">无</span>' ?></a></td>
                            <td class="aCenter"><?php echo $e['abilityjob'] ?></td>
                            <td class="aCenter"><?php echo $e['point_standard'] ?></td>
                            <td class="aCenter"><?php echo $e['point'] ?></td>
                            <td class="aCenter"><?php echo $e['others_point'] ?></td>
                            <td class="aCenter">
                                <a class="blue" href="<?php echo site_url('abilitymanage/reportevaluation/'.$e['ability_job_evaluation_id'].'/'.$student['id']) ?>">查看</a>
                            </td>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>
                <?php echo $links ?>
            <?php }else{ ?>
                <p class="clearfix f14 p2015">暂未评估记录</p>
            <?php } ?>
        </div>

    </div>
</div>