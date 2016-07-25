<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8">
		<title>培训派</title>
		<link rel="icon" href="favicon.ico" type="image/x-icon" />
		<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/common.css" />

		<script type="text/javascript" src="<?php echo base_url();?>js/jquery1.83.js"></script>
		<script type="text/javascript" src="<?php echo base_url();?>js/jquery.validate.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url();?>js/additional-methods.min.js"></script>
		<script type="text/javascript"  src="<?php echo base_url() ?>js/wdate/WdatePicker.js"></script>
<script type="text/javascript">
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?9432a72cc245c2b9cafed658f471d489";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
			$(document).ready(function(){
				$('.listBox').delegate('.listCont','hover',function(){
					$(this).toggleClass('hover');
				});
                                $('.delBtn').click(function(){
                                    return confirm('确定删除吗?');
                                });
                                $('.logTabUl li').click(function(){
					var i=$(this).index();
					var lf=parseInt($(this).offset().left-$('.logTabUl').offset().left);
					var w=parseInt($(this).css('width'));
					$('.tabLine').animate({'left':lf,'width':w});
					$('.tableBox').hide().eq(i).show();
				});
                                $('.loginT').hover(function(){
					$(this).find('.logoList').show();
				},function(){
					$(this).find('.logoList').hide();
				})
			});
		</script>

	</head>

	<body>

		<div class="headerCom">
			<div class="inner">
				<div class="log">
					<a href="<?php echo site_url('index/index') ?>"><img src="<?php echo base_url();?>images/logo01.png" alt="培训 派"></a>
				</div>
				<ul class="hNavi">
                                        <?php $this->load->view ( 'h_navi' ); ?>
				</ul>
				<div class="loginT">
                                    <?php if(empty($loginInfo)){ ?>
                                        <img src="<?php echo empty($loginInfo['logo'])?base_url().'images/face_default.png':base_url().'uploads/company_logo/'.$loginInfo['logo'];?>">
                                    <?php }else{ ?>
                                        <a href="<?php echo site_url('center/index') ?>"><img src="<?php echo empty($loginInfo['logo'])?base_url().'images/face_default.png':base_url().'uploads/company_logo/'.$loginInfo['logo'];?>"><?php echo $loginInfo['real_name'] ?><i class="ci-right"><s>◇</s></i></a>
					<ul class="logoList">
						<li><a href="<?php echo site_url('center/index/1') ?>">公司信息</a></li>
						<li><a href="<?php echo site_url('center/index/2') ?>">密码修改</a></li>
                                                <?php if($loginInfo['role']==1){ ?><li><a href="<?php echo site_url('center/index/3') ?>">权限设置</a></li><?php } ?>
						<li><a href="<?php echo site_url('login/loginout') ?>">退出登陆</a></li>
					</ul>
                                    <?php } ?>
				</div>
			</div>
		</div>