<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css?<?php echo $this->config->item('version');?>"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css?<?php echo $this->config->item('version');?>"/>
<script>
    $(document).ready(function(){
        $('.closeBtn').click(function () {
            $('#conWindow').hide();
        });
        $('#addSubcategory').click(function(){
            $('#conMessage input[name=act]').val('add');
            $('#conMessage input[name=subcategoryname]').val('');
            $('#title_divSpan').text('创建子分类(<?php echo $types[$model_type] ?>)');
            $('#conWindow').show();
            return false;
        });
        $('#editSubcategory').click(function () {
            $('#conMessage input[name=act]').val('save');
            $('#conMessage input[name=subcategoryname]').val($('.textureSide .fnavi .clink li.on a').text());
            $('#title_divSpan').text('编辑子分类');
            $('#conWindow').show();
        });
        $('a.okBtn').click(function () {
            act = $('#conMessage input[name=act]').val();
            subcategoryid = $('#conMessage input[name=subcategoryid]').val();
            subcategoryname = $('#conMessage input[name=subcategoryname]').val();
            if (act == 'save') {//编辑
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url('abilityentry/savesubcategory/'.$model_type.'/'.$subcategory['id']) ?>',
                    data: {'subcategoryname': subcategoryname},
                    success: function (res) {
                        if (res == 0) {
                            alert('修改失败');
                        }else if(res==-1){
                            alert('子分类已存在');
                        } else {
                            $('.textureSide .fnavi .clink li.on a,.texturetip .categoryname').text(subcategoryname);
                            $('#conWindow').hide();
                        }
                    }
                })
            } else {//新增
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url('abilityentry/addsubcategory/'.$model_type) ?>',
                    data: {'subcategoryname': subcategoryname},
                    success: function (res) {
                        if (res == 0) {
                            alert('添加失败');
                        }else if(res==-1){
                            alert('子分类已存在');
                        }else {
                            $('ul.modtypeChildren<?php echo $model_type ?>').append('<li><a href="<?php echo base_url() ?>abilityentry/index/<?php echo $model_type ?>/' + res + '.html">' + subcategoryname + '</a></li>');
                            $('#conWindow').hide();
                        }
                    }
                })
            }
            return false;
        });
        $('#delSubcategory').click(function () {
            if (confirm('确定删除当前子分类吗?')) {
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url('abilityentry/delsubcategory/'.$subcategory['id']) ?>',
                    success: function (res) {
                        if (res == 0) {
                            if($('.textureSide a.on,.textureSide li.on a').parent().parent().find('a').length>1){
                                window.location=$('.textureSide a.on,.textureSide li.on a').parent().prev().find('a').attr('href');
                            }else{
                                window.location=$('.textureSide a.on,.textureSide li.on a').parent().parent().prev().attr('href');
                            }
                        } else if (res == 1) {
                            alert('删除失败');
                        } else if (res == 2) {
                            alert('含有能力词条无法删除');
                        }
                    }
                });
            }
        });
        $('.delEntry').click(function () {
            if (confirm('确定删除吗?')) {
                var url=$(this).attr('href');
                $.ajax({
                    type: "post",
                    url: url,
                    success: function (res) {
                        if (res == 0) {
                            window.location='<?php echo current_url(); ?>';
                        } else if (res == 1) {
                            alert('删除失败');
                        } else if (res == 2) {
                            alert('能力词条正在使用,无法删除');
                        }
                    }
                });
            }
            return false;
        });
    });
</script>
<div class="wrap">
    <div class="textureCont width100">
        <div class="texturetip p15 clearfix"><span class="fLeft pt5">能力词典管理</span>
        </div>
        <div class="clearfix textureBox noborder">
            <div class="textureSide">
                <?php foreach ($types as $tk => $t){ ?>
                <div class="fnavi mb10">
                    <a href="<?php echo site_url('abilityentry/index/'.$tk) ?>" class="flink <?php echo $model_type == $tk ? 'on' : '' ?>"><?php echo $t ?><i class="iup fa fa-angle-right fa-lg"></i></a>
                    <ul class="clink modtypeChildren<?php echo $tk ?>">
                        <?php if (!empty($subcategories[$tk]['categories'])) {
                            foreach ($subcategories[$tk]['categories'] as $cate) { ?>
                                <li class="<?php echo $subcategory['id'] == $cate['id'] ? 'on' : '' ?>"><a href="<?php echo site_url('abilityentry/index/'.$tk.'/' . $cate['id']) ?>"><?php echo $cate['name'] ?></a>
                                </li>
                            <?php }
                        } ?>
                        <?php if(count($subcategories[$tk]['categories'])>0 && $subcategories[$tk]['isnoassigned']){?>
                            <li class="<?php echo $subcategoryid=='model_type' ? 'on' : '' ?>"><a href="<?php echo site_url('abilityentry/index/'.$tk.'/model_type') ?>">未分配</a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <?php } ?>
            </div>
            <div class="textureCont">
                <div class="texturetip textureWite pt10 clearfix mr20">
                    <span class="fLeft"><?php echo $types[$model_type] ;if(!empty($subcategory)){echo '&nbsp;>&nbsp;<span class="categoryname">'.$subcategory['name'].'</span>';}elseif($subcategoryid=='model_type'){echo '&nbsp;>&nbsp;未分配';}?></span>
                    <div class="fRight">
                            <a id="addSubcategory" class="borBlueBtnH28 w72 aCenter" href="#">创建子分类</a>
                        <?php if(!empty($subcategory)){ ?>
                            <a id="editSubcategory" class="borBlueBtnH28 w72 aCenter" href="#">编辑子分类</a>
                            <a id="delSubcategory" class="borBlueBtnH28 w72 aCenter" href="#">删除子分类</a>
                        <?php } ?>
                        <a class="borBlueBtnH28 w72 aCenter" href="<?php echo site_url('abilityentry/createabilityentry/'.$model_type.'/'.$subcategory['id']) ?>">创建能力词条</a>
                    </div>
                </div>
                <div class="clearfix mr20" style="min-height: 450px;">
                    <?php if (!empty($entries)) { ?>
                        <table cellspacing="0" class="listTable">
                            <colgroup>
                                <col width="35%">
                                <col width="25%">
                                <col width="25%">
                                <col width="15%">
                            </colgroup>
                            <tbody>
                            <tr>
                                <th class="aLeft">能力名称</th>
                                <th class="aCenter">能力类别</th>
                                <th class="aCenter">能力级数</th>
                                <th class="aCenter">操作</th>
                            </tr>
                            <?php foreach ($entries as $e) { ?>
                                <tr>
                                    <td><a class="blue" href="<?php echo site_url('abilityentry/editentry/'.$model_type.'/'.$e['id']) ?>"><?php echo $e['name'] ?></a></td>
                                    <td class="aCenter"><?php echo $types[$e['type']];echo empty($e['category'])?'':'/'.$e['category'] ?></td>
                                    <td class="aCenter"><?php echo $e['level'] ?></td>
                                    <td class="aCenter">
                                        <a class="blue mr5" href="<?php echo site_url('abilityentry/editentry/'.$model_type.'/'.$e['id']) ?>">编辑</a>
                                        <a class="blue delEntry mr5" href="<?php echo site_url('abilityentry/delentry/'.$e['id']) ?>">删除</a>
                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>
                        <?php echo $links ?>
                    <?php }else{ ?>
                        <p class="clearfix f14 p2015">暂未创建能力词条</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!--tankuang de yangshi -->
<div id="conWindow" style="z-index: 99999; display: none;" class="popWinBox">
    <div class="pop_div" style="z-index: 100001;">
        <div class="title_div"><a class="closeBtn" href="javascript:;"><i class="fa fa-close fa-lg"></i></a><span id="title_divSpan"
                                                                                                                  class="title_divText">创建子分类</span>
        </div>
        <div id="conMessage" class="pop_txt01">
            <table class="comTable">
                <col width="150"/>
                <tr>
                    <th>分类名</th>
                    <td class="aLeft">
                        <input name="act" value="add" type="hidden">
                        <input name="subcategoryid" type="hidden" value="<?php echo $subcategory['id'] ?>">
                        <input name="subcategoryname" type="text" value="<?php echo $subcategory['name'] ?>" class="ipt w250"></td>
                </tr>
                <tr>
                    <th></th>
                    <td class="aLeft"><a jsbtn="okBtn" href="javascript:;" class="okBtn">保存</a></td>
                </tr>
            </table>


        </div>

    </div>
    <div class="popmap" style="z-index: 100000;"></div>
</div>