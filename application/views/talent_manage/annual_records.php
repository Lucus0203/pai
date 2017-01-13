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
            <?php if (!empty($annuals)) { ?>
                <a class="blue" target="_blank" href="<?php echo site_url('export/annualrecords/'.$student['id']) ?>"><i class="fa fa-file-excel-o fa-lg mr5"></i>导出</a>
            <?php }else{ ?>
                <a class="grayC" href="javascript:;"><i class="fa fa-file-excel-o fa-lg mr5"></i>导出</a>
            <?php } ?>
        </div>
    </div>
    <div class="comBox">
        <div class="clearfix p20" style="min-height: 450px;">
            <?php if (!empty($annuals)) { ?>
                <table cellspacing="0" class="listTable">
                    <colgroup>
                        <col width="60%">
                        <col width="25%">
                        <col width="15%">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="aLeft">调研名称</th>
                        <th class="aCenter">开始时间</th>
                        <th class="aCenter">操作</th>
                    </tr>
                    <?php foreach ($annuals as $a) { ?>
                        <tr>
                            <td><a class="blue" target="_blank" href="<?php echo site_url('annualsurvey/answerdetail/'.$a['answer_id']) ?>"><?php echo $a['title'] ?></a></td>
                            <td class="aCenter"><?php echo date('Y-m-d H:i',strtotime($a['time_start'])) ?></td>
                            <td class="aCenter">
                                <a class="blue" target="_blank" href="<?php echo site_url('annualsurvey/answerdetail/'.$a['answer_id']) ?>">查看</a>
                            </td>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>
                <?php echo $links ?>
            <?php }else{ ?>
                <p class="clearfix f14 p2015">暂未调研记录</p>
            <?php } ?>
        </div>

    </div>
</div>