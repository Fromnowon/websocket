$(function () {
  var pushbar = new Pushbar({
    blur: true,
    overlay: false,
  });
  //菜单显示
  $('.menu_area').on('mouseenter', function () {
    pushbar.open('mypushbar1');
  });
  //菜单隐藏
  $('.pushbar').on('mouseleave', function () {
    pushbar.close();
  });

  //连接服务器
  let webSocket = new WebSocket("ws://" + $('.server_ip').text() + ":8083");

  webSocket.onopen = function () {
    webSocket.send(JSON.stringify({code: 3}));
    $('.status').html('<i class="fa fa-circle" style="color: limegreen"></i>');
  };

  webSocket.onclose = function () {
    $('.status').html('<i class="fa fa-circle" style="color: red"></i>');
  }
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

  //断开连接
  function wsClose(reason) {
    if (reason == undefined) {
      reason = '未知';
    }
    webSocket.send(JSON.stringify({code: -1, reason: reason, content: 'close'}));
    // webSocket.close();
    $('.status').html('<i class="fa fa-circle" style="color: red"></i>');
  }

});


