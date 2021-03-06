<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css?<?php echo $this->config->item('version');?>"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css?<?php echo $this->config->item('version');?>"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/print.css"/>
<script src="<?php echo base_url(); ?>js/highcharts/highcharts.js"></script>
<script src="<?php echo base_url(); ?>js/highcharts/modules/data.js"></script>
<script src="<?php echo base_url(); ?>js/highcharts/modules/exporting.js"></script>
<script type="text/javascript">
    $(function () {
        <?php if(count($departmentcourse)>0){ ?>
        //部门
        $('#dpcontainer1').highcharts({
            colors:['#36a2eb', '#91e8e1', '#ffce56', '#ff8e72','#bc8500', '#45b7cd', '#36A2EB', '#af7cad', '#ff6384', '#cc65fe'],
            data: {
                table: 'dpdatatable1'
            },
            chart: {
                type: 'column'
            },
            title: {
                text: '部门课程统计'
            },
            yAxis: {
                allowDecimals: false,
                title: {
                    text: '课程数'
                }
            },
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y}'
                    }
                }
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.series.name + '</b><br/>' +
                        this.point.y + ' ' + this.point.name.toLowerCase();
                }
            },
            credits:{enabled:false}//highcharts label hidden
        });
        $('#dpcontainer2').highcharts({
            colors:['#36a2eb', '#91e8e1', '#ffce56', '#ff8e72','#bc8500', '#45b7cd', '#36A2EB', '#af7cad', '#ff6384', '#cc65fe'],
            data: {
                table: 'dpdatatable2'
            },
            chart: {
                type: 'column'
            },
            title: {
                text: '部门培训人次统计'
            },
            yAxis: {
                allowDecimals: false,
                title: {
                    text: '人次'
                }
            },
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y}'
                    }
                }
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.series.name + '</b><br/>' +
                        this.point.y + ' ' + this.point.name.toLowerCase();
                }
            },
            credits:{enabled:false}//highcharts label hidden
        });

        $('#dpcontainer3').highcharts({
            colors:['#36a2eb', '#91e8e1', '#ffce56', '#ff8e72','#bc8500', '#45b7cd', '#36A2EB', '#af7cad', '#ff6384', '#cc65fe'],
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: '部门培训预算分布图'
            },
            tooltip: {
                pointFormat: '预算: <b>{point.y}元</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: 'Brands',
                colorByPoint: true,
                data: [
                    <?php foreach ($departmentcourse as $c){ ?>
                    {
                        name: '<?php echo $c['department'] ?>',
                        y: <?php echo round($c['price_total']) ?>
                    }<?php if($c!==end($courses)){ echo ','; } ?>
                    <?php } ?>]
            }],
            credits:{enabled:false}//highcharts label hidden
        });
        <?php } ?>
        //课程数
        $('#container1').highcharts({
            colors:['#36a2eb', '#91e8e1', '#ffce56', '#ff8e72','#bc8500', '#45b7cd', '#36A2EB', '#af7cad', '#ff6384', '#cc65fe'],
            data: {
                table: 'datatable1'
            },
            chart: {
                type: 'column'
            },
            title: {
                text: '课程统计图'
            },
            yAxis: {
                allowDecimals: false,
                title: {
                    text: '课程数'
                }
            },
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y}'
                    }
                }
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.series.name + '</b><br/>' +
                        this.point.y + ' ' + this.point.name.toLowerCase();
                }
            },
            credits:{enabled:false}//highcharts label hidden
        });

        //培训人次
        $('#container2').highcharts({
            colors:['#36a2eb', '#91e8e1', '#ffce56', '#ff8e72','#bc8500', '#45b7cd', '#36A2EB', '#af7cad', '#ff6384', '#cc65fe'],
            data: {
                table: 'datatable2'
            },
            chart: {
                type: 'column'
            },
            title: {
                text: '课程人次'
            },
            yAxis: {
                allowDecimals: false,
                title: {
                    text: '人次'
                }
            },
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y}'
                    }
                }
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.series.name + '</b><br/>' +
                        this.point.y + ' ' + this.point.name.toLowerCase();
                }
            },
            credits:{enabled:false}//highcharts label hidden
        });

        //课程预算
        $('#container3').highcharts({
            colors:['#36a2eb', '#91e8e1', '#ffce56', '#ff8e72','#bc8500', '#45b7cd', '#36A2EB', '#af7cad', '#ff6384', '#cc65fe'],
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: '课程预算分布图'
            },
            tooltip: {
                pointFormat: '预算: <b>{point.y}元</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: 'Brands',
                colorByPoint: true,
                data: [
                <?php foreach ($courses as $c){ ?>
                    {
                        name: '<?php echo $c['type_name'] ?>',
                        y: <?php echo round($c['price_num']) ?>
                    }<?php if($c!==end($courses)){ echo ','; } ?>
                <?php } ?>]
            }],
            credits:{enabled:false}//highcharts label hidden
        });
        //课程趋势
        $('#container4').highcharts({
            colors:['#36a2eb', '#91e8e1', '#ffce56', '#ff8e72','#bc8500', '#45b7cd', '#36A2EB', '#af7cad', '#ff6384', '#cc65fe'],
            chart: {
                type: 'line'
            },
            title: {
                text: '月课程趋势图'
            },
            xAxis: {
                type : 'linear',
                categories: [
                    <?php foreach($datatrend as $tk=>$t){
                        $ym=substr($tk,0,4).'.'.substr($tk,-2);
                        echo "'$ym',";
                    } ?>
                ]
            },
            yAxis: {
                title: {
                    text: '课程数'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                }
            },
            series: [{
                name: '课程趋势',
                data: [
                    <?php foreach($datatrend as $tk=>$t){
                        echo $t.',';
                    } ?>]
            }],
            credits:{enabled:false}//highcharts label hidden
        });
    });
</script>
<div class="wrap">
    <div class="titCom clearfix">
        <span class="titSpan"><?php echo $plan['title'] ?></span>
    </div>

    <div class="topNaviKec01">
        <?php $this->load->view ( 'annual_plan/top_navi' ); ?>
        <ul class="fRight proPrint">
            <li>
                <a href="javascript:window.print();" class="blue"><i class="fa fa-print fa-lg mr5"></i>打印</a>
            </li>
        </ul>
    </div>

    <div class="clearfix textureBox">
        <div class="p15">

            <div class="clearfix mr20">
                <p class="f24 aCenter mb30"><?php echo $plan['title'] ?></p>
                <?php if(count($departmentcourse)>0){ ?>
                    <div class="aCenter mb20"><div id="dpcontainer1" style="min-width: 310px; height: 400px; margin: 0 auto"></div></div>
                    <div class="ml20 mr20" style="margin-bottom: 150px;">
                        <table id="dpdatatable1" class="tableC">
                            <tbody>
                            <tr>
                                <th>部门名称</th>
                                <?php foreach ($departmentcourse as $dc){ ?>
                                    <th><?php echo $dc['department'] ?></th>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style="border-bottom: none;">课程数</td>
                                <?php foreach ($departmentcourse as $dc){ ?>
                                    <td style="border-bottom: none;"><?php echo round($dc['course_num']) ?></td>
                                <?php } ?>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="aCenter mb20"><div id="dpcontainer2" style="min-width: 310px; height: 400px; margin: 0 auto"></div></div>
                    <div class="ml20 mr20" style="margin-bottom: 150px;">
                        <table id="dpdatatable2" class="tableC">
                            <tbody>
                            <tr>
                                <th>部门名称</th>
                                <?php foreach ($departmentcourse as $dc){ ?>
                                    <th><?php echo $dc['department'] ?></th>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style="border-bottom: none;">课程人次</td>
                                <?php foreach ($departmentcourse as $dc){ ?>
                                    <td style="border-bottom: none;"><?php echo round($dc['people_num']) ?></td>
                                <?php } ?>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="aCenter mb20"><div id="dpcontainer3" style="min-width: 310px; height: 400px; margin: 0 auto"></div></div>
                    <div class="ml20 mr20" style="margin-bottom: 150px;">
                        <table id="dpdatatable3" class="tableC">
                            <tbody>
                            <tr>
                                <th>部门名称</th>
                                <?php foreach ($departmentcourse as $dc){ ?>
                                    <th><?php echo $dc['department'] ?></th>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style="border-bottom: none;">预算</td>
                                <?php foreach ($departmentcourse as $dc){ ?>
                                    <td style="border-bottom: none;"><?php echo round($dc['price_total']) ?></td>
                                <?php } ?>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                <?php } ?>
                <div class="aCenter mb20"><div id="container1" style="min-width: 310px; height: 400px; margin: 0 auto"></div></div>
                <div class="ml20 mr20" style="margin-bottom: 150px;">
                    <table id="datatable1" class="tableC">
                        <tbody>
                        <tr>
                            <th>项目</th>
                            <?php foreach ($courses as $c){ ?>
                                <th><?php echo $c['type_name'] ?></th>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td style="border-bottom: none;">课程数</td>
                            <?php foreach ($courses as $c){ ?>
                                <td style="border-bottom: none;"><?php echo round($c['count_num']) ?></td>
                            <?php } ?>
                        </tr>

                        </tbody>
                    </table>
                </div>

                <div class="aCenter mb20"><div id="container2" style="min-width: 310px; height: 400px; margin: 0 auto"></div></div>
                <div class="ml20 mr20" style="margin-bottom: 150px;">
                    <table id="datatable2" class="tableC">
                        <tbody>
                        <tr>
                            <th>项目</th>
                            <?php foreach ($courses as $c){ ?>
                                <th><?php echo $c['type_name'] ?></th>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td style="border-bottom: none;">课程人次</td>
                            <?php foreach ($courses as $c){ ?>
                                <td style="border-bottom: none;"><?php echo round($c['people_num']) ?></td>
                            <?php } ?>
                        </tr>

                        </tbody>
                    </table>
                </div>

                <div class="aCenter mb20"><div id="container3" style="min-width: 310px; height: 400px; margin: 0 auto"></div></div>
                <div class="ml20 mr20" style="margin-bottom: 150px;">
                    <table class="tableC">
                        <tbody>
                        <tr>
                            <th>项目</th>
                            <?php foreach ($courses as $c){ ?>
                                <th><?php echo $c['type_name'] ?></th>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td style="border-bottom: none;">预算</td>
                            <?php foreach ($courses as $c){ ?>
                                <td style="border-bottom: none;"><?php echo round($c['price_num']) ?>元</td>
                            <?php } ?>
                        </tr>

                        </tbody>
                    </table>
                </div>

                <p class="f18 aCenter mb10"></p>
                <div class="aCenter mb20"><div id="container4" style="min-width: 310px; height: 400px; margin: 0 auto"></div></div>
                <div class="ml20 mr20" style="margin-bottom: 150px;<?php if(count($datatrend)>12){echo 'overflow-x: scroll;';}?>">
                    <table class="tableC">
                        <tbody>
                        <tr>
                            <th style="word-break:keep-all;">时间</th>
                            <?php foreach($datatrend as $tk=>$t){
                                echo '<th style="padding: 5px;">'.substr($tk,0,4).'.'.substr($tk,-2).'</th>';
                            } ?>
                        </tr>
                        <tr>
                            <td style="border-bottom: none;word-break:keep-all;">课程数</td>
                            <?php foreach($datatrend as $tk=>$t){
                                echo '<td style="border-bottom: none;padding: 5px;">'.$t.'</td>';
                            } ?>
                        </tr>

                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>