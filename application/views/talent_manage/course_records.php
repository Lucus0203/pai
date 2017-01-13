<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css?<?php echo $this->config->item('version');?>"/>
<script>
    $(document).ready(function(){
        $('.delBtnErr').click(function(){
            alert('查看课程失败,此课程已被删除');
            return false;
        });
    });
</script>
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
            <?php if (!empty($courses)) { ?>
                <a class="blue" target="_blank" href="<?php echo site_url('export/courserecords/'.$student['id']) ?>"><i class="fa fa-file-excel-o fa-lg mr5"></i>导出</a>
            <?php }else{ ?>
                <a class="grayC" href="javascript:;"><i class="fa fa-file-excel-o fa-lg mr5"></i>导出</a>
            <?php } ?>
        </div>
    </div>
    <div class="comBox">
        <div class="clearfix p20" style="min-height: 450px;">
            <?php if (!empty($courses)) { ?>
                <table cellspacing="0" class="listTable">
                    <colgroup>
                        <col width="30%">
                        <col width="30%">
                        <col width="25%">
                        <col width="20%">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="aLeft">课程名称</th>
                        <th class="aCenter">开始时间</th>
                        <th class="aCenter">课程讲师</th>
                        <th class="aCenter">查看</th>
                    </tr>
                    <?php foreach ($courses as $c) { ?>
                        <tr>
                            <td><a class="blue" href="<?php echo site_url('course/courseinfo/'.$c['id']) ?>"><?php echo $c['title'] ?></a></td>
                            <td class="aCenter"><?php echo date('Y-m-d H:i',strtotime($c['time_start'])) ?></td>
                            <td class="aCenter"><?php echo !empty($c['teacher'])?$c['teacher']:'<span class="gray9">无</span>' ?></td>
                            <td class="aCenter">
                                <a class="blue <?php echo empty($c['isdel']==1)?'delBtnErr':''; ?> mr5" href="<?php echo site_url('course/courseinfo/'.$c['id'].'/'.$student['id']) ?>">课程</a>
                                <?php if($c['survey_num']>0){ ?><a class="blue mr5" href="<?php echo site_url('course/surveydetail/'.$c['id'].'/'.$student['id']) ?>" target="_blank">调研</a><?php } ?>
                                <?php if($c['ratings_num']>0){ ?><a class="blue" href="<?php echo site_url('course/ratingsdetail/'.$c['id'].'/'.$student['id']) ?>" target="_blank">反馈</a><?php } ?>
                            </td>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>
                <?php echo $links ?>
            <?php }else{ ?>
                <p class="clearfix f14 p2015">暂未课程评估</p>
            <?php } ?>
        </div>

    </div>
</div>