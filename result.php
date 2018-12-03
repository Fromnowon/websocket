<?php
$_ENV['COMPUTERNAME'] = isset($_ENV['COMPUTERNAME']) ? $_ENV['COMPUTERNAME'] : "";
$ip = gethostbyname($_ENV['COMPUTERNAME']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>显示结果</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/pushbar.css">
    <link rel="stylesheet" href="css/result.css">

    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/pushbar.js"></script>
    <script src="js/result.js"></script>
</head>
<body>
<div class="hide server_ip"><?php
    require_once './module/sqlConn.php';
    require_once './module/sqlHandler.php';
    $conn = sql_conn("localhost", "root", "8ud7fh", 'my_contest');
    $sql_handler = new sqlHandler($conn);
    echo $sql_handler->select('server_ip', "`id`=1")[0]['ip'];
    ?></div>
<div class="menu_area">

</div>

<div data-pushbar-id="mypushbar1" class="pushbar from_right">
    <div class="menu_item" style="margin: 10px">
        <span>服务器：<span class="status"><i class="fa fa-circle" style="color: red"></i></span></span>
        <span style="margin-left: 20px">选手数：<span class="num">未知</span></span>
    </div>
    <hr>
    <div class="menu_item">
        <button class="btn btn-default">隐藏页头</button>
    </div>
</div>
<!--主内容区域-->
<div class="pushbar_main_content">
    <div class="container-fluid">
        <div>
            <div class="page-header">
                <h1>Example page header
                    <span>&nbsp;</span>
                    <small>Subtext for header</small>
                </h1>
            </div>
            <div class="result row">

            </div>
        </div>

    </div>
</div>

</body>
</html>