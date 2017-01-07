<script type="text/javascript">
    $(document).ready(function () {
        $("#editForm").validate({
            rules: {
                name: {
                    required: true
                },
                info: {
                    required: true
                },
                level_info1:{
                    required: true
                },
                level_info2:{
                    required: true
                },
                level_info3:{
                    required: true
                },
                level_info4:{
                    required: true
                },
                level_info5:{
                    required: true
                }
            },
            messages: {
                name: {
                    required: "请输入能力名称"
                },
                info: {
                    required: "请输入能力描述"
                },
                level_info1:{
                    required: "请输入级别描述"
                },
                level_info2:{
                    required: "请输入级别描述"
                },
                level_info3:{
                    required: "请输入级别描述"
                },
                level_info4:{
                    required: "请输入级别描述"
                },
                level_info5:{
                    required: "请输入级别描述"
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
                $('input[name=level]').val($('textarea[name^=level_info]').length);
                $('input[type=submit]').val('请稍后..').attr('disabled', 'disabled');
                form.submit();
            }
        });
        $('.addLevelInfo').click(function(){
            var i=$('textarea[name^=level_info]').length+1;
            $(this).parent().parent().before('<tr><th><span class="red">*</span>级别'+i+'</th>'+
                '<td><span class="iptInner mr10">'+
                '<textarea name="level_info'+i+'" class="iptare pt10" placeholder="请输入级别描述"></textarea>'+
                '</span><a href="#" class="blue removeLevelInfo ml10">删除</a></td></tr>');
            if(i==5){
                $('.addLevelInfo').hide();
            }
            return false;
        });
        $('.removeLevelInfo').live('click',function(){
            $(this).parent().parent().remove();
            if($('textarea[name^=level_info]').length<5){
                $('.addLevelInfo').show();
            }
            return false;
        });
//        $('select[name=type]').change(function(){
//            var type=$(this).val();
//            $.ajax({
//                type: "post",
//                url: '<?php //echo site_url('abilityentry/getcategories') ?>//',
//                data: {'type': type},
//                dateType:'jsonp',
//                success: function (res) {
//                    var json_obj = $.parseJSON(res);
//                    var str='<option value="">请选择</option>';
//                    var categories='';
//                    $.each(json_obj,function(i,item){
//                        categories+='<option value="'+item.id+'">'+item.name+'</option>';
//                    });
//                    if(categories!=''){
//                        $('select[name=ability_subcategory_id]').html(str+categories).show();
//                    }else{
//                        $('select[name=ability_subcategory_id]').html(str).hide();
//                    }
//                }
//            });
//        });

    });
</script>
<div class="wrap">
    <div class="titCom clearfix"><span class="titSpan"><?php echo empty($entry) ? '创建能力词条' : '编辑能力词条' ?></span></div>
    <div class="comBox">
        <?php if (!empty($msg)) {?>
            <p class="alertBox alert-success"><span class="alert-msg"><?php echo $msg ?></span><a href="javascript:;" class="alert-remove">X</a></p>
        <?php } ?>
        <div class="tableBox">
            <form id="editForm" method="post" action="" enctype="multipart/form-data">
                <input name="act" type="hidden" value="act"/>
                <input name="level" type="hidden" value=""/>
                <table cellspacing="0" class="comTable">
                    <col width="20%"/>
                    <tr>
                        <th><span class="red">*</span>能力名称</th>
                        <td>
                            <span class="iptInner">
                            <input name="name" value="<?php echo $entry['name'] ?>" type="text" class="iptH37 w345" placeholder="请输入能力名称">
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>能力类别</th>
                        <td>
                            <input name="type" type="hidden" value="<?php echo $model_type ?>" /><?php echo $types[$model_type] ?>
                        </td>
                    </tr>
                    <tr <?php if(empty($subcategories)){echo 'style="display:none;"';}?>>
                        <th>子分类</th>
                        <td>
                            <select name="ability_subcategory_id" class="iptH37 w156" >
                                <option value="">请选择</option>
                                <?php foreach ($subcategories as $k=>$s){ ?>
                                    <option <?php if($subcategoryid==$s['id']||$entry['ability_subcategory_id']==$s['id']) echo 'selected'; ?> value="<?php echo $s['id'] ?>"><?php echo $s['name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>能力描述</th>
                        <td>
                            <span class="iptInner">
                            <textarea name="info" class="iptare pt10" placeholder="请输入能力描述"><?php echo $entry['info'] ?></textarea>
                            </span>
                        </td>
                    </tr>
                    <?php $level=!empty($entry['level'])?$entry['level']:3;
                        for($i=1;$i<=$level;$i++){ ?>
                            <tr>
                                <th><span class="red">*</span>级别<?php echo $i ?></th>
                                <td>
                                    <span class="iptInner">
                                    <textarea name="level_info<?php echo $i ?>" class="iptare pt10" placeholder="请输入级别描述"><?php echo $entry['level_info'.$i] ?></textarea>
                                    </span><?php if($i>3){?><a href="#" class="blue removeLevelInfo ml10">删除</a><?php } ?>
                                </td>
                            </tr>
                    <?php } ?>
                    <tr>
                        <th></th>
                        <td>
                            <a class="blue addLevelInfo" href="#">+添加级别</a>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="保存" class="coBtn mr30">
                            <input type="button" value="返回" class="coBtn" onclick="history.back(-1);">
                        </td>
                    </tr>
                </table>
        </div>

    </div>
</div>