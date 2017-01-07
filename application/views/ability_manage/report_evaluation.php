<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css?1128"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css?1228"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/print.css"/>
<script src="<?php echo base_url(); ?>js/highcharts/highcharts.js"></script>
<script src="<?php echo base_url(); ?>js/highcharts/modules/data.js"></script>
<script src="<?php echo base_url(); ?>js/highcharts/modules/exporting.js"></script>
<script type="text/javascript">
    $(function () {
        <?php if($evaluation_student['status']==2){ ?>
        //自评
        $('#container1').highcharts({
            colors:['#ffce49', '#00bbd3','#36a2eb', '#91e8e1', '#ffce56', '#ff8e72','#bc8500', '#45b7cd', '#36A2EB', '#af7cad', '#ff6384', '#cc65fe'],
            data: {
                table: 'datatable1'
            },
            chart: {
                type: 'column'
            },
            title: {
                text: '员工自评与标准对照表'
            },
            yAxis: {
                allowDecimals: false,
                title: {
                    text: '均分'
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
        <?php } ?>

        <?php if($evaluation_student['others_status']==2){ ?>
        //他评
        $('#container2').highcharts({
            colors:['#91e8e1', '#00bbd3','#36a2eb', '#91e8e1', '#ffce56', '#ff8e72','#bc8500', '#45b7cd', '#36A2EB', '#af7cad', '#ff6384', '#cc65fe'],
            data: {
                table: 'datatable2'
            },
            chart: {
                type: 'column'
            },
            title: {
                text: '员工他评与标准对照表'
            },
            yAxis: {
                allowDecimals: false,
                title: {
                    text: '均分'
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
        <?php } ?>
    });
</script>
<div class="wrap">
    <div class="titCom clearfix">
        <span class="titSpan"><?php echo $student['name'] ?>&nbsp;<?php echo $abilityjob['name'] ?></span>
        <div class="fRight">
            <a class="borBlueH37 mr5" href="<?php echo site_url($returnevaluationlisturl) ?>" >返回列表</a>
        </div>
    </div>

    <div class="topNaviKec01">
        <?php $this->load->view ( 'ability_manage/report_top_navi' ); ?>
        <ul class="fRight proPrint">
            <li>
                <a href="javascript:window.print();" class="blue"><i class="fa fa-print fa-lg mr5"></i>打印</a>
            </li>
        </ul>
    </div>

    <div class="clearfix textureBox">
        <div class="p15">
            <p class="f24 aCenter mb20"><?php echo $student['name'] ?>&nbsp;<?php echo $abilityjob['name'] ?></p>
            <div style="margin-bottom: 50px;">
                <table class="tableC">
                    <colgroup>
                        <col width="16%">
                        <col width="14%">
                        <col width="14%">
                        <?php if($abilityjob['hasleadership']==1){?><col width="14%"><?php } ?>
                        <col width="14%">
                        <col width="14%">
                        <col width="14%">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th></th>
                        <th>专业能力</th>
                        <th>通用能力</th>
                        <?php if($abilityjob['hasleadership']==1){?><th>领导力</th><?php } ?>
                        <th>个性</th>
                        <th>经验</th>
                        <th>总均分</th>
                    </tr>
                    <?php if($evaluation_student['status']==2){ ?>
                    <tr>
                        <td>自评</td>
                        <td><?php echo $dataself[1]; ?></td>
                        <td><?php echo $dataself[2]; ?></td>
                        <?php if($abilityjob['hasleadership']==1){?><td><?php echo $dataself[3]; ?></td><?php } ?>
                        <td><?php echo $dataself[4]; ?></td>
                        <td><?php echo $dataself[5]; ?></td>
                        <td><?php echo $dataself['point_total']; ?></td>
                    </tr>
                    <?php } ?>
                    <?php if($evaluation_student['others_status']==2){ ?>
                    <tr>
                        <td>他评</td>
                        <td><?php echo $dataother[1]; ?></td>
                        <td><?php echo $dataother[2]; ?></td>
                        <?php if($abilityjob['hasleadership']==1){?><td><?php echo $dataother[3]; ?></td><?php } ?>
                        <td><?php echo $dataother[4]; ?></td>
                        <td><?php echo $dataother[5]; ?></td>
                        <td><?php echo $dataother['point_total']; ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td style="border-bottom: none;">标准</td>
                        <td style="border-bottom: none;"><?php echo $datastandar[1]; ?></td>
                        <td style="border-bottom: none;"><?php echo $datastandar[2]; ?></td>
                        <?php if($abilityjob['hasleadership']==1){?><td style="border-bottom: none;"><?php echo $datastandar[3]; ?></td><?php } ?>
                        <td style="border-bottom: none;"><?php echo $datastandar[4]; ?></td>
                        <td style="border-bottom: none;"><?php echo $datastandar[5]; ?></td>
                        <td style="border-bottom: none;"><?php echo $datastandar['point_total']; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="clearfix mr20">
                <?php if($evaluation_student['status']==2){ ?>
                <div class="aCenter mb20"><div id="container1" style="min-width: 310px; height: 400px; margin: 0 auto"></div></div>
                <div class="ml20 mr20" style="display: none;">
                    <table id="datatable1" class="tableC">
                        <tbody>
                        <tr>
                            <th></th>
                            <th>自评</th>
                            <th>标准</th>
                        </tr>
                        <tr>
                            <td>专业能力</td>
                            <td><?php echo $dataself[1]; ?></td>
                            <td><?php echo $datastandar[1]; ?></td>
                        </tr>
                        <tr>
                            <td>通用能力</td>
                            <td><?php echo $dataself[2]; ?></td>
                            <td><?php echo $datastandar[2]; ?></td>
                        </tr>
                        <?php if($abilityjob['hasleadership']==1){?>
                        <tr>
                            <td>领导力</td>
                            <td><?php echo $dataself[3]; ?></td>
                            <td><?php echo $datastandar[3]; ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td>个性</td>
                            <td><?php echo $dataself[4]; ?></td>
                            <td><?php echo $datastandar[4]; ?></td>
                        </tr>
                        <tr>
                            <td>经验</td>
                            <td><?php echo $dataself[5]; ?></td>
                            <td><?php echo $datastandar[5]; ?></td>
                        </tr>
                        <tr>
                            <td>总均分</td>
                            <td><?php echo $dataself['point_total']; ?></td>
                            <td><?php echo $datastandar['point_total']; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="ml20 mr20" style="margin-bottom: 50px;">
                    <table class="tableC">
                        <colgroup>
                            <col width="16%">
                            <col width="14%">
                            <col width="14%">
                            <?php if($abilityjob['hasleadership']==1){?><col width="14%"><?php } ?>
                            <col width="14%">
                            <col width="14%">
                            <col width="14%">
                        </colgroup>
                        <tbody>
                    <tr>
                        <th></th>
                        <th>专业能力</th>
                        <th>通用能力</th>
                        <?php if($abilityjob['hasleadership']==1){?><th>领导力</th><?php } ?>
                        <th>个性</th>
                        <th>经验</th>
                        <th>总均分</th>
                    </tr>
                    <tr>
                        <td>自评</td>
                        <td><?php echo $dataself[1]; ?></td>
                        <td><?php echo $dataself[2]; ?></td>
                        <?php if($abilityjob['hasleadership']==1){?><td><?php echo $dataself[3]; ?></td><?php } ?>
                        <td><?php echo $dataself[4]; ?></td>
                        <td><?php echo $dataself[5]; ?></td>
                        <td><?php echo $dataself['point_total']; ?></td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;">标准</td>
                        <td style="border-bottom: none;"><?php echo $datastandar[1]; ?></td>
                        <td style="border-bottom: none;"><?php echo $datastandar[2]; ?></td>
                        <?php if($abilityjob['hasleadership']==1){?><td style="border-bottom: none;"><?php echo $datastandar[3]; ?></td><?php } ?>
                        <td style="border-bottom: none;"><?php echo $datastandar[4]; ?></td>
                        <td style="border-bottom: none;"><?php echo $datastandar[5]; ?></td>
                        <td style="border-bottom: none;"><?php echo $datastandar['point_total']; ?></td>
                    </tr>
                    </tbody>
                    </table>
                </div>
                <?php } ?>
                <?php if($evaluation_student['others_status']==2){ ?>
                <div class="aCenter mb20"><div id="container2" style="min-width: 310px; height: 400px; margin: 0 auto"></div></div>
                <div class="ml20 mr20" style="display: none;">
                    <table id="datatable2" class="tableC">
                        <tbody>
                        <tr>
                            <th></th>
                            <th>他评</th>
                            <th>标准</th>
                        </tr>
                        <tr>
                            <td>专业能力</td>
                            <td><?php echo $dataother[1]; ?></td>
                            <td><?php echo $datastandar[1]; ?></td>
                        </tr>
                        <tr>
                            <td>通用能力</td>
                            <td><?php echo $dataother[2]; ?></td>
                            <td><?php echo $datastandar[2]; ?></td>
                        </tr>
                        <?php if($abilityjob['hasleadership']==1){?>
                            <tr>
                                <td>领导力</td>
                                <td><?php echo $dataother[3]; ?></td>
                                <td><?php echo $datastandar[3]; ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td>个性</td>
                            <td><?php echo $dataother[4]; ?></td>
                            <td><?php echo $datastandar[4]; ?></td>
                        </tr>
                        <tr>
                            <td>经验</td>
                            <td><?php echo $dataother[5]; ?></td>
                            <td><?php echo $datastandar[5]; ?></td>
                        </tr>
                        <tr>
                            <td>总均分</td>
                            <td><?php echo $dataother['point_total']; ?></td>
                            <td><?php echo $datastandar['point_total']; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="ml20 mr20" style="margin-bottom: 100px;">
                    <table class="tableC">
                        <colgroup>
                            <col width="16%">
                            <col width="14%">
                            <col width="14%">
                            <?php if($abilityjob['hasleadership']==1){?><col width="14%"><?php } ?>
                            <col width="14%">
                            <col width="14%">
                            <col width="14%">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th></th>
                            <th>专业能力</th>
                            <th>通用能力</th>
                            <?php if($abilityjob['hasleadership']==1){?><th>领导力</th><?php } ?>
                            <th>个性</th>
                            <th>经验</th>
                            <th>总均分</th>
                        </tr>
                        <tr>
                            <td>他评</td>
                            <td><?php echo $dataother[1]; ?></td>
                            <td><?php echo $dataother[2]; ?></td>
                            <?php if($abilityjob['hasleadership']==1){?><td><?php echo $dataother[3]; ?></td><?php } ?>
                            <td><?php echo $dataother[4]; ?></td>
                            <td><?php echo $dataother[5]; ?></td>
                            <td><?php echo $dataother['point_total']; ?></td>
                        </tr>
                        <tr>
                            <td style="border-bottom: none;">标准</td>
                            <td style="border-bottom: none;"><?php echo $datastandar[1]; ?></td>
                            <td style="border-bottom: none;"><?php echo $datastandar[2]; ?></td>
                            <?php if($abilityjob['hasleadership']==1){?><td style="border-bottom: none;"><?php echo $datastandar[3]; ?></td><?php } ?>
                            <td style="border-bottom: none;"><?php echo $datastandar[4]; ?></td>
                            <td style="border-bottom: none;"><?php echo $datastandar[5]; ?></td>
                            <td style="border-bottom: none;"><?php echo $datastandar['point_total']; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <?php } ?>

            </div>

        </div>
    </div>
</div>