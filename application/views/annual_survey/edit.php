<div class="wrap">
    <div class="titCom clearfix">
        <span class="titSpan">创建新问卷</span></div>
    <div class="comBox">
        <?php if (!empty($msg)) {?>
            <p class="alertBox alert-success"><span class="alert-msg"><?php echo $msg ?></span><a href="javascript:;" class="alert-remove">X</a></p>
        <?php } ?>
        <div class="tableBox">
            <form id="editForm" method="post" action="">
                <input name="act" type="hidden" value="act"/>
                <table cellspacing="0" class="comTable">
                    <col width="20%"/>
                    <tr>
                        <th><span class="red">*</span>问卷名称</th>
                        <td>
                            <span class="iptInner">
                            <input name="title" value="<?php echo $survey['title'] ?>"
                                   type="text" class="iptH37 w345" placeholder="请输入问卷名称">
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>调查时间</th>
                        <td>
                            <span class="iptInner">
                            <input placeholder="开始时间" name="time_start" id="time_start" value="<?php echo empty($survey['time_start'])?'':date("Y-m-d H:i",strtotime($survey['time_start'])) ?>" type="text" class="iptH37 mr10 DTdate w156" >至<input placeholder="结束时间" name="time_end" id="time_end" value="<?php echo empty($survey['time_end'])?'':date('Y-m-d H:i',strtotime($survey['time_end'])) ?>" type="text" class="iptH37 ml10 DTdate w156" >
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th>问卷备注</th>
                        <td>
                            <span class="iptInner">
                            <textarea name="info" class="iptare pt10" placeholder="请输入问卷备注"><?php echo $survey['info'] ?></textarea>
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <span class="iptInner">
                            <input type="submit"
                                   value="<?php echo empty($survey) ? '创建问卷' : '保存问卷' ?>"
                                   class="coBtn mr30">
                                <label class="checkBox"><input
                                        name="public" <?php if ($survey['ispublic'] == '1') {
                                        echo 'checked="checked"';
                                    } ?> value="1" type="checkbox">发布</label>
                            </span>
                        </td>
                    </tr>
                </table>
        </div>
    </div>
</div>