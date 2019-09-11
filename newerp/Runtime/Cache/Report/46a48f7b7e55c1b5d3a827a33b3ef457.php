<?php if (!defined('THINK_PATH')) exit(); include "include/config.php"; include "top.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" type="text/css" href="/newerp/Public/css/bootstrap.min.css-v=3.3.5.css">
    <link rel="stylesheet" type="text/css" href="/newerp/Public/css/custom.css">
    <link href="/newerp/Public/css/plugins/chosen_v1.6.2/chosen.css" rel="stylesheet">
    <script src="/newerp/Public/js/jquery.js"></script>
    <script src="/newerp/Public/layer/layer.js"></script>
    <script type="text/javascript" src="/newerp/Public/css/plugins/chosen_v1.6.2/chosen.jquery.min.js"></script>
    <script src="/newerp/Public/js/plugins/laydate-v5.0/laydate.js"></script>
    <style>
        th[type=sort] {
            cursor: pointer;
        }

        .text-primary {
            color: #1ab394;
        }

        .search-bar > div > li, .search-bar > div > div > li {
            width: 120px;
            /*float: left;*/
            margin: 5px;
            display: inline-block;
            height: 30px;
        }

        .search-bar > div > li, .search-bar > div > div > li {
            width: 120px;
            /*float: left;*/
            margin: 5px;
            display: inline-block;
            height: 30px;
        }

        .ibox-content {
            padding: 0
        }

        .search-bar > div > .searchTitle, .search-bar > div > div > .searchTitle {
            line-height: 30px;
        }

        .custom-table th {
            font-weight: normal;
            border-bottom: 1px solid #f0f0f0;
        }

        .custom-table td {
            border-bottom: 1px solid #f0f0f0;
        }

        /*.chosen-container .chosen-single {*/
        /*height: 30px;*/
        /*line-height: 30px;*/
        /*border: 1px solid #ccc;*/
        /*}*/

        .input-group {
            margin: 5px 5px;
        }

        .form-control {
            border: 1px solid #ccc;
            padding: 0px 12px;
        }

        .form-control:focus {
            border-color: #66afe9;
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
        }
        .chosen-container .chosen-single {
            height: 30px;
            line-height: 30px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>



    <div class="ibox float-e-margins" style="margin:0 auto;height: 100%;padding:20px;width:100%;background:#fff;">
        <form>
        <div class="form-inline">
            <div class="input-group">
                <div class="input-group-addon btn-primary" style="cursor:pointer;" onclick="searchForm(this,'1')">前一小时</div>
                <input type="text" class="form-control input-sm" name="start_time" style="width: 100px;" id="start"
                       placeholder="开始时间" value="<?php echo ($params["start_time"]); ?>">
                <div class="input-group-addon">-</div>
                <input type="text" class="form-control input-sm" name="end_time" style="width: 100px;"  id="end"
                       placeholder="结束时间" value="<?php echo ($params["end_time"]); ?>">
                <div class="input-group-addon btn-primary" style="cursor:pointer;" onclick="searchForm(this,'2')">后一小时</div>
            </div>
            <button onclick="searchForm(this)" class="btn btn-primary btn-sm" id="search-btn" type="button">显示统计结果</button>
        </div>

        </form>
        <div id="container" style="min-width: 310px; height: 400px; margin: 100px auto"></div>
        <div id="xnum" style="display:none;"><?php echo $fordate ? $fordate : ''; ?></div>
    </div>
    <script src="js/highcharts.js"></script>
    <script type="text/javascript">

        $(function () {

            laydate.render({elem: '#start', type: 'time', theme: 'molv'});
            laydate.render({elem: '#end', type: 'time', theme: 'molv'});

            var xnum = $('#xnum').html();
            if (xnum != '') {
                xnum = JSON.parse(xnum);
            } else {
                xnum = [0];
            }

            var result = <?php echo ($result); ?>;
            var seriesArr = [];
            $.each(result,function(index,value){
                seriesArr.push({name:index,data:value});
            });

            $('#container').highcharts({

                chart: { //整体控制
                    renderTo: 'container', //图表容器的DIVbar:横向条形图
                    defaultSeriesType: 'column' //可选，默认为line【line:折线;spline:平滑的线;area:区域图;bar:曲线图;pie:饼图;scatter:点状图等等;
                },
                title: {
                    text: '连续7天相同时段订单量对比',
                    x: -20 //center
                },
                subtitle: {
                    text: ' ',
                    x: -20
                },
                xAxis: {
                    categories: xnum
                },
                yAxis: {
                    title: {
                        text: '订单量'
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                },
                tooltip: {
                    valueSuffix: ' '
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0
                },
                series: seriesArr
            });

        });
        /*
         * 搜索
         */
        function searchForm(obj,st) {
            var start	= document.getElementById('start').value;
            var end  	= document.getElementById('end').value;

            if(start == '' || end == ''){
                layer.alert('请选择日期');
                return false;
            }

            if(end < start){
                layer.alert('起始时间不得超过结束时间,请重新筛选');return false;
            }
            var data = $(obj).closest('form').serialize();
            data     = data.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
            data     = data.replace(/^&/g, '');
            var url = '/t.php?s=/Report/ComingWmsOrderQty/index';
            if (url.indexOf('?') > 0) {
                url += '&' + data;
            } else {
                url += '?' + data;
            }
            url += "&st="+st;
            location.href = url;
        }


    </script>


</body>
</html>