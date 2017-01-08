<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css?<?php echo $this->config->item('version');?>"/>
<script>
    $(document).ready(function(){
        $('.detail').click(function(){
            if($(this).attr('href')=='#'){
                alert('暂无提交评测');
                return false;
            }else{
                return true;
            }
        });
        $('.delBtnErr').click(function(){
            return confirm('删除后评估记录将无法恢复,确定删除吗?');
        });
    });
</script>
<div class="wrap">
    <div class="titCom clearfix"><span class="titSpan" ><?php echo $abilityjob['name'] ?></span>
        <?php $this->load->view ( 'ability_manage/top_tit' ); ?>
    </div>
    <div class="topNaviKec01">
        <div class="fRight">
            <a id="editEntryBtn" class="borBlueH37 mt5 mr5" href="<?php echo site_url('abilitymanage/abilityjobrecords/'.$abilityjob['id']) ?>">返回</a>
        </div>
        <?php $this->load->view ( 'ability_manage/top_navi' ); ?>
    </div>
    <div class="comBox">
        <div class="clearfix p20" style="min-height: 450px;">
            <?php if (!empty($students)) { ?>
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
                        <th class="aLeft">评估学员</th>
                        <th class="aCenter">部门</th>
                        <th class="aCenter">标准总分</th>
                        <th class="aCenter">自评总分</th>
                        <th class="aCenter">他评总分</th>
                        <th class="aCenter">操作</th>
                    </tr>
                    <?php foreach ($students as $s) { ?>
                        <tr>
                            <td><a class="blue detail" href="<?php echo (!empty($s['point'])||!empty($s['others_point']))?site_url('abilitymanage/reportevaluation/'.$evaluation['id'].'/'.$s['id']):'#'; ?>"><?php echo $s['name'] ?></a></td>
                            <td class="aCenter"><?php echo $s['department'] ?></td>
                            <td class="aCenter"><?php echo $s['point_standard'] ?></td>
                            <td class="aCenter"><?php echo $s['point'] ?></td>
                            <td class="aCenter"><?php echo $s['others_point'] ?></td>
                            <td class="aCenter">
                                <a class="blue mr5 detail" href="<?php echo (!empty($s['point'])||!empty($s['others_point']))?site_url('abilitymanage/reportevaluation/'.$evaluation['id'].'/'.$s['id']):'#'; ?>">查看</a>
                                <a class="blue <?php echo (!empty($s['point'])||!empty($s['others_point']))?'delBtnErr':'delBtn'; ?>" href="<?php echo site_url('abilitymanage/delevaluationlist/'.$evaluation['id'].'/'.$s['id']); ?>">删除</a>
                            </td>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>
                <?php echo $links ?>
            <?php }else{ ?>
                <p class="clearfix f14 p2015">暂未提交评估记录</p>
            <?php } ?>
        </div>

    </div>
</div>