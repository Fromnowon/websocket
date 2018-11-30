<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">

    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <style>
        body {
            overflow-x: hidden;
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
if (!isset($_GET['num'])) {
    //输入选手号码
    require_once './module/InputNum.php';
    exit();
}
?>
<div class="draw">
    <div class="js-signature" data-border="1px solid black"
         data-line-width="5" data-line-color="#000000" data-background="#DCDCDC" data-auto-fit="true">
    </div>
    <div class="ws_status">
        null
    </div>
    <div style="clear: both"></div>
    <div style="margin-top: 10px">
        <button id="clearBtn" class="btn btn-danger btn-lg pull-left" onclick="clearCanvas();"><i class="fa fa-eraser"></i>
        </button>
        <!--        <button id="clearBtn" style="margin-left: 20px" class="btn btn-danger pull-left"-->
        <!--                onclick="wsClose('主动断开');">断开连接-->
        <!--        </button>-->
        <button id="saveBtn" class="btn btn-success btn-lg pull-right" onclick="saveSignature();" disabled>
            <i class="fa fa-check"></i>
        </button>
    </div>

</div>
<script src="js/jq-signature.js"></script>
<script type="text/javascript">
var ip = '192.168.2.113';
var webSocket = new WebSocket("ws://" + ip + ":8083");

window.onbeforeunload = function () {
  wsClose('离开页面');
};

webSocket.onopen = function (ev) {
  if (ev.isTrusted) {
    ws_status_handler(1);
  } else {
    ws_status_handler(0);
  }
};
webSocket.onclose = function () {
  wsClose('断开连接');
};

//ws状态指示
function ws_status_handler(code) {
  switch (code) {
    case 0:
      $('.ws_status').html("<span style='color: red;font-weight: bold'><i class='fa fa-remove'></i> 未连接服务器</span>");
      break;
    case 1:
      $('.ws_status').html("<span style='color: green;font-weight: bold'><i class='fa fa-check'></i> 已连接服务器</span>");
      break;
  }
}

//断开连接
function wsClose(reason) {
  if (reason == undefined) reason = '未知';
  webSocket.send(JSON.stringify({code: -1, reason: reason, content: 'close'}));
  ws_status_handler(0);
  webSocket.close();
}

//清除画布
function clearCanvas() {
  $('.js-signature').jqSignature('clearCanvas');
  $('#saveBtn').attr('disabled', true);
}

//提交
function saveSignature() {
  $('#signature').empty();
  var dataUrl = $('.js-signature').jqSignature('getDataURL');
  $.ajax({
    type: "POST",
    dataType: "text",
    url: "handler.php?action=save",
    data: {img: dataUrl},
    success: function (msg) {
      //发送图片url地址
      webSocket.send(JSON.stringify({code: 1, content: msg}));
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
    $('#saveBtn').attr('disabled', false);
  });
})
</script>
</body>
</html>