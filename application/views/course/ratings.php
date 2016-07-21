<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css" />
<div class="wrap">
        <div class="titCom clearfix"><span class="titSpan"><?php echo $course['title'] ?>  </span><a href="#" class="<?php echo $course['status_class']; ?>"><?php echo $course['status_str']; ?></a></div>
        <div class="topNaviKec">
                <?php $this->load->view ( 'course/top_navi' ); ?>

        </div>
        <div class="comBox clearfix">
                <div class="baoming">

                        <p class="clearfix f14 mb20">
                                <select class="iptH37 fRight">
                                        <option></option>
                                </select>

                                <span class="pt10 fLeft">共有35人对本次进行评价，综合评分<span class="startBox"><i style="width: 10px;"></i></span></span>
                        </p>
                        <table cellspacing="0" class="listTable">
                                <tbody>
                                        <tr>
                                                <th>姓名</th>
                                                <th>工号</th>
                                                <th>职务</th>
                                                <th>部门</th>
                                                <th>手机</th>
                                                <th>评价星值</th>
                                                <th>意见建议</th>
                                        </tr>
                                        <tr>
                                                <td class="blue">李小军</td>
                                                <td>209987</td>
                                                <td>招聘经理</td>
                                                <td>人力资源部</td>
                                                <td>13901680997</td>
                                                <td><span class="startBox"><i style="width: 10px;"></i></span></td>
                                                <td>
                                                        <p>人力资源部</p>
                                                </td>
                                        </tr>

                                        <tr>
                                                <td class="blue">李小军</td>
                                                <td>209987</td>
                                                <td>招聘经理</td>
                                                <td>人力资源部</td>
                                                <td>13901680997</td>
                                                <td><span class="startBox"><i style="width: 10px;"></i></span></td>
                                                <td>
                                                        <p class="gray9">无建议</p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td class="blue">李小军</td>
                                                <td>209987</td>
                                                <td>招聘经理</td>
                                                <td>人力资源部</td>
                                                <td>13901680997</td>
                                                <td><span class="startBox"><i style="width: 10px;"></i></span></td>
                                                <td>
                                                        <p class="gray9">无建议</p>
                                                </td>
                                        </tr>

                                </tbody>
                        </table>

                        <div class="pageNavi">
                                <ul>
                                        <li class="only"><a href="#">首页</a></li>
                                        <li><a href="#">1</a></li>
                                        <li><span>2</span></li>
                                        <li class="only"><a href="#">下页</a></li>
                                        <li class="only"><a href="#">尾页</a></li>
                                </ul>
                        </div>
                </div>

        </div>
</div>