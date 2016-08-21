<script type="text/javascript">
    $(document).ready(function(){
        $('[js="addPingfen"]').click(function(){
            $('.zuoyeList').append('<li class="fengfen">评分题<input name="ratingses[]" type="text" class="iptH37 w600 ml10" value=""><input type="hidden" name="type[]" value="1" /><a href="javascript:;" js="removeZuoye" class="blue ml10">删除</a></li>');
        })
        $('[js="addKaifang"]').click(function(){
            $('.zuoyeList').append('<li class="kaifang">开放题<input name="ratingses[]" type="text" class="iptH37 w600 ml10" value=""><input type="hidden" name="type[]" value="2" /><a href="javascript:;" js="removeZuoye" class="blue ml10">删除</a></li>');
        })
        $('[js="removeZuoye"]').live('click',function(){
            $(this).parent().remove();
        });
    });
    function checkform(){
        if($('#anstotal').val()*1>0){
            return confirm('已提交的学员需要重新填写,确认保存吗?');
        }else{
            return true;
        }
    }
</script>
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css" />
<div class="wrap">
        <div class="titCom clearfix"><span class="titSpan"><?php echo $course['title'] ?>  </span><span class="<?php echo $course['status_class']; ?> ml20"><?php echo $course['status_str']; ?></span></div>
        <div class="topNaviKec">
                <?php $this->load->view ( 'course/top_navi' ); ?>
        </div>
        <div class="comBox clearfix">
                <div class="baoming">

                        <div class="sideLeft">
                                <ul class="sideLnavi">
<?php if($loginInfo['role']==1||$roleInfo['ratingsedit']==1){ ?>
                                        <li class="cur"><a href="<?php echo site_url('course/ratingsedit/'.$course['id']) ?>">问题设置<i class="ml10 fa fa-angle-right fa-lg"></i></a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['ratingslist']==1){ ?>
                                        <li><a href="<?php echo site_url('course/ratingslist/'.$course['id']) ?>">反馈结果</a></li>
<?php } ?>
                                </ul>

                        </div>
                        <div class="contRight">
                        <form id="editForm" method="post" action="" onsubmit="return checkform()">
                            <input name="act" type="hidden" value="act" />
                            <input id="anstotal" type="hidden" value="<?php echo $anstotal ?>" />
                            <?php if (!empty($msg)) {?>
                                <p class="alertBox alert-success mb20"><span class="alert-msg"><?php echo $msg ?></span><a href="javascript:;" class="alert-remove">X</a></p>
                            <?php } ?>
                                <p class="yellowTipBox mb20">如果已有学员提交，修改问题后需要学员重新填写</p>
                                <ul class="zuoyeList">
                                    <?php foreach ($ratingses as $k=>$h){ ?>
                                    <li class="<?php echo $h['type']==1?'pingfen':'kaifang' ?>"><?php echo $h['type']==1?'评分题':'开放题' ?>
                                            <input name="ratingses[]" type="text" class="iptH37 w600 ml10 <?php echo ($k==0)?'gray9':'' ?>" <?php echo ($k==0)?'readonly':'' ?> value="<?php echo $h['title'] ?>"><input type="hidden" name="type[]" value="<?php echo $h['type'] ?>" /><?php if($k>0){ ?><a href="javascript:;" js='removeZuoye' class="blue ml10">删除</a><?php } ?>
                                        </li>
                                    <?php } ?>
                                    <?php if(count($ratingses)==0){ ?>
                                        <li class="pingfen">评分题
                                            <input name="ratingses[]" type="text" class="iptH37 w600 ml10 gray9" value="您对课程的总体评分" readonly ><input type="hidden" name="type[]" value="1" />
                                        </li>
                                        <li class="pingfen">评分题
                                                <input name="ratingses[]" type="text" class="iptH37 w600 ml10" value="这个课程的目标清楚明确"><input type="hidden" name="type[]" value="1" /><a href="javascript:;" js='removeZuoye' class="blue ml10">删除</a>
                                        </li>
                                        <li class="kaifang">开放题
                                            <input name="ratingses[]" type="text" class="iptH37 w600 ml10" value="您认为本次培训有哪些方面给您留下较深的印象？"><input type="hidden" name="type[]" value="2" /><a href="javascript:;" js='removeZuoye' class="blue ml10">删除</a>
                                        </li>
                                    <?php } ?>
                                </ul>

                                <div class="ml50 mb20"><a href="javascript:;" class="borBlueH37 mr10" js="addPingfen">添加评分题</a><a href="javascript:;" class="borBlueH37" js="addKaifang">添加开放题</a></div>
                                
                                <div class="aCenter"><input type="submit" class="coBtn" value="保存" /></div>
                        </form>
                        </div>

                </div>

        </div>
    </div>
