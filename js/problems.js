$(function () {
  //初始化第一个编辑器
  editor_init($('#editor0'));

  //内容高度
  set_size();

  //滚动条初始化
  $('.left').optiscroll();

  //回到顶部
  $('.p_top_btn').click(function () {
    $('.right').animate({scrollTop: 0}, 500);
  });

  //监听右侧滚动
  $('.right').scroll(function () {
    //滚到顶部
    if ($(this).scrollTop() < $('.p_content').eq(0).height()) {
      $('.p_top_btn').stop().fadeOut();
    } else {
      $('.p_top_btn').fadeIn();
    }

    $.each($('.p_content'), function (index, el) {
      if (($(this).offset().top + $(this).height() * 0.5) > 0) {//元素本身的高度需要参与计算，具有“过渡”效果
        $('.list-group-item').removeClass('active');
        $('.list-group-item').eq(index).addClass('active');
        return false;
      }
    });
    //console.log($(this).scrollTop() / ($(this)[0].scrollHeight - $(this).height()));
    if ($(this).scrollTop() / ($(this)[0].scrollHeight - $(this).height()) > 0.98) {
      //滚到底部
      $('.list-group-item').removeClass('active');
      $('.list-group-item:last-child').addClass('active');
    }
  });
  //左侧菜单跳转
  $('.list-group').on('click', '.list-group-item', function () {
    var num = $('.list-group-item').index($(this));
    $(".right").stop().animate({scrollTop: $('.p_content').eq(num).offset().top + $(".right").scrollTop()});
  });

  //监听所有答案输入框
  $('.right').on('focus input propertychange', '.answer_input', function () {
    //获取焦点时滚动到此题
    $('.right').animate({scrollTop: ($(".right").scrollTop() + $(this).offset().top - 40)});

    var index = $('.p_content').index($(this).parents('.p_content'));//获取当前题号，从0开始
    $('.list-group-item').eq(index).find('.answer_menu').text($(this).val());
  });

  //增加题目
  $('.p_add_btn').click(function () {
    var num = $('.p_content').length;
    $('.right').append('<div class="p_content"><div class="answer form-group form-inline">' +
      '<h4>第<span class="p_num">' + (num + 1) + '</span>题，答案：</h4>' +
      '<input type="text" class="form-control answer_input" style="width: 30%"></div>' +
      '<div id="editor' + num + '" class="editor"></div></div>');
    //编辑器初始化
    editor_init($('#editor' + num));
    //增加导航
    $('.list-group').append('<a class="list-group-item"><span class="answer_num">' + (num + 1) + '</span>、<span class="answer_menu"></span></a>');
  });

  //保存
  $('.btn-save').click(function () {
    var content = {title: $('.title_input').val()};//标题将于后端完全转义
    if (content.title.length == 0) {
      alert('请填写标题信息');
    } else {
      var p = Array();
      var exit = 0;
      $.each($('.p_content'), function (index, el) {
        //遍历每道题目
        var p_answer = $(this).find('.answer_input').val();
        var p_editor = $(this).find('.editor');
        if (p_answer.length == 0) {
          alert('答案信息不完整，请检查');
          exit = 1;
        } else if (p_editor.summernote('isEmpty')) {
          alert('题目描述不完整，请检查');
          exit = 1;
        } else {
          p.push({answer: html_encode(p_answer), description: html_encode(p_editor.summernote('code'))});
        }
      });
      if (exit) return;
      else {
        //检验通过
        content['content'] = p;
        $.ajax({
          type: 'POST',
          url: './handler.php?action=create_problem',
          dataType: 'text',
          data: {data: JSON.stringify(content)},
          success: function (msg) {
            console.log(msg);
            if (msg == 'SUCCESS') {
              alert('保存成功');
            } else {
              alert('发生错误，' + msg);
            }
          }
        })
      }
    }
  });

});


window.onresize = function () {
  set_size();
};

function insert_img(editor, files) {
  var imgs = new FormData();
  for (var index in files) {
    imgs.append(index + '', files[index]);
  }
  $.ajax({
    type: 'POST',
    processData: false,
    contentType: false,
    url: './handler.php?action=p_img_upload',
    dataType: 'text',
    data: imgs,
    success: function (msg) {
      var img_arr = JSON.parse(msg);
      for (var index in img_arr) {
        editor.summernote('insertImage', img_arr[index], 'img' + index);
      }
    }
  })
}

function editor_init(editor) {
  var MAX_IMG_SIZE = 1024 * 1024 * 5;
  //编辑器初始化
  editor.summernote({
    lang: 'zh-CN',
    focus: false,
    minHeight: 300,
    followingToolbar: false,
    dialogsFade: true,
    maximumImageFileSize: MAX_IMG_SIZE,
    callbacks: {
      onFocus: function () {
        //获取焦点回调
        $('.right').animate({scrollTop: ($(".right").scrollTop() + $(this).parent().find('.answer').offset().top - 10)});
      },
      onImageUpload: function (files) {
        //校验图片大小、后缀
        var len = files.length;
        for (var index = 0; index < len; index++) {
          if (files[index].type.substr(0, files[index].type.indexOf('/')) != 'image') {
            alert('不能上传非图片文件');
            return;
          }
          if (files[index].size > MAX_IMG_SIZE) {
            alert('所选图片体积过大');
            return;
          }
        }
        //上传并插入图片
        insert_img($(this), files);
      }
    }
  });
}

function set_size() {
  $('.left').css({width: $(window).width() * 0.15 - 0 / .5, height: $(window).height()});//计算结果被转成整数
  $('.right').css({width: $(window).width() * 0.85 - 0.5, height: $(window).height()});
}

//转义函数
function html_encode(str) {
  if (str.length == 0) return "";
  str = str.replace(new RegExp("\"", "g"), "&quot;");
  str = str.replace(new RegExp("'", "g"), "&#39;");
  str = str.replace(new RegExp(" ", "g"), "&nbsp;");
  return str;
}
