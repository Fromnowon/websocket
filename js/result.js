var webSocket;
var frozenData = 1;//默认冻结数据，不接收图像
var remain;
var relate_problem = 0;//默认不关联题目
var problem_content_arr = [];//存储题目描述数据，减少与服务器联系次数
$(function () {
  var pushbar = new Pushbar({
    blur: true,
    overlay: false,
  });
  //菜单显示
  $('.menu_area').on('mouseenter', function () {
    pushbar.open('mypushbar1');
    $('.start_btn_div').hide();
  });
  //菜单高度
  $('.pushbar .row').css({height: $('.pushbar').height() - $('.pushbar').children().outerHeight(true)});

  //菜单隐藏
  $(document).click(function () {
    pushbar.close();
    $('.start_btn_div').show();
  });
  $('.pushbar').click(function (e) {
    e.stopPropagation();
  });
  //加载题目列表
  $.ajax({
    type: 'POST',
    dataType: 'json',
    url: './handler.php?action=pull_problem',
    success: function (msg) {
      var html = '<table class="table p_list_table disabled"><thead><th>序号</th><th>标题</th><th>操作</th></thead>';
      $.each(msg, function (index, item) {
        html += '<tr><td>' + (index + 1) + '</td><td class="title">' + item.title + '</td><td><button class="disabled btn btn-default btn-sm p_select">选择</button></td></tr>';
      });

      $('.p_list').html(html + '</table>');
      //绑定题目选择
      $('.p_select').click(function () {
        if ($(this).hasClass('disabled')) return;
        relate_problem = 1;
        $.ajax({
          type: 'POST',
          dataType: 'json',
          url: './handler.php?action=pull_problem_selected',
          data: {title: html_encode($(this).parents('tr').find('.title').html())},
          success: function (msg) {
            var html = '<h3>' + msg.title + '</h3>';
            html += '<table class="table p_list_selected_table"><thead><th>标记</th><th>题号</th><th>答案</th></thead>';
            var content_json = JSON.parse(msg.content);
            $.each(content_json, function (index, value) {
              html += '<tr';
              if (index == 0) {
                //即将开始作答此题
                html += ' class="ready"';
              }
              html += '><td><i class="fa fa-circle-o not"></i></td><td class="p_num">' + (index + 1) + '</td><td><span class="not_yet">未揭晓</span><span class="hide answer">' + value.answer + '</span></td></tr>';
              problem_content_arr.push(value.description);
            });

            html += '</table>';
            $('.p_list').html(html);
          }
        });
      });
    }
  });
  //记录当前比赛进度


  //菜单功能
  $('.header_info').change(function () {
    if ($(this).is(":checked")) {
      $('.page-header').show();
      $('.header_input').show();
    } else {
      $('.page-header').hide();
      $('.header_input').hide();
    }
  });
  $('#header_lg').on('input propertychange', function () {
    $('.page-header-lg').html($(this).val());
  });
  $('#header_sm').on('input propertychange', function () {
    $('.page-header-sm').html($(this).val());
  });
  $('.reload').click(function () {
    window.location.href = './result.php';
  });
  $('.test').click(function () {
    if ($(this).attr('flag') == 0) {
      //开启测试
      frozenData = 0;
      $(this).removeClass('btn-default').addClass('btn-danger').attr('flag', 1).html(' <i class="fa fa-pencil"></i> 关闭测试');
    } else {
      //关闭测试
      frozenData = 1;
      $(this).removeClass('btn-danger').addClass('btn-default').attr('flag', 0).html(' <i class="fa fa-pencil"></i> 开启测试');
    }
    //清除图像
    $('.result_img').html('');
  });
  //关联题目开关
  $('.relate_problem').change(function () {
    if ($(this).is(":checked")) {
      //relate_problem = 1;//最后选择题组后才赋值
      $('.p_list table').removeClass('disabled');
      $('.p_list .p_select').removeClass('disabled');
    } else {
      relate_problem = 0;
      $('.p_list table').addClass('disabled');
      $('.p_list .p_select').addClass('disabled');
    }
  });

  //图像放大
  $('.result').on('click', '.result_img_div', function () {
    $('.img_detail').html($('.result_img').find('img').clone().css({height: '100%'})).fadeIn(function () {
    });
  });
  $('.img_detail').click(function () {
    if ($('.img_detail').css('display') != 'none') {
      $('.img_detail').fadeOut();
    }
  });

  //进度条
  $('.circle').click(function () {
    var times = $('#time_set').val();
    if (times.length <= 0) {
      alert('未设置倒计时');
      return;
    }
    //若测试开启则关闭
    if ($('.test').attr('flag') == 1) {
      $('.test').trigger('click');
    }
    //清除答案区域
    $('.result_img').html('');
    //检测是否关联题目，是则显示modal
    if (relate_problem) {
      //填充数据
      var num = parseInt($('.p_list_selected_table').find('.ready').find('.p_num').text()) - 1;
      $('#problem_modal .modal-title').html('第' + (num + 1) + '题：');
      $('#problem_modal .modal-body').html(problem_content_arr[num]);
      $('#problem_modal').modal({
        backdrop: 'static'
      });
      $('#problem_modal .btn_p_mode').off().click(function () {
        $('#problem_modal').modal('hide');
        start_fun(times);
      });

    } else {
      start_fun(times);
    }
  });

  //作答函数
  function start_fun(times) {
    //开始接收数据
    frozenData = 0;
    //隐藏按钮，显示进度条
    $('.circle').animate({bottom: '-100px'}, 500);
    $('.progress').css({opacity: 1});
    var bar = $('.time_bar .progress-bar');
    bar.text(times + 's');
    var times_remain = times;
    var play = 0;
    var sound = $('.times_sound').is(":checked");
    var counter = setInterval(function () {
      times_remain--;
      if (!play && times_remain <= 10) {
        $('.progress-bar').removeClass('progress-bar-success').addClass('progress-bar-danger');
        if (sound) {
          $('.player').html('<audio autoplay="autoplay" style="display: none" src="./assert/ding.ogg"></audio>');
        }
        play = 1;
      }
      if (times_remain <= -1) {
        //结束
        window.clearInterval(counter);
        //初始化
        $('.circle').animate({bottom: '0'}, 500);
        $('.progress').css({opacity: 0});
        $('.time_bar .progress-bar').css({width: '100%'});
        $('.progress-bar').removeClass('progress-bar-danger').addClass('progress-bar-success');
        //若关联，则切到下一题
        if (relate_problem) {
          var obj = $('.p_list_selected_table').find('.ready');
          obj.find('.not_yet').next().removeClass('hide').prev().remove();
          obj.find('.not').replaceWith('<i class="fa fa-circle"></i>');
          obj.removeClass('ready').next().addClass('ready');
          //显示答案
          $('#answer_modal .modal-body').html('<div style="text-align: center;font-size: 32px;font-weight: bold">' + obj.find('.answer').text() + '</div>');
          $('#answer_modal').modal('show');
        }
        //通知服务器
        webSocket.send(JSON.stringify({code: 0}));
        //不再接收新图像
        frozenData = 1;
      } else {
        bar.css({width: 100 * times_remain / times + '%'});
        bar.text(times_remain + 's');
      }
    }, 1000);
  }


  //连接服务器
  webSocket = new WebSocket("ws://" + $('.server_ip').text() + ":8083");

  webSocket.onerror = function () {
    console.log('无法连接服务器');
  };

  webSocket.onopen = function () {
    webSocket.send(JSON.stringify({code: 3}));
    $('.status').html('<i class="fa fa-circle" style="color: limegreen"></i>');
  };

  webSocket.onclose = function () {
    $('.status').html('<i class="fa fa-circle" style="color: red"></i>');
  };
  webSocket.onmessage = function (ev) {
    //处理信息
    var op = JSON.parse(ev.data)['op'];
    var data = JSON.parse(ev.data)['data'];
    switch (op) {
      case 0:
        result_init(data);
        break;
      case 1:
        //提取图片
        if (!frozenData) {
          $(".result_img_div[flag='" + data + "'] .result_img").html("<img class='img-responsive center-block' src='./img/" + data + ".jpeg?" + Math.random() + "'/>");
        }
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
      remain_height($('.result_img_div').length + 1);
      if (action) {
        if ($(".result_img_div[flag='" + ip + "']").length == 0) {
          //不存在
          $('.result').append("<div class='col-md-4 result_img_div' style='height: " + remain + "px;' flag='" + ip + "'><div class='result_img'></div>" +
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
      var clients_arr = data;
      remain_height(clients_arr.length);
      $('.num').html(clients_arr.length);
      //添加图像区域
      for (var index in clients_arr) {
        $('.result').append("<div class='col-md-4 result_img_div' style='height: " + remain + "px;' flag='" + clients_arr[index] + "'><div class='result_img'></div>" +
          "<span style='position: absolute;left: 5px;bottom: 5px'>" + clients_arr[index] + "</span></div>");
      }
    }
  };

  function remain_height(num) {
    //图像尺寸
    remain = ($(window).height() - $('.page-header').height() - $('.start_btn_div').height()) * 0.8 / (Math.ceil(num / 3));
  }

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

//loading指示器
function loading(option) {
  //待补充
}

//转义函数
function html_encode(str) {
  if (str.length == 0) return "";
  str = str.replace(new RegExp("\"", "g"), "&quot;");
  str = str.replace(new RegExp("'", "g"), "&#39;");
  str = str.replace(new RegExp(" ", "g"), "&nbsp;");
  return str;
}