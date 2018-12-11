<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>比赛</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/font-awesome.min.css">
    <link rel="stylesheet" href="./css/pushbar.css">
    <link rel="stylesheet" href="./css/checkbox.min.css">
    <link rel="stylesheet" href="./css/result.css">

    <script src="./js/jquery-1.11.0.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/pushbar.js"></script>
    <script src="./js/result.js"></script>
</head>
<body>
<div class="menu_area"></div>
<div class="hide server_ip"><?php
    include './module/sqlConn.php';
    include './module/sqlHandler.php';
    $conn = sql_conn("localhost", "root", "8ud7fh", 'my_contest');
    $sql_handler = new sqlHandler($conn);
    $ip = $sql_handler->select('server_ip', "`id`=1")[0]['ip'];
    echo $ip;
    ?>
</div>
<div class="player"></div>
<!--侧边菜单栏-->
<div data-pushbar-id="mypushbar1" class="pushbar from_right" style="width: 600px">
    <div style="text-align: right;margin-top: 10px;margin-right: 20px;margin-bottom: 10px">
        <a href="javascript:void(0)" data-pushbar-close="#mypushbar1"><i class="fa fa-remove fa-lg"
                                                                         style="color: black"></i></a>
    </div>
    <div class="row">
        <div class="col-md-5 menu_content">
            <div class="menu_item">
                <p><span class="bold">服务器:</span><?php echo $ip; ?></p>
                <p><span class="bold">状态：</span><span class="status"><i class="fa fa-circle"
                                                                        style="color: red"></i></span></p>
                <p><span class="bold">选手数：</span><span class="num">未知</span></p>
            </div>
            <hr>
            <div class="menu_item">
                <a class="btn btn-default test" href="javascript:void(0)" flag="0">
                    <i class="fa fa-pencil"></i> 开启测试</a>
            </div>
            <hr>
            <div class="menu_item">
                <div class="form-group">
                    <label for="time_set">答题时长:</label>
                    <input type="text" class="form-control" id="time_set" value="15">
                </div>
            </div>
            <hr>
            <div class="menu_item">
                <div>
                    <label class="el-switch el-switch-green">
                        <input class="relate_problem" type="checkbox" name="switch">
                        <span class="el-switch-style"></span>
                    </label>
                    <span class="margin-r bold" style="color: blue">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;关联题目</span>
                </div>
            </div>
            <br>
            <div class="menu_item">
                <div>
                    <label class="el-switch el-switch-green">
                        <input class="header_info" type="checkbox" name="switch" checked="">
                        <span class="el-switch-style"></span>
                    </label>
                    <span class="margin-r bold">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;标题信息</span>
                </div>
            </div>
            <br>
            <div class="menu_item">
                <div>
                    <label class="el-switch el-switch-green">
                        <input class="times_sound" type="checkbox" name="switch" checked="">
                        <span class="el-switch-style"></span>
                    </label>
                    <span class="margin-r bold">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;倒计时音效</span>
                </div>
            </div>
            <hr>
            <div class="menu_item header_input">
                <div class="form-group">
                    <label for="header_lg">主标题:</label>
                    <input type="text" class="form-control" id="header_lg">
                </div>
                <div class="form-group">
                    <label for="header_sm" style="color: gray">副标题:</label>
                    <input type="text" class="form-control" id="header_sm">
                </div>
            </div>
        </div>
        <div class="col-md-7 p_list">

        </div>
    </div>
</div>
<!--主内容区域-->
<div class="pushbar_main_content">
    <div class="container-fluid">
        <div class="page-header">
            <h1>
                <span class="page-header-lg">Example page header</span>
                <small class="page-header-sm">Subtext for header</small>
            </h1>
        </div>
        <div class="time_bar">
            <div class="progress">
                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                     aria-valuemax="100" style="min-width: 2em;width: 100%">
                </div>
            </div>
        </div>
        <div class="result row">

        </div>

    </div>
</div>
<!--图像放大-->
<div class="img_detail">

</div>
<div class="start_btn_div">
    <div class="circle" style="display: inline-block">
        <span class="start_btn_text">开始</span>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="problem_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="myModalLabel">题号</h3>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary btn_p_mode">开始</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="answer_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>