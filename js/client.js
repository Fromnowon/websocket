var POST_TIME = 500;
var flag = 0;//提交控制

window.onbeforeunload = function () {
  wsClose('离开页面');
};

webSocket.onopen = function () {
  if (webSocket.readyState == 1) {
    ws_status_handler(1);
    webSocket.send(JSON.stringify({code: 4}))//告诉服务器有新选手加入
  } else {
    ws_status_handler(0);
  }
};
webSocket.onmessage = function (ev) {
  var data = JSON.parse(ev.data);
  switch (data['op']) {
    case 0:
      //答题结束
      modal_edit('结束', '<h5>本次作答结束</h5>');
      $('.js-signature').jqSignature('clearCanvas');//清除画布
      setTimeout(function () {
        $('#modal').modal('hide');
      }, 3000);
      break;
    case 1:
      //获取ip
      var ip = data['ip'];
      $('.ws_status .ip').html("<span style='margin-left: 20px'>IP：<span class='ip_'>" + ip + "</span></span>");
      break;
  }
};
webSocket.onclose = function () {
  //已断开连接
  ws_status_handler(0);
  modal_edit('错误', '<h5>与服务器断开连接，请<a href="javascript:viod(0)" onclick="(function() { window.location.reload(); })()">刷新</a>页面</h5>');
};
webSocket.onerror = function () {
  ws_status_handler(0);
  modal_edit('错误', '<h5>与服务器断开连接，请<a href="javascript:viod(0)" onclick="(function() { window.location.reload(); })()">刷新</a>页面</h5>');
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
  modal_edit('错误', '<h5>与服务器断开连接，请<a href="javascript:viod(0)" onclick="(function() { window.location.reload(); })()">刷新</a>页面</h5>');
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

//modal定制
function modal_edit(title, content) {
  var modal = $('#modal');
  modal.find('.modal-title').html(title);
  modal.find('.modal-body').html(content);
  modal.modal();
}

$(function () {
  var signature = $('.js-signature');
  //动态高度
  signature.attr('data-height', $(window).height() - 100);

  if (signature.length) {
    $('.js-signature').jqSignature({
      lineColor: '#222222',
      lineWidth: 5,
      border: '1px dashed #AAAAAA',
      background: '#FFFFFF',
      autoFit: true
    });
  }
  signature.on('mousemove touchmove', function (event) {
    //console.log(event.originalEvent);
    event.preventDefault();//阻止浏览器的默认事件
  });

  //内容无变化时无需提交
  signature.on('jq.signature.changed', function (e) {
    flag = 1;
  });
  //定时提交
  setInterval(function () {
    if (flag) {
      saveSignature();
    }
  }, POST_TIME);

  //橡皮擦
  $('.btn-eraser').click(function () {
    var canvas = $('.js-signature').jqSignature('getCanvas');
    if (canvas.getContext) {
      //获取对应的CanvasRenderingContext2D对象(画笔)
      var ctx = canvas.getContext("2d");

      //绘制一个以坐标点(100,10)为圆心、半径为50px的圆形
      ctx.arc(100, 100, 50, 0, Math.PI * 2, false);

      //绘制并填充圆形内部
      ctx.fill();

      //擦除矩形区域内的图形
      ctx.clearRect(90, 90, 20, 20);
    }
  })
});