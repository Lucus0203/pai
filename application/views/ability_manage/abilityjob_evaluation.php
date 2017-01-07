<script type="text/javascript"  src="<?php echo base_url() ?>js/trainingpie.student-target.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#time_end').appendDtpicker({"locale": "cn","calendarMouseScroll": false,"autodateOnStart": false,"closeOnSelected": true,'futureOnly':true});
        $("#editForm").validate({
            rules: {
                name: {
                    required: true
                },
                time_end: {
                    required: true
                },
                job_series_id: {
                    required: true
                },
                job_level_id: {
                    required: true
                },
                ability_job_id: {
                    required: true
                },
                targetstudent: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "请输入评估名称"
                },
                time_end: {
                    required: "请输入结束时间"
                },
                job_series_id: {
                    required: "请选择岗位系列"
                },
                job_level_id: {
                    required: "请选择岗位职级"
                },
                ability_job_id: {
                    required: "请选择模型名称"
                },
                targetstudent: {
                    required: "请选择需要评估的学员"
                }
            },
            errorPlacement: function (error, element) {
                error.addClass("ui red pointing label transition");
                error.insertAfter(element.parent());
            },
            highlight: function (element, errorClass, validClass) {
                $(element).parents(".row").addClass(errorClass);
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents(".row").removeClass(errorClass);
            },
            submitHandler: function (form) {
                $('input[type=submit]').val('请稍后..').attr('disabled', 'disabled');
                form.submit();
            }
        });
        $('#time_end').focus(function(){
            $(this).val($.trim($(this).val())==''?$('#time_start').val():$(this).val());
        });
        //选择岗位系列
        $('#job_series_id').change(function(){
            var series_id=$(this).val();
            $.ajax({
                type: "post",
                url: '<?php echo base_url() ?>'+'abilitymanage/getabilityjobbyseries/'+series_id,
                datatype: 'jsonp',
                success: function (res) {
                    var json_obj = $.parseJSON(res);
                    var str='<option value="">请选择</option>';
                    if(json_obj.prolevels.length>0) {
                        str+='<optgroup label="专业系">';
                        $.each(json_obj.prolevels, function (i, item) {
                            str+='<option value="'+item.id+'">'+item.name+'</option>';
                        });
                        str+='</optgroup>';
                    }
                    if(json_obj.maglevels.length>0) {
                        str+='<optgroup label="管理系">';
                        $.each(json_obj.maglevels, function (i, item) {
                            str+='<option value="'+item.id+'">'+item.name+'</option>';
                        });
                        str+='</optgroup>';
                    }
                    $('#job_level_id').html(str).removeAttr('readonly');
                    $('#ability_job_id').html('<option value="">请选择</option>').attr('readonly','readonly');
                }
            });
        });
        $('#job_level_id').change(function(){
            var series_id=$('#job_series_id').val();
            var level_id=$(this).val();
            $.ajax({
                type: "post",
                url: '<?php echo base_url() ?>'+'abilitymanage/getabilityjobbylevel/'+series_id+'/'+level_id,
                datatype: 'jsonp',
                success: function (res) {
                    var json_obj = $.parseJSON(res);
                    if(json_obj.jobs.length>0) {
                        var str='<option value="">请选择</option>';
                        $.each(json_obj.jobs, function (i, item) {
                            str+='<option value="'+item.id+'">'+item.name+'</option>';
                        });
                    }
                    $('#ability_job_id').html(str).removeAttr('readonly');
                }
            });
        });
        //选择评估学员
        var onelis='<?php $arr = explode(",", $target['targetone']); foreach ($target['deparone'] as $k => $d) { ?><li class="deparone <?php if ($k == 0) { echo 'secIpt'; } ?>"><input class="deparonecheckbox" <?php if (in_array($d['id'], $arr)) { echo 'checked'; } ?> type="checkbox" value="<?php echo $d['id']; ?>"/><?php echo $d['name']; ?></li><?php } ?>';
        var targetone='<?php echo $target['targetone'] ?>';
        var targettwo='<?php echo $target['targettwo'] ?>';
        var targetstudent='<?php echo $target['targetstudent'] ?>';
        $.initStudentTarget('<?php echo site_url('department/ajaxDepartmentAndStudent') ?>','<?php echo site_url('department/ajaxStudent') ?>',onelis,targetone,targettwo,targetstudent);

         $('a.okBtn').click(function () {
             $(this).text('请稍后..');
             $.ajax({
                 type: "post",
                 url: '<?php echo site_url('ajax/getStudentsByIds') ?>',
                 data: {'targetstudent': $('#targetstudent').val()},
                 async: false,
                 success: function (res) {
                     var str='';
                     if(res!=0){
                         var json_obj = $.parseJSON(res);
                         if(json_obj.students.length>0){
                             var str='';
                             $.each(json_obj.students, function (i, item) {
                                 str+='<tr><td class="aCenter">'+item.name+'</td>'+
                                     '<td class="aCenter">'+item.department+'</td>'+
                                     '<td class="aCenter"><a class="blue delListStu" href="#" rel="'+item.id+'">删除</a></td>'+
                                     '</tr>';
                             });
                             $('input[name=targetstudent]').val($('#targetstudent').val());
                             if($('input[name=targetstudent]').val()!=''){
                                 $('input[name=targetstudent]').parent().parent().find('label.error').hide();
                             }
                         }
                     }
                     $('#studentlist').html(str);
                 }
             });
             $(this).text('确定');
             $('#conWindow').hide();
             return false;
         });
        $('.listTable .delListStu').live('click',function(){
            var v=$(this).attr('rel');
            var str=$('input[name=targetstudent]').val();
            var arr = $.unique(str.split(','));
            $('ul.threeUl').find('input[value=' + v + ']').removeAttr('checked');
            if ($('ul.threeUl').find('input:checked').length === 0) {
                $('.twoUl .secIpt').find('input').removeAttr('checked');
            }
            if ($('ul.twoUl').find('input:checked').length === 0) {
                $('.oneUl .secIpt').find('input').removeAttr('checked', 'checked');
            }
            if ($.inArray(v, arr) !== -1) {
                arr.splice($.inArray(v, arr), 1);
            }
            $('input[name=targetstudent],#targetstudent').val(arr.join(','));
            $(this).parent().parent().remove();
            return false;
        });
    });
</script>
<div class="wrap">
    <div class="titCom clearfix"><span class="titSpan"><?php echo empty($evaluation) ? '发起能力评估' : '编辑能力评估'; ?></span></div>
    <div class="comBox" style="min-height: 550px;">
        <div class="tableBox">
            <form id="editForm" method="post" action="" enctype="multipart/form-data">
                <input name="act" type="hidden" value="act"/>
                <input name="backuri" type="hidden" value="<?php echo $_SERVER['HTTP_REFERER'] ?>"/>
                <table cellspacing="0" class="comTable">
                    <col width="20%"/>
                    <col width="38%"/>
                    <tr>
                        <th style="vertical-align: middle;">评估名称</th>
                        <td>
                            <span class="iptInner ">
                            <input name="name" value="<?php echo $evaluation['name'] ?>" type="text" class="iptH37 w156" placeholder="请输入模型名称">
                            </span>
                        </td>
                        <td class="aLeft" rowspan="7" style="padding-top: 19px;">
                            <div style="width: 250px;border: 1px solid #ccc;">
                                <table cellspacing="0" class="listTable scrollListTable">
                                    <colgroup>
                                        <col width="25%">
                                        <col width="55%">
                                        <col width="20%">
                                    </colgroup>
                                    <tbody>
                                    <tr>
                                        <th class="aCenter">名单</th>
                                        <th class="aCenter">部门</th>
                                        <th class="aCenter">&nbsp;</th>
                                    </tr>
                                    </tbody>
                                </table>
                                <div style="overflow-y: scroll;height: 405px;">
                                    <table cellspacing="0" class="listTable scrollListTable">
                                        <colgroup>
                                            <col width="25%">
                                            <col width="55%">
                                            <col width="20%">
                                        </colgroup>
                                        <tbody id="studentlist">
                                        <?php foreach ($students as $s){?>
                                            <tr>
                                                <td class="aCenter"><?php echo $s['name'] ?></td>
                                                <td class="aCenter"><?php echo $s['department'] ?></td>
                                                <td class="aCenter"><a class="blue delListStu" href="#" rel="<?php echo $s['id'] ?>">删除</a></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th style="vertical-align: middle;">结束时间</th>
                        <td>
                            <span class="iptInner ">
                            <input placeholder="结束时间" name="time_end" id="time_end" value="<?php echo empty($evaluation['time_end'])?'':date('Y-m-d H:i',strtotime($evaluation['time_end'])) ?>" type="text" class="iptH37 w156" autocomplete="off" >
                            </span>
                        </td>
                    </tr>
                    <?php if(!empty($abilityjob)){ ?>
                        <tr>
                            <th style="vertical-align: middle;">岗位系列</th>
                            <td>
                            <span class="iptInner ">
                                <?php echo $job_series['name'] ?>
                            </span>
                            </td>
                        </tr>
                        <tr>
                            <th style="vertical-align: middle;">岗位职级</th>
                            <td>
                            <span class="iptInner ">
                                <?php echo $job_level['name'] ?>
                            </span>
                            </td>
                        </tr>
                        <tr>
                            <th style="vertical-align: middle;">模型名称</th>
                            <td>
                            <span class="iptInner ">
                                <input type="hidden" name="ability_job_id" value="<?php echo $abilityjob['id']; ?>" />
                                <?php echo $abilityjob['name'] ?>
                            </span>
                            </td>
                        </tr>
                    <?php }else{ ?>
                        <tr>
                            <th style="vertical-align: middle;">岗位系列</th>
                            <td>
                                <span class="iptInner ">
                                    <select id="job_series_id" name="job_series_id" class="iptH37 w156">
                                        <option value="">请选择</option>
                                        <?php foreach ($job_series as $j){ ?>
                                            <option value="<?php echo $j['id'] ?>"><?php echo $j['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th style="vertical-align: middle;">岗位职级</th>
                            <td>
                                <span class="iptInner ">
                                    <select id="job_level_id" name="job_level_id" class="iptH37 w156" readonly="readonly">
                                        <option value="">请选择</option>
                                    </select>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th style="vertical-align: middle;">模型名称</th>
                            <td>
                                <span class="iptInner ">
                                    <select id="ability_job_id" name="ability_job_id" class="iptH37 w156" readonly="readonly">
                                        <option value="">请选择</option>
                                    </select>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th style="vertical-align: middle;">评估学员</th>
                        <td>
                            <span class="iptInner ">
                                <a id="addTarget" class="borBlueH37" href="javascript:void(0)">选择学员</a>
                                <input type="hidden" name="targetstudent" value="<?php echo $evaluation['targetstudent'] ?>" />
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <th style="vertical-align: middle;"></th>
                        <td colspan="2">
                            <span class="iptInner">
                                <input type="submit" value="保存" class="coBtn mr30">
                                <input type="button" value="返回" class="coBtn" onclick="history.back(-1);">
                            </span>
                        </td>
                    </tr>
                </table>
        </div>

    </div>
</div>
