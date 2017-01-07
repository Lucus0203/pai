$.extend({
    initStudentTarget: function(ajaxDepartmentAndStudentUrl,ajaxStudentUrl,onelis,targetone,targettwo,targetstudent) {
        var targetone=targetone||'';
        var targettwo=targettwo||'';
        var targetstudent=targetstudent||'';
    $('.wrap').append(
        '<div id="conWindow" style="z-index: 99999;display:none;" class="popWinBox">'+
        '<div class="pop_div" style="z-index: 100001;">'+
        '<div class="title_div"><a class="closeBtn" id="popConClose" href="javascript:;"><i class="fa fa-close fa-lg"></i></a><span id="title_divSpan" class="title_divText">请选择学员</span>'+
        '</div>'+
        '<div id="conMessage" class="pop_txt01">'+
        '<div class="secBox">'+
            '<ul class="oneUl">'+onelis+'</ul>'+
            '<ul class="twoUl"></ul>'+
            '<ul class="threeUl"></ul>'+
        '</div>'+
        '<ul class="com_btn_list clearfix">'+
            '<li><a class="okBtn" href="javascript:void(0);" jsBtn="okBtn">确定</a></li>'+
        '</ul>'+
        '</div>'+
        '</div>'+
        '<div class="popmap" style="z-index: 100000;"></div>'+
        '</div>'+
        '<input type="hidden" id="targetone" value="'+targetone+'" ><input type="hidden" id="targettwo" value="'+targettwo+'" ><input type="hidden" id="targetstudent" value="'+targetstudent+'" > '
    );
    //选择对象
    $('#addTarget').click(function () {
        $('#conWindow').show();
        resetconWindow();
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
                url: ajaxDepartmentAndStudentUrl,
            data: {'departmentid': $(this).find('input').val()},
        datatype: 'jsonp',
            success: function (res) {
            var json_obj = $.parseJSON(res);
            var count = 0;
            var str = '';
            $.each(json_obj.departs, function (i, item) {
                var secIpt = (i == 0) ? 'secIpt' : '';
                str += '<li class="departwo ' + secIpt + '"><input type="checkbox" value="' + item.id + '" />' + item.name + '</li>';
                ++count;
            });
            $('ul.twoUl').html(str);
            var studentcount = 0;
            var studentstr = '';
            $.each(json_obj.students, function (i, item) {
                studentstr += '<li class="students"><input type="checkbox" value="' + item.id + '" />' + item.name + '</li>';
                ++studentcount;
            });
            $('ul.threeUl').html(studentstr);
            //判断选中状态
            if (ischecked) {
                var targettwo = $('#targettwo').val();
                var strarr = targettwo.split(',');
                for (var i = 0; i < strarr.length; i++) {
                    $('ul.twoUl').find('input[value=' + strarr[i] + ']').attr('checked', 'checked');
                }

                var targetstudent = $('#targetstudent').val();
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
                url: ajaxStudentUrl,
            data: {'departmentid': $(this).find('input').val()},
        datatype: 'jsonp',
            success: function (res) {
            var json_obj = $.parseJSON(res);
            var count = 0;
            var studentstr = '';
            $.each(json_obj.students, function (i, item) {
                studentstr += '<li class="students"><input type="checkbox" value="' + item.id + '" />' + item.name + '</li>';
                ++count;
            });
            $('ul.threeUl').html(studentstr);
            //判断选中状态
            if (ischecked) {
                var targetstudent = $('#targetstudent').val();
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
            if ($('ul.twoUl').find('input:checked').length === 0) {
                $('.oneUl .secIpt').find('input').removeAttr('checked', 'checked');
            }
        } else {
            $('.twoUl .secIpt').find('input').attr('checked', 'checked');
            $('.oneUl .secIpt').find('input').attr('checked', 'checked');
        }

        //遍历赋值隐藏域
        targetsetval('oneUl', 'targetone');
        targetsetval('twoUl', 'targettwo');
        targetsetval('threeUl', 'targetstudent');

    });
    function targetsetval(ulclass, inputname) {
        //遍历初始化targetcheck值
        var str = $('#' + inputname).val();
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
        $('#' + inputname).val(arr.join(','));
        resetconWindow();
    }

    //调整弹窗列数
    function resetconWindow() {
        if ($('#conMessage .twoUl li').length <= 0) {
            $('#conMessage .twoUl').hide();
            $('#conMessage .oneUl,#conMessage .threeUl').width('45%');
        } else {
            $('#conMessage .oneUl,#conMessage .threeUl').width('33%');
            $('#conMessage .twoUl').show();
        }
    }

    //初始化数据
    $.ajax({
        type: "post",
        url: ajaxDepartmentAndStudentUrl,
        data: {'departmentid': $('.oneUl li:eq(0)').find('input').val()},
        datatype: 'jsonp',
        success: function (res) {
            var json_obj = $.parseJSON(res);
            var count = 0;
            var str = '';
            $.each(json_obj.departs, function (i, item) {
                var secIpt = (i == 0) ? 'secIpt' : '';
                str += '<li class="departwo ' + secIpt + '"><input type="checkbox" value="' + item.id + '" />' + item.name + '</li>';
                ++count;
            });
            $('ul.twoUl').html(str);
            var studentcount = 0;
            var studentstr = '';
            $.each(json_obj.students, function (i, item) {
                studentstr += '<li class="students"><input type="checkbox" value="' + item.id + '" />' + item.name + '</li>';
                ++studentcount;
            });
            $('ul.threeUl').html(studentstr);
            var targettwo = $('#targettwo').val();
            var strarr = targettwo.split(',');
            for (var i = 0; i < strarr.length; i++) {
                $('ul.twoUl').find('input[value=' + strarr[i] + ']').attr('checked', 'checked');
            }
            var targetstudent = $('#targetstudent').val();
            var strarr = targetstudent.split(',');
            for (var i = 0; i < strarr.length; i++) {
                $('ul.threeUl').find('input[value=' + strarr[i] + ']').attr('checked', 'checked');
            }
        }
    });
    // $('a.okBtn').click(function () {
    //     $(this).text('请稍后..');
    //     $.ajax({
    //             type: "post",
    //             url: '<?php echo site_url('course/updateTarget') ?>',
    //         data: {
    //         'targetstudent': $('input[name=targetstudent]').val()
    //     },
    //     async: false,
    //         success: function (res) {
    //         $('input[name=target]').val(res);
    //     }
    //     });
    //     $(this).text('确定');
    //     $('#conWindow').hide();
    //     return false;
    // });
    }
})