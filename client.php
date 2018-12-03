<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1,minimum-scale=1,user-scalable=no">
    <title>test</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">

    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
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

    </style>
</head>
<body>
<?php
if (!isset($_GET['server'])) {
    //输入选手号码
    require_once './module/InputNum.php';
    exit();
} else $SERVER = $_GET['server'];
?>
<div class="draw">
    <div class="js-signature" data-border="1px solid black"
         data-line-width="5" data-line-color="#000000" data-auto-fit="true">
    </div>
    <div class="ws_status">
        <span class="server"></span>
        <span class="ip"></span>
    </div>
    <div style="clear: both"></div>
    <div style="margin-top: 10px">
        <button id="clearBtn" class="btn btn-danger btn-lg pull-left" onclick="clearCanvas();"><i
                    class="fa fa-eraser"></i>
        </button>
        <!--        <button id="clearBtn" style="margin-left: 20px" class="btn btn-danger pull-left"-->
        <!--                onclick="wsClose('主动断开');">断开连接-->
        <!--        </button>-->
        <button id="saveBtn" class="btn btn-success btn-lg pull-right" onclick="saveSignature();">
            <i class="fa fa-check"></i>
        </button>
    </div>

</div>
<script src="js/jq-signature.js"></script>
<script type="text/javascript">
var POST_TIME = 500;
var flag = 0;//提交控制
var ip = '<?php echo $SERVER;?>';
var webSocket = new WebSocket("ws://" + ip + ":8083");

window.onbeforeunload = function () {
  wsClose('离开页面');
};

webSocket.onopen = function (ev) {
  if (ev.isTrusted) {
    ws_status_handler(1);
    webSocket.send(JSON.stringify({code: 4}))//告诉服务器有新选手加入
  } else {
    ws_status_handler(0);
  }
};
webSocket.onmessage = function (ev) {
  //获取ip
  let ip = ev.data;
  $('.ws_status .ip').html("<span style='margin-left: 20px'>本机ip：<span class='ip_'>" + ip + "</span></span>");
};
webSocket.onclose = function () {
  //已断开连接
  ws_status_handler(0);
  $('#error').modal();
};
webSocket.onerror = function () {
  ws_status_handler(0);
  $('#error').modal();
};

//ws状态指示
function ws_status_handler(code) {
  switch (code) {
    case 0:
      $('.ws_status .server').html("<span><i style='color: red;' class='fa fa-circle'></i> 服务器状态</span>");
      break;
    case 1:
      $('.ws_status .server').html("<span ><i style='color: lawngreen;' class='fa fa-circle'></i> 服务器状态</span>");
      break;
  }
}

//断开连接
function wsClose(reason) {
  if (reason == undefined) {
    reason = '未知';
  }
  webSocket.send(JSON.stringify({code: -1, reason: reason, content: 'close'}));
  ws_status_handler(0);
  // webSocket.close();
  $('#error').modal();
}

//清除画布
function clearCanvas() {
  $('.js-signature').jqSignature('clearCanvas');
  // $('#saveBtn').attr('disabled', true);
  saveSignature();
}

//提交
function saveSignature() {
  console.log('upload img');
  flag = 0;
  var dataUrl = $('.js-signature').jqSignature('getDataURL');
  $.ajax({
    type: "POST",
    dataType: "text",
    url: "handler.php?action=save",
    data: {img: dataUrl, ip: $('.ws_status .ip_').text()},//注意不要传输多余字段
    success: function (msg) {
      //发送图片url地址
      webSocket.send(JSON.stringify({code: 2, content: msg}));
    },
    error: function (msg) {
      alert("异常！");
      console.log(msg)
    }
  })
}

$(function () {
  //动态高度
  var signature = $('.js-signature');
  signature.attr('data-height', $(window).height() - 100);

  if (signature.length) {
    $('.js-signature').jqSignature();
  }
  signature.on('touchmove', function (event) {
    event.preventDefault();//阻止浏览器的默认事件
  });

  signature.on('jq.signature.changed', function () {
    flag = 1;
  });

  //定时提交
  setInterval(function () {
    if (flag) {
      saveSignature();
    }
  }, POST_TIME);
})
</script>
<!-- Modal -->
<div class="modal fade" id="error" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static"
     data-keyboard="false">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">错误</h4>
            </div>
            <div class="modal-body">
                <h5>与服务器连接断开，请刷新页面</h5>
            </div>
        </div>
    </div>
</div>
</body>
</html>