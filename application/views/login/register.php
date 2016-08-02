<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>培训派</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/common.css"/>
    <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/login.css"/>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery1.83.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.validate.min.js"></script>
    <script type="text/javascript">
        var _hmt = _hmt || [];
        (function () {
            var hm = document.createElement("script");
            hm.src = "//hm.baidu.com/hm.js?9432a72cc245c2b9cafed658f471d489";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
        $(document).ready(function () {
            if (!+[1,]) {
                $('.ieTxt').show();
            } else {
                $('.ieTxt').hide()
            }
            // 手机号码验证
            jQuery.validator.addMethod("isMobile", function (value, element) {
                var length = value.length;
                var mobile = /^((1[0-9][0-9])+\d{8})$/;
                return this.optional(element) || (length == 11 && mobile.test(value));
            }, "请正确填写您的手机号码");
            jQuery.validator.addMethod("chrnum", function (value, element) {
                var chrnum = /^([a-zA-Z0-9]+)$/;
                return this.optional(element) || (chrnum.test(value));
            }, "只能输入数字和字母(字符A-Z, a-z, 0-9)");
            $("#signupForm").validate({
                rules: {
                    user_name: {
                        required: true,
                        chrnum: true,
                        minlength: 6
                    },
                    user_pass: {
                        required: true,
                        minlength: 5
                    },
                    password_confirm: {
                        required: true,
                        equalTo: "input[name=user_pass]"
                    },
                    company_name: {
                        required: true
                    },
                    industry_parent_id:{
                        required: true
                    },
                    industry_id:{
                        required: true
                    },
                    real_name: {
                        required: true,

                    },
                    mobile: {
                        required: true,
                        digits: true,
                        isMobile: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    mobile_code: {
                        required: true
                    },
                    invitation_code: {
                        required: true
                    }
                },
                messages: {

                    user_name: {
                        required: "用户名不能为空",
                        chrnum: "用户名必须是字母或数字",
                        minlength: "用户名必须大于6个字符"
                    },
                    user_pass: {
                        required: "请输入密码",
                        minlength: "密码的长度要大于5个字符"
                    },
                    password_confirm: {
                        required: "请再输入一次密码",
                        equalTo: "两次密码不一致"
                    },
                    company_name: {
                        required: "请输入您的企业名称",

                    },
                    industry_parent_id:{
                        required: "请选择所属行业"
                    },
                    industry_id:{
                        required: "请选择行业领域"
                    },
                    real_name: {
                        required: "请输入您的姓名",

                    },
                    mobile: {
                        required: "请输入您的电话号码",
                        digits: "只能输入数字",
                        isMobile: "请输入正确的手机号码",
                    },
                    email: {
                        required: "请输入您的邮箱地址",
                        email: "请输入正确的邮箱地址",
                    },
                    mobile_code: {
                        required: "请输入验证码",
                        digits: "只能输入数字"
                    },
                    invitation_code: {
                        required: "请输入邀请码"
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
                }
            });
            $('#get_mobile_code').click(function () {
                var username = $('#username').val();
                var mobile = $('#mobile').val();
                if (ismobile(mobile) && $('#get_mobile_code').attr('rel') <= 0) {
                    $.ajax({
                        type: "post",
                        url: '<?php echo site_url('login/getcode') ?>',
                        data: {'mobile': mobile, 'user_name': username},
                        success: function (res) {
                            if (res == 1) {
                                alert('验证码已发送,请注意查收')
                                $('#get_mobile_code').css('background-color', '#ccc').text('重新获取验证码60').attr('rel', '60');
                                remainsecondes = 60;
                                timing()
                            } else {
                                alert(res);
                            }
                        }
                    })
                }
                return false;

            });
            $('#industry_parent_id').change(function () {
                var parent_id = $(this).val();
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url('ajax/getIndustries') ?>',
                    data: {'parent_id': parent_id},
                    success: function (res) {
                        var json_obj = $.parseJSON(res);
                        var str = '<option value="">请选择行业领域</option>';
                        $.each(json_obj, function (i, item) {
                            str += '<option value="' + item.id + '">' + item.name + '</option>';
                        });
                        $('#industry_id').html(str);

                    }
                })
            });
        });
        function timing() {
            if (remainsecondes > 0) {
                setTimeout(function () {
                    remainsecondes--;
                    $('#get_mobile_code').text('重新获取验证码' + remainsecondes).attr('rel', remainsecondes);
                    timing();
                }, 1000);
            } else {
                $('#get_mobile_code').css('background-color', '#67d0de').text('获取验证码').attr('rel', 0);
            }
        }
        function ismobile(mobile) {
            if (mobile.length == 0) {
                alert('请输入手机号码！');
                $('input [name=mobile]').focus();
                return false;
            }
            if (mobile.length != 11) {
                alert('请输入有效的手机号码！');
                $('input [name=mobile]').focus();
                return false;
            }

            var myreg = /^0?1[0-9][0-9]\d{8}$/;
            if (!myreg.test(mobile)) {
                alert('请输入有效的手机号码！');
                $('input [name=mobile]').focus();
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
<div class="loginReg">
    <div class="header"><img src="<?php echo base_url(); ?>images/logo_login.png" alt="">
        <p class="aRight">已注册会员？<a href="<?php echo site_url('login/index') ?>">请登录 </a></p>

    </div>
    <div class="logCont">
        <div class="tit">企业管理员注册</div>
        <div class="logInner">
            <p class="red"><?php echo $msg ?></p>
            <form id="signupForm" action="" method="post">
                <input type="hidden" name="act" value="act"/>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="text" name="user_name" value="<?php echo $user['user_name'] ?>" class="ipt"
                               placeholder="用户名"/>
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="password" name="user_pass" value="<?php echo $user['user_pass'] ?>" class="ipt" placeholder="密码"/>
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="password" name="password_confirm" value="<?php echo $user['user_pass'] ?>" class="ipt" placeholder="再输入一次密码"/>
                    </div>
                </div>

                <div class="iptBox">
                    <div class="iptInner">
                        <input type="text" name="company_name" value="<?php echo $user_company_name ?>" class="ipt"
                               placeholder="企业注册名称"/>
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <select id="industry_parent_id" name="industry_parent_id" class="iptH37">
                            <option value="">请选择所属行业</option>
                            <?php foreach ($industry_parent as $pindus) { ?>
                                <option value="<?php echo $pindus['id'] ?>" <?php if($pindus['id']==$user_industry_parent['id']){ ?>selected<?php } ?> ><?php echo $pindus['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <select id="industry_id" name="industry_id" class="iptH37">
                            <option value="">请选择行业领域</option>
                            <?php if(!empty($user_industry_id)){
                                    foreach ($user_industrys as $ind){
                                ?>
                                <option value="<?php echo $ind['id'] ?>" <?php if($ind['id']==$user_industry_id){ ?>selected<?php } ?> ><?php echo $ind['name'] ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="text" name="real_name" value="<?php echo $user['real_name'] ?>" class="ipt"
                               placeholder="您的姓名"/>
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="text" name="email" value="<?php echo $user['email'] ?>" class="ipt"
                               placeholder="电子邮箱 "/>
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="text" id="mobile" value="<?php echo $user['mobile'] ?>" name="mobile" class="ipt"
                               placeholder="手机号码 "/>
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="text" name="mobile_code" value="<?php echo $user['mobile_code'] ?>"
                               class="ipt w157" placeholder="验证码 "/>
                        <a id="get_mobile_code" href="javascript:void(0)" class="coBtn" rel="0">获取验证码</a>
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="text" id="invitation_code" value="<?php echo $user['invitation_code'] ?>" name="invitation_code" class="ipt" placeholder="您的邀请码 "/>
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="submit" value="注册" class="blueBtn"/>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<p class="ieTxt"><span onclick="$('.ieTxt').hide()">X</span>您目前使用的浏览器无法获得最好的培训管理体验，建议您使用谷歌Chrome浏览器、360浏览器、猎豹浏览器和IE10等
</p>
