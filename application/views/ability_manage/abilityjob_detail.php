<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css?<?php echo $this->config->item('version');?>"/>
<script type="text/javascript" src="<?php echo base_url(); ?>js/Chart.bundle.min.js"></script>
<script type="text/javascript"  src="<?php echo base_url() ?>js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-scrolltofixed-min.js"></script>
<style>
    canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }
</style>
<script>
    var currentCategory=1;
    var editflag=false;
    $(function(){
        $(document).tooltip({show: { delay: 500 }});
        var config = {
            type: 'radar',
            data: {
                labels: [<?php if(array_key_exists(1,$abilities)){?>"专业/技能",<?php } ?> <?php if(array_key_exists(3,$abilities)){?>"领导力",<?php } ?><?php if(array_key_exists(5,$abilities)){?>"经验",<?php } ?><?php if(array_key_exists(4,$abilities)){?>"个性",<?php } ?><?php if(array_key_exists(2,$abilities)){?>"通用",<?php } ?>],
                datasets: [{
                    label:'<?php echo $abilityjob['name'] ?>',
                    backgroundColor: "rgba(156,224,234,0.7)",
                    pointBackgroundColor: "rgba(220,220,220,1)",
                    data: [<?php if(array_key_exists('1',$levelradar)){ echo round($levelradar[1]['level_standard']/$levelradar[1]['level_total']*5,1); ?>,<?php } if(array_key_exists('3',$levelradar)){ echo round($levelradar[3]['level_standard']/$levelradar[3]['level_total']*5,1); ?>,<?php } if(array_key_exists('5',$levelradar)){ echo round($levelradar[5]['level_standard']/$levelradar[5]['level_total']*5,1); ?>,<?php } if(array_key_exists('4',$levelradar)){ echo round($levelradar[4]['level_standard']/$levelradar[4]['level_total']*5,1); ?>,<?php } if(array_key_exists('2',$levelradar)){ echo round($levelradar[2]['level_standard']/$levelradar[2]['level_total']*5,1); } ?>]
                }]
            },
            options: {
                legend: {
                    //display:false
                    labels:{boxWidth: 20}
                },
                scale: {
                    ticks: {
                        beginAtZero: true,
                        backdropColor:'rgba(255, 255, 255, 0)'
                    }
                }
            }
        };
        window.myRadar = new Chart(document.getElementById("canvas"), config);

        $('ul.star li').live('click',function(){
            var i=$(this).parent().find('li').index($(this));
            $(this).addClass('cur yellow').siblings().removeClass('cur yellow');
            var starBox=$(this).parent().parent();
            starBox.find('.starTxt').hide().eq(i).show();
            starBox.prev().find('.company_model_level').val(i+1);
            resetRadarData();
            if($('#newAbilityStandard').hasClass('borGaryBtnH28')){
                $('#newAbilityStandard').removeClass('borGaryBtnH28').addClass('borBlueBtnH28');
            }
            editflag=true;
            return false;
        });
        function resetRadarData(){
            var data=[0,0,0,0,0,0];
            $('.company_model_type').each(function(i){
                var type=$(this).val()*1;
                data[type]+=$('.company_model_level').eq(i).val()*1;
            });
            for(i in data){
                if($('.starType'+i+' li').length>0){
                    data[i]=data[i]/$('.starType'+i+' li').length*5;
                }
            }
            var radardata=new Array();
            radardata[0]=data[1]<1?1:data[1].toFixed(2);
            radardata[1]=data[3]<1?<?php echo array_key_exists('3',$levelradar)?1:0; ?>:data[3].toFixed(2);
            radardata[2]=data[5]<1?1:data[5].toFixed(2);
            radardata[3]=data[4]<1?1:data[4].toFixed(2);
            radardata[4]=data[2]<1?1:data[2].toFixed(2);
            radardata=radardata.filter(function(n){return n});
            config.data.datasets[0].data=radardata;
            window.myRadar.update();

        }
        $('.nengliRight,.sideLeft').scrollToFixed({
            marginTop: $('.nengli').offsetTop + 10,
            limit: function() {
                var limit = $('.footer').offset().top - $(this).outerHeight(true) - 30;
                return limit;
            },
            zIndex: 999
        });
        var sidenaviClick=false;
        $('.sideLnavi li').click(function(){
            sidenaviClick=true;
            var i=$('.sideLnavi li').index($(this));
            $(this).addClass('cur').find('i').show();
            $(this).siblings().removeClass('cur').find('i').hide();
            $('html body').stop().animate({scrollTop:$('.model').eq(i).offset().top},500,function(){
                sidenaviClick=false;
            });
            return false;
        });
        $(window).scroll(function(){
            if(!sidenaviClick){
                $('.model').each(function(i){
                    if($(this).offset().top-$(window).scrollTop()<100){
                        $('.sideLnavi li').eq(i).addClass('cur').find('i').show();
                        $('.sideLnavi li').eq(i).siblings().removeClass('cur').find('i').hide();
                    }
                });
            }
        });
        //添加词条
        $('.chosenEntry').click(function(){
            var model_type=$(this).attr('rel');
            currentCategory=model_type;
            $.ajax({
                type: "post",
                url: '<?php echo site_url('abilitymanage/getcateoriesentries') ?>',
                data: {'model_type': model_type},
                async: false,
                datatype: 'jsonp',
                success: function (res) {
                    if(res!=0){
                        var json_obj = $.parseJSON(res);
                        if(json_obj.categries.length>0){
                            var str='<option value="">选择分类</option>';
                            $.each(json_obj.categries, function (i, item) {
                                str+='<option value="'+item.id+'">'+item.name+'</option>';
                            });
                            $('#subcategory_id').html(str).show();
                        }else{
                            $('#subcategory_id').hide();
                        }
                        str = '';
                        if(json_obj.entries.length>0) {
                            var str='';
                            $.each(json_obj.entries, function (i, item) {
                                if($('input.company_model_id[value='+item.id+']').length<=0){
                                    str += '<li class="subcategory_'+item.category+'"><input type="checkbox" value="' + item.id + '"  /><span title="' + item.info + '">' + item.name + '</span></li>';
                                }
                            });
                            if(str!=''){
                                $('#conMessage .secBox ul').html(str);
                            }else{
                                $('#conMessage .secBox ul').html('<li class="gray9">没有词条可以添加</li>');
                            }
                        }else{
                            $('#conMessage .secBox ul').html('');
                        }
                        $('#conWindow').show();
                    }
                }
            });
            return false;
        });
        $('#editEntryBtn').toggle(function(){
            $(this).text('取消编辑');
            $('.delEntryBtn,.chosenEntry,.operating').show();
            return false;
        },function(){
            $(this).text('编辑词典');
            $('.delEntryBtn,.chosenEntry,.operating').hide();
            return false;
        });
        $('#cancel').click(function(){
            $('.delEntryBtn,.chosenEntry,.operating').hide();
            return false;
        })
        $('.delEntryBtn').live('click',function(){
            var mobj=$(this).parent().parent().parent();
            $(this).parent().parent().next().remove();
            $(this).parent().parent().remove();
            //重新排序
            serialization();
            return false;
        });
        $('#subcategory_id').change(function(){
            filterEntries();
        });
        $('#filter_keyword').keyup(function(){
            filterEntries();
        });
        $('.okBtn').click(function(){
            var str='';
            $('#conMessage .secBox ul li:visible input:checked').each(function(i){
                str+=$(this).val()+',';
            });
            if(str!=''){
                str=str.slice(0,-1);
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url('abilitymanage/getentries') ?>',
                    data: {'entryids': str},
                    async: false,
                    datatype: 'jsonp',
                    success: function (res) {
                        if(res!='0'){
                            var json_obj = $.parseJSON(res);
                            if(json_obj.entries.length>0) {
                                var str='';
                                $.each(json_obj.entries, function (i, item) {
                                    var n='';
                                    var p='';
                                    for(var j=1;j<=item.level;j++){
                                        n+='<li><a href="#"><i class="fa fa-star fa-3x"></i><span class="num">'+j+'</span></a></li>';
                                        p+='<p class="starTxt" style="display: none;" >'+item['level_info'+j].replace(/\n/g, "<br />")+'</p>';
                                    }
                                    str += '<p class="txt">'+
                                        '<input class="company_model_id" type="hidden" value="'+item.id+'">'+
                                        '<input class="company_model_type" type="hidden" value="'+item.type+'">'+
                                        '<input class="company_model_level" type="hidden" value="">'+
                                        '<span>'+($('.starBox').length+i+1)*1+'、'+item.name+'<a href="#" class="delEntryBtn blue f16 ml20">删除词条</a></span>'+item.info.replace(/\n/g, "<br />")+'</p>'+
                                        '<div class="starBox mb20">'+
                                        '<ul class="star starType'+item.type+'">'+n+'</ul>'+
                                        p+
                                        '</div>';
                                });
                                $('.category'+currentCategory).before(str);
                                serialization();
                            }
                        }
                    }
                });
            }
            $('#conWindow').hide();
            return false;
        });
        $('#popConClose').click(function(){
            $('#conWindow').hide();
            return false;
        });

        //保存数据
        $('#save').click(function(){
            var levels=types=mids='';
            var flag=true;
            $('.model').each(function(){
                if($(this).find('.starBox').length<=0){
                    $('html body').scrollTop($(this).offset().top-30);
                    alert('请为['+$(this).find('.blueline span').text()+']添加能力词条');
                    flag = false;
                    return false;
                }
            });
            if(!flag){
                return flag;
            }
            $(".company_model_level").each(function(){
                if($(this).val()==''){
                    $('html body').scrollTop($(this).parent().offset().top-30);
                    var str=$(this).next().html();
                    str=str.slice(str.indexOf('、')+1,str.indexOf('<a'));
                    alert('请选择['+str+']级别标准');
                    flag = false;
                    return false;
                }
                levels+=levels==''?$(this).val():','+$(this).val();
            });
            if(!flag){
                return flag;
            }
            $(".company_model_type").each(function(){
                types+=types==''?$(this).val():','+$(this).val();
            });
            $(".company_model_id").each(function(){
                mids+=mids==''?$(this).val():','+$(this).val();
            });
            $.ajax({
                url:'<?php echo site_url('abilitymanage/saveabilityjobentry/'.$abilityjob['id']) ?>',
                type:'post',
                data:{'levels':levels,'types':types,'mids':mids},
                dataType:"json",
                success:function(res){
                    if(res.success!='ok'){
                        $('.alert-danger .alert-msg').text(res.msg).parent().show();
                        $('html,body').scrollTop(0);
                    }else{
                        $('ul.star li.yellow').addClass('blue').siblings().removeClass('blue');
                        $('.alert-success .alert-msg').text('保存成功!').parent().show();
                        $(window).scrollTop(0);
                    }
                }
            });
            return false;
        });
    });
    //重新排序
    function serialization(){
        $('div.model').each(function(){
            $(this).find('.txt span').each(function(i){
                var str=$(this).html()+'';
                str=(i+1)+str.slice(str.indexOf('、'));
                $(this).html(str);
            });
        });
    }
    //过滤词条
    function filterEntries(){
        var catevalue=$('#subcategory_id').val();
        var keyword=$('#filter_keyword').val();
        $('#conMessage .secBox ul li').show();
        if(catevalue!=''){
            $('#conMessage .secBox ul li').hide();
            $('.subcategory_'+catevalue).show();
        }
        $('#conMessage .secBox ul li:visible').each(function(i){
            var t=$(this).find('span').text();
            if(t.indexOf(keyword)==-1){
                $(this).hide();
            }
        });
    }
</script>
<div class="wrap">
    <div class="titCom clearfix"><span class="titSpan" ><?php echo $abilityjob['name'] ?></span>
        <?php $this->load->view ( 'ability_manage/top_tit' ); ?>
    </div>
    <div class="topNaviKec01">
        <div class="fRight">
            <a id="editEntryBtn" class="borBlueH37 mt5 mr5" href="#">编辑词典</a>
        </div>
        <?php $this->load->view ( 'ability_manage/top_navi' ); ?>
    </div>
    <div class="comBox">
        <div class="baoming" style="padding: 0 20px;">
            <div class="sideLeft" style="margin-top: 10px;">
                <ul class="sideLnavi">
                    <?php foreach ($types as $k=>$t){ ?>
                        <li class="<?php if($k==1){echo 'cur';} ?>" style="padding-left: 20px;"><a href="#"><?php echo $t ?><i style="margin-left: <?php if($k<3){echo '20';}else{echo $k>3?'48':'34';} ?>px;<?php if($k>1){echo 'display:none;';} ?>" class="fa fa-angle-right fa-lg"></i></a></li>
                    <?php } ?>
                </ul>
            </div>
            <div class="contRight" style="width:82%;">
                <div class="nengli" style="padding:0;">
                    <div class="nengliRight pt10" style="width: 28%;">
                        <div style="width: 425px;height:300px; margin:0 0 0 -100px;">
                            <canvas id="canvas"></canvas>
                        </div>
                    </div>
                    <div class="nengliLeft" style="padding-top: 10px;">
                        <p class="alertBox alert-success " style="display: none;"><span class="alert-msg">保存成功!</span><a href="javascript:;" class="alert-remove">X</a></p>
                        <p class="alertBox alert-danger" style="display: none;"><span class="alert-msg">保存失败!</span><a href="javascript:;" class="alert-remove">X</a></p>
                        <?php foreach ($abilities as $key=>$abily) { ?>
                            <div class="model">
                                <p class="blueline"><span><?php echo $abily['type'] ?></span></p>
                                <?php foreach ($abily['abilities'] as $k=>$a){ ?>
                                    <p class="txt">
                                        <input class="company_model_id" type="hidden" value="<?php echo $a['id'] ?>">
                                        <input class="company_model_type" type="hidden" value="<?php echo $a['type'] ?>">
                                        <input class="company_model_level" type="hidden" value="<?php echo $a['level_standard'] ?>">
                                        <span><?php echo ($k+1)?>、<?php echo$a['name'] ?><a href="#" class="delEntryBtn blue f16 ml20" <?php if($entries_count>0){echo 'style="display:none;"';}?>>删除词条</a></span>
                                        <?php echo nl2br($a['info']) ?>
                                    </p>
                                    <div class="starBox mb20">
                                        <ul class="star starType<?php echo $a['type'] ?>">
                                            <?php for($i=1;$i<=$a['level'];$i++){?>
                                                <li class="<?php if($i==$a['level_standard']){ echo 'cur blue'; } ?>" >
                                                    <a href="#"><i class="fa fa-star fa-3x"></i><span class="num"><?php echo $i ?></span></a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                        <?php for($i=1;$i<=$a['level'];$i++){?>
                                            <p class="starTxt" <?php if($i!=$a['level_standard']){ ?>style="display: none;"<?php } ?> ><?php echo nl2br($a['level_info'.$i]) ?></p>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <p class="txt category<?php echo $key ?> mb30"><a href="#" class="blue chosenEntry" rel="<?php echo $key ?>" <?php if($entries_count>0){echo 'style="display:none;"';}?> >+添加能力词条</a></p>
                            </div>
                        <?php } ?>
                        <p class="aCenter operating p40" <?php if($entries_count>0){echo 'style="display:none;"';}?> >
                            <input id="save" type="button" class="coBtn" value="保存">
                            <input id="cancel" type="button" class="coBtn ml20" value="取消" />
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<div id="conWindow" style="z-index: 1009;display: none;" class="popWinBox">
    <div class="pop_div" style="z-index: 1001;width: 400px;margin-left: -250px;">
        <div class="title_div">
            <a class="closeBtn" id="popConClose" href="javascript:;"><i class="fa fa-close fa-lg"></i></a>
            <span id="title_divSpan" class="title_divText">添加能力词条</span>
        </div>
        <div class="title_div">
            <select id="subcategory_id" class="iptH37 w184 mr5" >
                <option value="">选择分类</option>
            </select>
            <input id="filter_keyword" type="text" value="" placeholder="关键字" class="iptH37 w184" />
        </div>
        <div id="conMessage" class="pop_txt01">
            <div class="secBox">
                <ul style="width: 100%;"></ul>
            </div>
            <ul class="com_btn_list clearfix">
                <li><a class="okBtn" href="javascript:void(0);" >确定</a></li>
            </ul>
        </div>

    </div>
    <div class="popmap" style="z-index: 1000;"></div>
</div>