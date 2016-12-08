<script type="text/javascript">
    $(document).ready(function () {
        $('#department_parent_id').change(function(){
            var departmentid=$(this).val();
            $.ajax({
                type:"post",
                url:'<?php echo site_url('department/ajaxDepartmentAndStudent/teacheredit') ?>',
                data:{'departmentid':departmentid},
                datatype:'jsonp',
                success:function(res){
                    var json_obj = $.parseJSON(res);
                    var count=0;
                    var str='<option value="'+departmentid+'">请选择</option>';
                    $.each(json_obj.departs,function(i,item){
                        str+='<option value="'+item.id+'">'+item.name+'</option>';
                        ++count;
                    });
                    if(count>0){
                        $('#department_id').show().html(str)
                    }else{
                        $('#department_id').hide().html('<option value="'+departmentid+'" selected >请选择</option>');
                    }
                }
            });
            $.ajax({
                type:"post",
                url:'<?php echo site_url('teacher/ajaxStudent/'.$teacher['id']) ?>',
                data:{'departmentid':departmentid},
                datatype:'jsonp',
                success:function(res){
                    var json_obj = $.parseJSON(res);
                    var str='<option value="">请选择</option>';
                    $.each(json_obj.students,function(i,item){
                        str+='<option value="'+item.id+'">'+item.name+'</option>';
                    });
                    $('select[name=student_id]').html(str);
                }
            });

        });
        $('#department_id').change(function(){
            var departmentid=$(this).val();
            $.ajax({
                type:"post",
                url:'<?php echo site_url('teacher/ajaxStudent/'.$teacher['id']) ?>',
                data:{'departmentid':departmentid},
                datatype:'jsonp',
                success:function(res){
                    var json_obj = $.parseJSON(res);
                    var str='<option value="">请选择</option>';
                    $.each(json_obj.students,function(i,item){
                        str+='<option value="'+item.id+'">'+item.name+'</option>';
                    });
                    $('select[name=student_id]').html(str);
                }
            });
        });

        $('input[name=type]').change(function(){
            if($('input[name=type]:checked').val()=='1'){
                $('#editForm .comTable tr').eq(1).show();
                $('#editForm .comTable tr').eq(2).show();
                $('#editForm .comTable tr').eq(3).hide();
            }else{
                $('#editForm .comTable tr').eq(1).hide();
                $('#editForm .comTable tr').eq(2).hide();
                $('#editForm .comTable tr').eq(3).show();
            }
        });

        $("#editForm").validate({
            rules: {
                name: {
                    required: function(element){
                        return $('input[name=type]:checked').val() == '2' ;
                    }
                },
                student_id: {
                    required: function(element){
                        return $('input[name=type]:checked').val() == '1' ;
                    }
                }
            },
            messages: {
                name: {
                    required: "请输入讲师姓名"
                },
                student_id: {
                    required: "请选择内部讲师"
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
            submitHandler:function(form){
                $('input[type=submit]').val('请稍后..').attr('disabled','disabled');
                form.submit();
            }
        });
        $('#fileBtn').change(function () {
            // 检查是否为图像类型
            var simpleFile = document.getElementById("fileBtn").files[0];
            if (!/image\/\w+/.test(simpleFile.type)) {
                alert("请确保文件类型为图像类型");
                return false;
            }
            var reader = new FileReader();
            // 将文件以Data URL形式进行读入页面
            reader.readAsDataURL(simpleFile);
            reader.onload = function (e) {
                $('#fileBtn').prev().attr('src', this.result);
            }
        });
    });
</script>
<div class="wrap">
    <div class="titCom clearfix"><span class="titSpan"><?php echo empty($teacher) ? '创建讲师' : '编辑讲师' ?></span></div>
    <div class="comBox">
        <?php if (!empty($msg)) {?>
            <p class="alertBox alert-success"><span class="alert-msg"><?php echo $msg ?></span><a href="javascript:;" class="alert-remove">X</a></p>
        <?php } ?>
        <div class="tableBox">
            <form id="editForm" method="post" action="" enctype="multipart/form-data">
                <input name="act" type="hidden" value="act"/>
                <input name="refere_url" type="hidden" value="<?php echo $_SERVER['HTTP_REFERER'];?>" />
                <div class="upPhoto">
                    <span><?php if (!empty($teacher['head_img'])) { ?><img src="<?php echo base_url() ?>/uploads/teacher_img/<?php echo $teacher['head_img'] ?>" alt="" width="122"><?php } else { ?><img src="<?php echo base_url() ?>images/face_default.png" width="122"><?php } ?><input name="head_img" type="file" style="<?php if (empty($teacher['head_img'])) { ?>visibility: hidden;<?php } else { ?>display:none<?php } ?>" id="fileBtn"/><a class="blue" href="javascript:;" onclick="$('#fileBtn').click()">上传头像</a>
                    </span>
                </div>
                <table cellspacing="0" class="comTable">
                    <col width="20%"/>
                    <tr>
                        <th><span class="red">*</span>师资类型</th>
                        <td>
                            <ul class="lineUl">
                                <li>
                                    <label><input name="type" checked="checked" value="1" type="radio">内部</label></li>
                                <li>
                                    <label><input name="type" <?php echo $teacher['type'] == 2 ? 'checked="checked"' : '' ?> value="2" type="radio">外部</label></li>
                            </ul>

                        </td>
                    </tr>
                    <tr style="<?php if($teacher['type']==2){echo 'display:none;';} ?>">
                        <th><span class="red">*</span>所在部门</th>
                        <td><span class="iptInner">
                                <select id="department_parent_id" class="iptH37 w156">
                                    <option value="" selected>请选择</option>
                                    <?php foreach($departments as $d){ ?>
                                        <option <?php if($d['id']==$stu['department_parent_id']){ ?>selected<?php } ?> value="<?php echo $d['id'] ?>"><?php echo $d['name'] ?></option>
                                    <?php } ?>
                                </select>&nbsp;
                                <select <?php if(count($second_departments)<=0){?>style="display: none;"<?php } ?> id="department_id" class="iptH37 w156">
                                    <option value="<?php echo $stu['department_parent_id']?>" selected >请选择</option>
                                    <?php foreach($second_departments as $d){ ?>
                                        <option <?php if($d['id']==$stu['department_id']){ ?>selected<?php } ?> value="<?php echo $d['id'] ?>"><?php echo $d['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </span>
                        </td>
                    </tr>
                    <tr style="<?php if($teacher['type']==2){echo 'display:none;';} ?>">
                        <th><span class="red">*</span>讲师姓名</th>
                        <td><span class="iptInner">
                                <select name="student_id" class="iptH37 w156">
                                    <option value="" selected >请选择</option>
                                    <?php foreach ($students as $s){ ?>
                                        <option value="<?php echo $s['id'] ?>"<?php if($s['id']==$teacher['student_id']){?>selected<?php } ?>><?php echo $s['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </span>
                        </td>
                    </tr>
                    <tr style="<?php if($teacher['type']!=2){echo 'display:none;';} ?>">
                        <th><span class="red">*</span>讲师姓名</th>
                        <td>
                            <span class="iptInner">
                            <input name="name" placeholder="请输入讲师姓名" value="<?php echo $teacher['name'] ?>"
                                   type="text" class="iptH37 w237" >
                            </span>

                        </td>
                    </tr>

                    <tr>
                        <th><span class="red">*</span>工作形式</th>
                        <td>
                            <ul class="lineUl">
                                <li>
                                    <input checked="checked" name="work_type" value="1" type="radio">专职
                                </li>
                                <li>
                                    <input name="work_type" <?php echo $teacher['work_type'] == 2 ? 'checked="checked"' : '' ?> value="2" type="radio">兼职
                                </li>
                            </ul>

                        </td>
                    </tr>
                    <tr>
                        <th>擅长类别</th>
                        <td>
                            <span class="iptInner">
                            <input name="specialty" placeholder="请输入擅长类型" value="<?php echo $teacher['specialty'] ?>" type="text" class="iptH37 w237">

                        </td>
                    </tr>
                    <tr>
                        <th>授课年限</th>
                        <td>
                            <span class="iptInner">
                            <select name="years" class="iptH37 w237">
                                <option value="">请选择</option>
                                <?php for ($i = 1; $i <= 30; $i++) {
                                    if ($teacher['years'] == $i) {
                                        echo '<option selected="selected" value="' . $i . '">' . $i . '年</option>';
                                    } else {
                                        echo '<option value="' . $i . '">' . $i . '年</option>';
                                    }
                                } ?>
                            </select>
                            </span>

                        </td>
                    </tr>

                    <tr>
                        <th>授课薪酬</th>
                        <td>
                            <span class="iptInner">
                            <input name="hourly" placeholder="请输入授课薪酬" value="<?php echo $teacher['hourly'] ?>" type="text" class="iptH37 w157 mr20">元/课时
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th>讲师简介</th>
                        <td>
                            <span class="iptInner">
                            <textarea name="info" placeholder="请输入讲师简介和头衔" class="iptare pt10"><?php echo $teacher['info'] ?></textarea>
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="保存" class="coBtn">
                        </td>
                    </tr>
                </table>
            </form>
        </div>

    </div>
</div>