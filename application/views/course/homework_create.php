<script type="text/javascript">
        $(document).ready(function(){
                $('[js="addZuoye"]').click(function(){
                        var num=$('.zuoyeList li:last .num').text();
                        var liList=$('.zuoyeList li:last').clone();
                        liList.find('.iptH37').val('');
                        liList.appendTo('.zuoyeList')
                        .find('.num').text(parseInt(parseInt(num)+1));
                })
        })
</script>
<div class="wrap">
        <div class="titCom clearfix"><span class="titSpan">薪酬设计实战训练营  </span><a href="#" class="greenH25">报名中</a></div>
        <div class="topNaviKec">
                <ul class="topNaviUlKec">
                        <li><a href="#">课程信息</a></li>
                        <li><a href="#">报名管理</a></li>
                        <li><a href="#">签到管理</a></li>
                        <li class="cur"><a href="#">课前作业</a></li>
                        <li><a href="#">课程评价</a></li>
                        <li><a href="#">通知设置</a></li>
                </ul>

        </div>
        <div class="comBox clearfix">
                <div class="baoming">

                        <div class="sideLeft">
                                <ul class="sideLnavi">
                                        <li class="cur"><a href="#">作业编辑<i></i></a></li>
                                        <li><a href="#">提交名单<i></i></a></li>
                                </ul>

                        </div>
                        <div class="contRight">
                                <p class="titCom">作业题编辑</p>
                                <p class="f14 mb20 gray6">本课程暂未创建课前作业，请通过以下模板进行创建</p>
                                <ul class="zuoyeList">
                                        <li><span class="num">1</span>
                                                <input type="text" class="iptH37 w600 ml10" value="员工为什么会努力工作？">
                                        </li>
                                        <li><span class="num">2</span>
                                                <input type="text" class="iptH37 w600 ml10" value="企业需要什么样的员工，什么样的员工是优秀的员工？">
                                        </li>
                                </ul>

                                <div><a href="javascript:;" class="borBlueH37" js="addZuoye">新增作业题</a></div>
                                <div class="aCenter"><a href="#" class="coBtn">创建作业题</a></div>
                        </div>

                </div>

        </div>
    </div>
