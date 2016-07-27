<script type="text/javascript">
    $(document).ready(function () {
        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param)
        });
        $("#editForm").validate({
            rules: {
                title: {
                    required: true
                },
                time_start: {
                    required: true
                },
                time_end: {
                    required: true
                },
                address: {
                    required: true
                },
                page_img: {
                    accept: "image/*",
                    filesize: 5 * 1048576
                },
                outline: {
                    required: true
                }
            },
            messages: {

                title: {
                    required: "请输入课程标题"
                },
                time_start: {
                    required: "请输入开始时间"
                },
                time_end: {
                    required: "请输入结束时间"
                },
                address: {
                    required: "请输入课程地点"
                },
                page_img: {
                    accept: "请选择正确的图片格式",
                    filesize: "图片大小不能超过5M"
                },
                outline: {
                    required: "请输入课程大纲"
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

        $('.Wdate').eq(1).focus(function () {
            if ($('.Wdate').eq(1).val() == '') {
                $('.Wdate').eq(1).val($('.Wdate').eq(0).val());
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
                if ($('#fileBtn').parent().find('img').length > 0) {
                    $('#fileBtn').prev().attr('src', this.result);
                } else {
                    $('#fileBtn').before('<img onclick="$(\'#fileBtn\').click()" src="' + this.result + '" width="200" />');
                    $('#fileBtn').next().text('点击图片可更换,文件类型JPG、PNG、GIF格式图片，大小不超过5M');
                }
            }
        });
        //选择对象
        $('#addTarget').click(function () {
            $('#conWindow').show();
            return false;
        });
        $('#popConClose,a.okBtn').click(function () {
            $('#conWindow').hide();
            return false;
        });
        $('.deparone').click(function () {
            $(this).addClass('secIpt').siblings().removeClass('secIpt');
            var ischecked = $(this).find('input').is(':checked');
            $.ajax({
                type: "post",
                url: '<?php echo site_url('department/ajaxDepartmentAndStudent') ?>',
                data: {'departmentid': $(this).find('input').val()},
                datatype: 'jsonp',
                success: function (res) {
                    var json_obj = $.parseJSON(res);
                    var count = 0;
                    var str = '';
                    $.each(json_obj.departs, function (i, item) {
                        var secIpt = (i == 0) ? 'secIpt' : '';
                        str += '<li class="departwo ' + secIpt + '"><label><input type="checkbox" value="' + item.id + '" />' + item.name + '</label></li>';
                        ++count;
                    });
                    $('ul.twoUl').html(str);
                    var studentcount = 0;
                    var studentstr = '';
                    $.each(json_obj.students, function (i, item) {
                        studentstr += '<li class="students"><label><input type="checkbox" value="' + item.id + '" />' + item.name + '</label></li>';
                        ++studentcount;
                    });
                    $('ul.threeUl').html(studentstr);
                    //判断选中状态
                    if (ischecked) {
                        var targettwo = $('input[name=targettwo]').val();
                        var strarr = targettwo.split(',');
                        for (var i = 0; i < strarr.length; i++) {
                            $('ul.twoUl').find('input[value=' + strarr[i] + ']').attr('checked', 'checked');
                        }

                        var targetstudent = $('input[name=targetstudent]').val();
                        var strarr = targetstudent.split(',');
                        for (var i = 0; i < strarr.length; i++) {
                            $('ul.threeUl').find('input[value=' + strarr[i] + ']').attr('checked', 'checked');
                        }

                        if ($('ul.twoUl').find('input:checked').length === 0) {
                            $('ul.twoUl').find('input').attr('checked', 'checked');
                        }
                        if ($('ul.threeUl').find('input:checked').length === 0) {
                            $('ul.threeUl').find('input').attr('checked', 'checked');
                        }
                    } else {
                        $('ul.twoUl').find('input').removeAttr('checked');
                        $('ul.threeUl').find('input').removeAttr('checked');
                    }
                    //遍历赋值隐藏域
                    targetsetval('oneUl', 'targetone');
                    targetsetval('twoUl', 'targettwo');
                    targetsetval('threeUl', 'targetstudent');
                }
            });
        });
        $('.departwo').live('click', function () {
            $(this).addClass('secIpt').siblings().removeClass('secIpt');
            var ischecked = $(this).find('input').is(':checked');
            $.ajax({
                type: "post",
                url: '<?php echo site_url('department/ajaxStudent') ?>',
                data: {'departmentid': $(this).find('input').val()},
                datatype: 'jsonp',
                success: function (res) {
                    var json_obj = $.parseJSON(res);
                    var count = 0;
                    var studentstr = '';
                    $.each(json_obj.students, function (i, item) {
                        studentstr += '<li class="students"><label><input type="checkbox" value="' + item.id + '" />' + item.name + '</label></li>';
                        ++count;
                    });
                    $('ul.threeUl').html(studentstr);
                    //判断选中状态
                    if (ischecked) {
                        var targetstudent = $('input[name=targetstudent]').val();
                        var strarr = targetstudent.split(',');
                        for (var i = 0; i < strarr.length; i++) {
                            $('ul.threeUl').find('input[value=' + strarr[i] + ']').attr('checked', 'checked');
                        }
                        if ($('ul.threeUl').find('input:checked').length === 0) {
                            $('ul.threeUl').find('input').attr('checked', 'checked');
                        }
                    } else {
                        $('ul.threeUl').find('input').removeAttr('checked');
                    }
                    if ($('ul.twoUl').find('input:checked').length === 0) {
                        $('.oneUl .secIpt').find('input').removeAttr('checked', 'checked');
                    } else {
                        $('.oneUl .secIpt').find('input').attr('checked', 'checked');
                    }
                    //遍历赋值隐藏域
                    targetsetval('oneUl', 'targetone');
                    targetsetval('twoUl', 'targettwo');
                    targetsetval('threeUl', 'targetstudent');

                }
            });

        });
        $('.threeUl .students input').live('click', function () {
            if ($('ul.threeUl').find('input:checked').length === 0) {
                $('.twoUl .secIpt').find('input').removeAttr('checked', 'checked');
            } else {
                $('.twoUl .secIpt').find('input').attr('checked', 'checked');
            }

            //遍历赋值隐藏域
            targetsetval('oneUl', 'targetone');
            targetsetval('twoUl', 'targettwo');
            targetsetval('threeUl', 'targetstudent');

        });
        function targetsetval(ulclass, inputname) {
            //遍历初始化targetcheck值
            var str = $('input[name=' + inputname + ']').val();
            var arr = $.unique(str.split(','));
            $('ul.' + ulclass).find('input').each(function () {
                var v = $(this).val();
                if ($(this).is(':checked')) {
                    if ($.inArray(v, arr) === -1) {
                        arr.push(v);
                    }
                } else if ($.inArray(v, arr) !== -1) {
                    arr.splice($.inArray(v, arr), 1);
                }
            });
            for (var i = 0; i < arr.length; i++) {
                if (arr[i].length == 0) arr.splice(i, 1);
            }
            $('input[name=' + inputname + ']').val(arr.join(','));
            var targetstr = '';
            $('ul.oneUl input:checked,ul.twoUl input:checked,ul.threeUl input:checked').each(function () {
                targetstr += $(this).parent().text() + ',';
            });
            $('input[name=target]').val(targetstr.slice(0, -1));
        }

    });
</script>
<div class="wrap">
    <div class="titCom clearfix"><span class="titSpan"><?php echo empty($course) ? '创建新课程' : '编辑课程' ?></span></div>
    <div class="comBox">
        <div class="tableBox">
            <?php if (!empty($msg)) {
                echo '<p class="aCenter red f14">' . $msg . '</p>';
            } ?>
            <form id="editForm" method="post" action="" enctype="multipart/form-data">
                <input name="act" type="hidden" value="act"/>
                <table cellspacing="0" class="comTable">
                    <col width="20%"/>
                    <tr>
                        <th><span class="red">*</span>课程标题</th>
                        <td>
                                                            <span class="iptInner">
                                                            <input name="title" value="<?php echo $course['title'] ?>"
                                                                   type="text" class="iptH37 w345">
                                                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>开课时间</th>
                        <td>
                                                            <span class="iptInner">
                                                            <input name="time_start"
                                                                   value="<?php echo $course['time_start'] ?>"
                                                                   type="text" class="iptH37 w157 mr5 Wdate"
                                                                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
                                                                   autocomplete="off">至<input name="time_end"
                                                                                              value="<?php echo $course['time_end'] ?>"
                                                                                              type="text"
                                                                                              class="iptH37 w157 ml5 Wdate"
                                                                                              onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
                                                                                              autocomplete="off">
                                                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>课程地点</th>
                        <td>
                                                            <span class="iptInner">
                                                            <input name="address"
                                                                   value="<?php echo $course['address'] ?>" type="text"
                                                                   class="iptH37 w345">
                                                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th>课程讲师</th>
                        <td>
                                                            <span class="iptInner">
                                                            <select name="teacher_id" class="iptH37 w215">
                                                                <option value="">请选择</option>
                                                                <?php foreach ($teachers as $t) {
                                                                    echo $course['teacher_id'] == $t['id'] ? '<option selected value="' . $t['id'] . '">' . $t['name'] . '</option>' : '<option value="' . $t['id'] . '">' . $t['name'] . '</option>';
                                                                } ?>
                                                            </select>
                                                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th>培训对象</th>
                        <td>
                            <input type="hidden" name="targetone" value="<?php echo $course['targetone'] ?>"/><input
                                type="hidden" name="targettwo" value="<?php echo $course['targettwo'] ?>"/><input
                                type="hidden" name="targetstudent" value="<?php echo $course['targetstudent'] ?>"/>
                            <input readonly="true" name="target" value="<?php echo $course['target'] ?>" type="text"
                                   class="iptH37 w250"><a id="addTarget" class="borBlueH37 ml20"
                                                          href="javascript:void(0)"><i class="addQuan"></i>选择名单</a>
                            <p class="gray9">您的培训对象将在报名开启后收到报名通知</p>

                        </td>
                    </tr>

                    <tr>
                        <th>课程封面</th>
                        <td>
                                                            <span class="iptInner">
                                                            <?php if (!empty($course['page_img'])) { ?><img
                                                                onclick="$('#fileBtn').click()"
                                                                src="<?php echo base_url() . 'uploads/course_img/' . $course['page_img'] ?> "
                                                                width="200" /> <?php } ?>
                                                                <input name="page_img" type="file"
                                                                       style="visibility: hidden;" id="fileBtn"/><br><a
                                                                    href="javascript:;" onclick="$('#fileBtn').click()"
                                                                    class="borBlueH37 mb10">上传封面</a>
                                                            </span>
                            <p class="gray9"><?php echo !empty($course['page_img']) ? '点击图片可更换,文件类型' : '' ?>
                                JPG、PNG、GIF格式图片，大小不超过5M</p>
                        </td>
                    </tr>

                    <tr>
                        <th>课程价格</th>
                        <td>
                                                            <span class="iptInner">
                                                            <input name="price" value="<?php echo $course['price'] ?>"
                                                                   type="text" class="iptH37 w157 mr5">元/课时
                                                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th>课程介绍</th>
                        <td>
                                                            <span class="iptInner">
                                                            <textarea name="info"
                                                                      class="iptare"><?php echo $course['info'] ?></textarea>
                                                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th>课程收益</th>
                        <td>
                                                            <span class="iptInner">
                                                            <textarea name="income"
                                                                      class="iptare"><?php echo $course['income'] ?></textarea>
                                                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>课程大纲</th>
                        <td>
                                                            <span class="iptInner">
                                                            <textarea name="outline"
                                                                      class="iptare"><?php echo $course['outline'] ?></textarea>
                                                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                                                            <span class="iptInner">
                                                            <input type="submit"
                                                                   value="<?php echo empty($course) ? '创建课程' : '保存课程' ?>"
                                                                   class="coBtn mr30">
                                                                <label class="checkBox"><input
                                                                        name="public" <?php if ($course['ispublic'] == '1') {
                                                                        echo 'checked="checked"';
                                                                    } ?> value="1" type="checkbox">发布</label>
                                                            </span>
                        </td>
                    </tr>
                </table>
        </div>

    </div>
</div>

<div id="conWindow" style="z-index: 99999;display:none;" class="popWinBox">
    <div class="pop_div" style="z-index: 100001;">
        <div class="title_div"><a class="closeBtn" id="popConClose" href="javascript:;">X</a><span id="title_divSpan"
                                                                                                   class="title_divText">请选择对象</span>
        </div>
        <div id="conMessage" class="pop_txt01">
            <div class="secBox">
                <ul class="oneUl">
                    <?php
                    $arr = explode(",", $course['targetone']);
                    foreach ($deparone as $k => $d) { ?>
                        <li class="deparone <?php if ($k == 0) {
                            echo 'secIpt';
                        } ?>"><label><input class="deparonecheckbox" <?php if (in_array($d['id'], $arr)) {
                                    echo 'checked';
                                } ?> type="checkbox" value="<?php echo $d['id']; ?>"/><?php echo $d['name']; ?></label>
                        </li>
                    <?php } ?>
                </ul>

                <ul class="twoUl">
                    <?php
                    $arr = explode(",", $course['targettwo']);
                    foreach ($departwo as $k => $d) { ?>
                        <li class="departwo <?php if ($k == 0) {
                            echo 'secIpt';
                        } ?>"><label><input class="departwocheckbox" <?php if (in_array($d['id'], $arr)) {
                                    echo 'checked';
                                } ?> type="checkbox" value="<?php echo $d['id']; ?>"/><?php echo $d['name']; ?></label>
                        </li>
                    <?php } ?>
                </ul>
                <ul class="threeUl">
                    <?php
                    $arr = explode(",", $course['targetstudent']);
                    foreach ($students as $k => $s) { ?>
                        <li class="students <?php if ($k == 0) {
                            echo 'secIpt';
                        } ?>"><label><input class="studentscheckbox" <?php if (in_array($s['id'], $arr)) {
                                    echo 'checked';
                                } ?> type="checkbox" value="<?php echo $s['id']; ?>"/><?php echo $s['name']; ?></label>
                        </li>
                    <?php } ?>

                </ul>
            </div>
            <ul class="com_btn_list clearfix">
                <li><a class="okBtn" href="javascript:void(0);" jsBtn="okBtn">确定</a></li>
        </div>

    </div>
    <div class="popmap" style="z-index: 100000;"></div>
</div>