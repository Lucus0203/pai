<script type="text/javascript">
    $(document).ready(function () {
        serializ();
        $('input[name=hasmanage]').change(function(){
            if($('input[name=hasmanage]:checked').val()==1){
                $('.manageBox').show();
            }else{
                $('.manageBox').hide();
            }
        });
        $('.addProjob').live('click',function(){
            $(this).parent().parent().before('<tr class="proBox"><th>职级1</th><td><input name="name[]" value="" type="text" class="iptH37 w156 mr10"><input name="id[]" value="" type="hidden"><span class="operational"></span></td></tr>');
            serializ();
            return false;
        });
        $('.addMagjob').live('click',function(){
            $(this).parent().parent().before('<tr class="manageBox"><th>职级1</th><td><input name="name[]" value="" type="text" class="iptH37 w156 mr10"><input name="id[]" value="" type="hidden"><span class="operational"></span></td></tr>');
            serializ();
            return false;
        });
        $('.moveup').live('click',function(){
            $(this).parent().parent().parent().prev().before($(this).parent().parent().parent());
            serializ();
            return false;
        });
        $('.movedown').live('click',function(){
            $(this).parent().parent().parent().next().after($(this).parent().parent().parent());
            serializ();
            return false;
        });
        $('.removejob').live('click',function(){
            var joblevelid=$(this).parent().parent().find('input[name^=id]').val();
            obj=$(this).parent().parent().parent();
            if($.trim(joblevelid)!=''){
                if(confirm('确认删除么?')){
                    $.ajax({
                        type: "post",
                        url: '<?php echo base_url().'abilitymanage/deljoblevel/' ?>'+joblevelid,
                        datatype: 'jsonp',
                        success: function (res) {
                            if(res==1){
                                obj.remove();
                                serializ();
                            }else{
                                alert('该职级存在岗位模型,无法删除')
                            }
                        }
                    });
                }
            }else{
                obj.remove();
                serializ();
            }
            return false;
        });
        $("#editForm").validate({
            rules: {
                series_name: {
                    required: true
                }
            },
            messages: {
                series_name: {
                    required: "请输入系列名称"
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
                var flag=false;
                $('input[name^=name]').each(function(i){
                    if($.trim($(this).val())!=''){
                        flag=true;
                    }
                });
                if(flag){
                    $('input[type=submit]').val('请稍后..').attr('disabled', 'disabled');
                    form.submit();
                }else{
                    alert('至少需要一个职级');
                }
            }
        });
    });

    //序列化顺序
    function serializ(){
        $('.proBox input[name^=name]').each(function(i){
            var level=i+1;
            $(this).parent().prev().html('职级'+level+'<input type="hidden" name="level[]" value="'+level+'" /><input type="hidden" name="series_type[]" value="1" /> ');
            var operationhtml='';
            if($('.proBox input[name^=name]').length>1){
                operationhtml='<a class="blue removejob" href="#">删除</a>';
                if(level==1){
                    operationhtml='<a class="blue movedown mr20" href="#">下移</a>'+operationhtml;
                }else if(level==$('.proBox input[name^=name]').length){
                    operationhtml='<a class="blue moveup mr20" href="#">上移</a>'+operationhtml;
                }else{
                    operationhtml='<a class="blue moveup mr20" href="#">上移</a><a class="blue movedown mr20" href="#">下移</a>'+operationhtml;
                }
            }
            $(this).parent().find('.operational').html(operationhtml);
        });
        $('.manageBox input[name^=name]').each(function(i){
            var level=i+1;
            $(this).parent().prev().html('职级'+level+'<input type="hidden" name="level[]" value="'+level+'" /><input type="hidden" name="series_type[]" value="2" /> ');
            var operationhtml='';
            if($('.manageBox input[name^=name]').length>1) {
                operationhtml='<a class="blue removejob" href="#">删除</a>';
                if (level == 1) {
                    operationhtml = '<a class="blue movedown mr20" href="#">下移</a>' + operationhtml;
                } else if (level == $('.manageBox input[name^=name]').length) {
                    operationhtml = '<a class="blue moveup mr20" href="#">上移</a>' + operationhtml;
                } else {
                    operationhtml = '<a class="blue moveup mr20" href="#">上移</a><a class="blue movedown mr20" href="#">下移</a>' + operationhtml;
                }
            }
            $(this).parent().find('.operational').html(operationhtml);
        });
    }
</script>
<div class="wrap">
    <div class="titCom clearfix"><span class="titSpan"><?php echo empty($series) ? '新增岗位系列' : '编辑岗位系列' ?></span></div>
    <div class="comBox">
        <div class="tableBox">
            <form id="editForm" method="post" action="" enctype="multipart/form-data">
                <input name="act" type="hidden" value="act"/>
                <table cellspacing="0" class="comTable">
                    <col width="20%"/>
                    <tr>
                        <th>系列名称</th>
                        <td>
                            <span class="iptInner">
                            <input name="series_name" value="<?php echo $series['name'] ?>" type="text" class="iptH37 w156 mr10" placeholder="请输入系列名称">
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th class="f16 gray9">专业系</th>
                        <td></td>
                    </tr>
                    <?php if(count($projob)>0){
                        foreach ($projob as $j){ ?>
                        <tr class="proBox">
                            <th>职级</th>
                            <td>
                                <input name="name[]" value="<?php echo $j['name'] ?>" type="text" class="iptH37 w156 mr10">
                                <input name="id[]" value="<?php echo $j['id'] ?>" type="hidden"><span class="operational"></span>
                            </td>
                        </tr>
                    <?php } }else{ ?>
                    <tr class="proBox">
                        <th>职级1</th>
                        <td>
                            <input name="name[]" value="" type="text" class="iptH37 w156 mr10"><span class="operational"></span>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th></th>
                        <td>
                            <a class="blue addProjob" href="#">+添加职级</a>
                        </td>
                    </tr>
                    <tr>
                        <th class="f16 gray9">管理系</th>
                        <td>
                            <ul class="lineUl">
                                <li><label><input name="hasmanage" value="1" type="radio" checked="checked" >有</label></li>
                                <li><label><input name="hasmanage" value="2" type="radio" <?php if($series['hasmanage']==2){?>checked="checked"<?php } ?>>无</label></li>
                            </ul>
                        </td>
                    </tr>

                    <?php if(count($magjob)>0){
                    foreach ($magjob as $j){ ?>
                        <tr class="manageBox" <?php if($series['hasmanage']==2){?>style="display: none;" <?php } ?>>
                            <th>职级1</th>
                            <td>
                                <input name="name[]" value="<?php echo $j['name'] ?>" type="text" class="iptH37 w156 mr10">
                                <input name="id[]" value="<?php echo $j['id'] ?>" type="hidden"><span class="operational"></span>
                            </td>
                        </tr>
                    <?php } }else{ ?>
                    <tr class="manageBox" <?php if($series['hasmanage']==2){?>style="display: none;" <?php } ?>>
                        <th>职级1</th>
                        <td>
                            <input name="name[]" value="" type="text" class="iptH37 w156 mr10"><span class="operational"></span>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr class="manageBox" <?php if($series['hasmanage']==2){?>style="display: none;" <?php } ?>>
                        <th></th>
                        <td>
                            <a class="blue addMagjob" href="#">+添加职级</a>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
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
