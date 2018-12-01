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

    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <style>
        .result_img_div {
            height: 300px;
            border: 1px solid lightgray;
        }

        .row {
            margin: 0;
        }

        .result_img {
            height: 100%;
        }

        .result_img img {
            height: 100%;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div style="margin-top: 10px">
        <span>服务器：<span class="status"></span></span>
        <span style="margin-left: 20px">选手数：<span class="num">null</span></span>
    </div>
    <hr size="3" style="margin: 10px 0">
    <div>
        <div class="result row">

        </div>
    </div>

</div>
</body>
<script>
var ip = '<?php echo '192.168.2.113';?>';
var webSocket = new WebSocket("ws://" + ip + ":8083");

webSocket.onopen = function () {
  webSocket.send(JSON.stringify({code: 3}));
  $('.status').html('<i class="fa fa-circle" style="color: limegreen"></i>');
};
webSocket.onmessage = function (ev) {
  //处理信息
  let op = JSON.parse(ev.data)['op'];
  let data = JSON.parse(ev.data)['data'];
  switch (op) {
    case 0:
      result_init(data);
      break;
    case 1:
      //提取图片
      $(".result_img_div[flag='" + data + "'] .result_img").html("<img class='img-responsive center-block' src='./img/" + data + ".jpeg?" + Math.random() + "'/>");
      break;
    case 2:
      //增加选手
      result_handler(data, 1);
      break;
    case 3:
      //删除选手
      result_handler(data, 0);
      break;
  }

  //结果区域控制
  function result_handler(ip, action) {
    if (action) {
      if ($(".result_img_div[flag='" + ip + "']").length == 0) {
        //不存在
        $('.result').append("<div class='col-md-4 result_img_div' flag='" + ip + "'><div class='result_img'></div>" +
          "<span style='position: absolute;left: 5px;bottom: 5px'>" + ip + "</span></div>");
        $('.num').text(parseInt($('.num').text()) + 1);
      }
    } else {
      //删除一个结果区域
      $(".result_img_div[flag='" + ip + "']").remove();
      $('.num').text(parseInt($('.num').text()) - 1);
    }
  }

  function result_init(data) {
    let clients_arr = data;
    $('.num').html(clients_arr.length);
    //添加图像区域
    for (let index in clients_arr) {
      $('.result').append("<div class='col-md-4 result_img_div' flag='" + clients_arr[index] + "'><div class='result_img'></div>" +
        "<span style='position: absolute;left: 5px;bottom: 5px'>" + clients_arr[index] + "</span></div>");

    }
  }
};

window.onbeforeunload = function () {
  wsClose();
};
$(function () {

});

//断开连接
function wsClose(reason) {
  if (reason == undefined) {
    reason = '未知';
  }
  webSocket.send(JSON.stringify({code: -1, reason: reason, content: 'close'}));
  webSocket.close();
  $('.status').html('<i class="fa fa-circle" style="color: red"></i>');
}
</script>
</html>