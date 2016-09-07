<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>/css/kecheng.css" />
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-scrolltofixed-min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('.listBox').delegate('.listCont', 'hover', function() {
            $(this).toggleClass('hover');
        });

        /*精度条*/
        var select = $("#minbeds");
        var slider = $("#slider").slider({
            min: 100,
            max: 1800,
            step: 100,
            range: "min",
            value: 100,
            slide: function(event, ui) {
                select[0].value= (ui.value>1000)?(1000+(ui.value-1000)*5):ui.value;
                calculate();
            }
        });
        $("#minbeds").on("change", function() {
            slider.slider("value", this.selectedIndex);
            calculate();
        });
        /*精度条01*/
        var select01 = $("#minbeds01");
        var slider01 = $("#slider01").slider({
            min: 0,
            max: 6,
            range: "min",
            value: 0,
            slide: function(event, ui) {
                select01[0].selectedIndex = ui.value;
                calculate();
            }
        });
        $("#minbeds01").on("change", function() {
            slider01.slider( "value", this.selectedIndex);
            calculate();
        });

        /*精度条02*/
        var select02 = $("#minbeds02");
        var slider02 = $("#slider02").slider({
            min: 0,
            max: 10,
            range: "min",
            value: 0,
            slide: function(event, ui) {
                select02[0].selectedIndex = ui.value;
                calculate();
            }
        });
        $("#minbeds02").on("change", function() {
            slider02.slider( "value", this.selectedIndex );
            calculate();
        });
        $('.zifeiRight').scrollToFixed({
            marginTop: $('.zifeiBox').offsetTop + 10,
            limit: function() {
                var limit = $('.footer').offset().top - $(this).outerHeight(true) - 30;
                return limit;
            },
            zIndex: 999
        });
        $('#requireSurvey,#trainPlan,#wechat,input[name=discount]').change(function(){calculate();});
    })
    function calculate(){
        var minbeds=$('#minbeds').val()*1;
        var minbeds01=$('#minbeds01').val()*1;
        var minbeds02=$('#minbeds02').val()*1;
        var requireSurvey=($('#requireSurvey').attr('checked'))?1:0;
        var trainPlan=($('#trainPlan').attr('checked'))?1:0;
        var wechat=($('#wechat').attr('checked'))?1:0;
        var discount=$('input[name=discount]:checked').val();
        var numval=minbeds>1000?minbeds*13:minbeds*15;
        var amount=(numval+200*minbeds01+2000*minbeds02+trainPlan*5000+wechat*500)*discount;
        $('.minbedsnum').text(minbeds+'人');
        $('.minbedsAmount').text(numval);
        $('.minbeds01num').text(minbeds01+'个');
        $('.minbeds01Amount').text(200*minbeds01);
        if(minbeds01>0){$('.minbeds01Tr').show();}else{$('.minbeds01Tr').hide();}
        $('.minbeds02num').text(minbeds02+'个');
        $('.minbeds02Amount').text(2000*minbeds02);
        if(minbeds02>0){$('.minbeds02Tr').show();}else{$('.minbeds02Tr').hide();}
        if(requireSurvey>0){$('.surveyTr').show();}else{$('.surveyTr').hide();}
        if(trainPlan>0){$('.trainPlanTr').show();}else{$('.trainPlanTr').hide();}
        if(wechat>0){$('.wechatTr').show();}else{$('.wechatTr').hide();}
        $('#amount').text(amount);

    }
</script>
<div class="wrap">
    <div class="comBox">
        <div class="ttl01 aCenter">资费标准</div>
        <div class="zifeiBox p20">
            <div class="zifeiLeft">
                <div class="ttl01 pt0">基础功能</div>
                <table class="tableA mb20">
                    <col width="20%" />
                    <col width="40%" />
                    <tr>
                        <th>功能</th>
                        <th>内容</th>
                        <th>收费标准</th>
                    </tr>
                    <tr>
                        <td>培训流程管理</td>
                        <td>培训流程管理，课程发布、报名、签到、课前调研、课前公告、短信通知、课后反馈</td>
                        <td rowspan="4">
                            <p class="mb10">1、学员数1000人以下，按<span class="red">1500元</span>/100人/年。<br> 2、学员数1000~5000人，按<span class="red">6500元</span>/500人/年。 <br>3、超过5000人请咨询客服详谈。
                            </p>
                            <p>
                                2017年1月1日前免费。</p>
                        </td>
                    </tr>
                    <tr>
                        <td>公司信息管理</td>
                        <td>公司基本信息管理，员工信息管理</td>

                    </tr>
                    <tr>
                        <td>权限管理</td>
                        <td>员工权限管理</td>

                    </tr>
                    <tr>
                        <td>师资管理</td>
                        <td>师资管理</td>

                    </tr>
                </table>

                <form id="reservation" class="sidebox">
                    <label for="minbeds">学员数</label>
                    <div id='slider' class="sideList">
                        <span class="sliderSpan">100</span>
                        <span class="sliderSpan">&nbsp;</span>
                        <span class="sliderSpan">&nbsp;</span>
                        <span class="sliderSpan">&nbsp;</span>
                        <span class="sliderSpan">500</span>
                        <span class="sliderSpan">&nbsp;</span>
                        <span class="sliderSpan">&nbsp;</span>
                        <span class="sliderSpan">&nbsp;</span>
                        <span class="sliderSpan">&nbsp;</span>
                        <span class="sliderSpan">1000</span>
                        <span class="sliderSpan">&nbsp;</span>
                        <span class="sliderSpan">&nbsp;</span>
                        <span class="sliderSpan">&nbsp;</span>
                        <span class="sliderSpan">3000</span>
                        <span class="sliderSpan">&nbsp;</span>
                        <span class="sliderSpan">&nbsp;</span>
                        <span class="sliderSpan">&nbsp;</span>
                        <span class="sliderSpan" style="width: 25px;">5000</span>
                    </div>
                    <select name="minbeds" id="minbeds" class="sectside">
                        <option value="100">100</option>
                        <option value="200">200</option>
                        <option value="300">300</option>
                        <option value="400">400</option>
                        <option value="500">500</option>
                        <option value="600">600</option>
                        <option value="700">700</option>
                        <option value="800">800</option>
                        <option value="900">900</option>
                        <option value="1000">1000</option>
                        <option value="1500">1500</option>
                        <option value="2000">2000</option>
                        <option value="2500">2500</option>
                        <option value="3000">3000</option>
                        <option value="3500">3500</option>
                        <option value="4000">4000</option>
                        <option value="4500">4500</option>
                        <option value="5000">5000</option>
                    </select>
                </form>
                <div class="ttl01">可选功能</div>
                <table class="tableA mb20">
                    <col width="20%" />
                    <col width="40%" />
                    <tr>
                        <th>功能</th>
                        <th>内容</th>
                        <th>收费标准</th>
                    </tr>
                    <tr>
                        <td>能力模型基础版</td>
                        <td>仅限于可用的基础岗位</td>
                        <td><span class="red">200元</span>/个/年</td>

                    </tr>
                    <tr>
                        <td>能力模型定制版</td>
                        <td>定制按照每个岗位收费</td>
                        <td><span class="red">2000元</span>/个/年</td>
                    </tr>

                </table>

                <form id="reservation01" class="sidebox">
                    <label for="minbeds01">能力模型基础版</label>
                    <div id='slider01' class="sideList" style="width: 400px;">
                        <span class="aRight" style="display: block">6</span>
                    </div>

                    <select name="minbeds01" id="minbeds01" class="sectside">
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>

                    </select>
                </form>

                <form id="reservation02" class="sidebox">
                    <label for="minbeds02">能力模型定制版</label>
                    <div id='slider02' class="sideList" style="width: 400px;">
                        <span class="aRight" style="display: block">10</span>
                    </div>

                    <select name="minbeds02" id="minbeds02" class="sectside">
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>

                    </select>
                </form>

                <table class="tableA mb20">
                    <col width="20%" />
                    <col width="40%" />
                    <tr>
                        <th>功能</th>
                        <th>内容</th>
                        <th>收费标准</th>
                    </tr>
                    <tr>
                        <td>年度需求调研</td>
                        <td>年度需求调研问卷的制作，需求调研的实施与结果收集。</td>
                        <td>
                            2017年1月1日前免费使用。
                        </td>
                    </tr>
                    <tr>
                        <td>年度培训计划</td>
                        <td>年度培训开课安排、预算计划及分析。</td>
                        <td>
                            <span class="red">5000</span>元/年，2017年1月1日前免费使用。
                        </td>

                    </tr>

                </table>
                <div class="sidebox clearfix">
                    <label>年度需求调研:</label>
                    <label class="switch">
                        <input id="requireSurvey" type="checkbox" name="requireSurvey" value="1">
                        <div class="slider round"></div>
                    </label>
                </div>
                <div class="sidebox clearfix">
                    <label>年度培训计划:</label>
                    <label class="switch">
                        <input id="trainPlan" type="checkbox" name="trainPlan" value="1">
                        <div class="slider round"></div>
                    </label>
                </div>

                <table class="tableA mb20">
                    <col width="20%" />
                    <col width="40%" />
                    <tr>
                        <th>功能</th>
                        <th>内容</th>
                        <th>收费标准</th>
                    </tr>
                    <tr>
                        <td>定制微信公众号/ 服务号
                        </td>
                        <td>定制企业专属公众号/服务号，提升使用体验。</td>
                        <td>
                            <span class="red">500元</span>/年。
                        </td>
                    </tr>

                </table>


                <div class="sidebox clearfix">
                    <label>个性化微信号:</label>
                    <label class="switch">
                        <input id="wechat" name="wechat" type="checkbox">
                        <div class="slider round"></div>
                    </label>
                </div>

                <div class="ttl01">后续功能</div>
                <table class="tableA mb20">
                    <col width="20%" />
                    <tr>
                        <th>功能</th>
                        <th>内容</th>

                    </tr>
                    <tr>
                        <td>职业发展通道</td>
                        <td>员工职业发展通道</td>

                    </tr>
                    <tr>
                        <td>人才发展计划</td>
                        <td>梯队建设</td>

                    </tr>
                    <tr>
                        <td>直播平台</td>
                        <td>直播和录播平台，含学员互动和课程管理</td>

                    </tr>
                    <tr>
                        <td>微课开发工具</td>
                        <td>微课的开发工具，快速将PPT和语音转化为课程</td>

                    </tr>
                    <tr>
                        <td>考试</td>
                        <td>考题设计、题库管理、一键发布、考试统计、报表分析</td>

                    </tr>
                    <tr>
                        <td>学员成长轨迹</td>
                        <td>企业私有数据分析</td>

                    </tr>

                    <tr>
                        <td>课后跟进</td>
                        <td></td>

                    </tr>

                </table>
            </div>

            <div class="zifeiRight">
                <div class="ttl01">价格清单：</div>
                <table class="tableB">
                    <col width="40%" />
                    <tr>
                        <td>功能</td>
                        <td>数量</td>
                        <td>价格</td>
                    </tr>
                    <tr>
                        <td>基础功能 </td>
                        <td class="minbedsnum">100人 </td>
                        <td class="red minbedsAmount">1500元</td>
                    </tr>
                    <tr class="minbeds01Tr" style="display: none;">
                        <td>基础能力模型 </td>
                        <td class="minbeds01num">1个 </td>
                        <td class="red minbeds01Amount">200元</td>
                    </tr>
                    <tr class="minbeds02Tr" style="display: none;">
                        <td>定制能力模型 </td>
                        <td>1个 </td>
                        <td class="red minbeds02Amount">2000元</td>
                    </tr>
                    <tr class="surveyTr" style="display: none;">
                        <td>年度需求调研</td>
                        <td>1年</td>
                        <td class="red">免费</td>
                    </tr>
                    <tr class="trainPlanTr" style="display: none;">
                        <td>年度培训计划</td>
                        <td>1年</td>
                        <td class="red trainPlanAmount">5000元</td>
                    </tr>
                    <tr class="wechatTr" style="display: none;">
                        <td>个性化微信号</td>
                        <td>1个</td>
                        <td class="red wechatAmount">500元</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <ul class="listRadio">
                                <li><label><input name="discount" type="radio" checked value="1">一年</label></li>
                                <li><label><input name="discount" type="radio" value="0.9">两年(<span class="red">9</span>折)</label></li>
                                <li><label><input name="discount" type="radio" value="0.85">三年(<span class="red">8.5</span>折)</label></li>
                            </ul>

                        </td>

                    </tr>
                </table>
                <p class="f16 aRight p15">总价：<span class="red" id="amount">1500</span></p>

            </div>

        </div>
    </div>
</div>