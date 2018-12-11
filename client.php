<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1,minimum-scale=1,user-scalable=no">
    <title>书写</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/font-awesome.min.css">

    <script src="./js/jquery-1.11.0.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <style>
        body {
            overflow: hidden;
            background: #efefef;
        }

        .draw {
            margin: 10px;
        }

        .btn {
            min-width: 100px;
        }

        .signature {
            cursor: url('assert/pen.png') 10 30, auto;
        }

    </style>
</head>
<body>
<?php
if (!isset($_GET['server'])) {
    //输入选手号码
    include './module/InputNum.php';
    echo '</body></html>';
    exit();
} else $SERVER = $_GET['server'];
?>
<div class="draw">
    <div class="signature">
    </div>
    <div class="ws_status">
        <span class="server"></span>
        <span class="ip"></span>
    </div>
    <div style="clear: both"></div>
    <div style="margin-top: 10px">
        <button id="clearBtn" class="btn btn-danger btn-lg pull-left" onclick="clearCanvas();"><i
                    class="fa fa-eraser"> 清空</i>
        </button>
        <button id="clearBtn" class="btn btn-warning btn-lg pull-left btn-eraser" style="margin-left: 20px"><i
                    class="fa fa-eraser"> 橡皮</i>
        </button>
        <button id="saveBtn" class="btn btn-success btn-lg pull-right" onclick="saveSignature();">
            <i class="fa fa-check"> 提交</i>
        </button>
    </div>

</div>
<!--<script src="//cdn.jsdelivr.net/npm/eruda"></script>-->
<!--<script>-->
<!--eruda.init();-->
<!--console.log('控制台打印信息');-->
<!--</script>-->
<script src="./js/jSignature.js"></script>
<script>
var ip = '<?php echo $SERVER;?>';
var webSocket = new WebSocket("ws://" + ip + ":8083");
</script>
<script src="./js/client.js"></script>
<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static"
     data-keyboard="false">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">title</h4>
            </div>
            <div class="modal-body">
                <h5>content</h5>
            </div>
        </div>
    </div>
</div>
</body>
</html>