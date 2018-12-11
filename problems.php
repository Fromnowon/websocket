<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>生成题目</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/font-awesome.min.css">
    <link rel="stylesheet" href="./css/checkbox.min.css">
    <link rel="stylesheet" href="./plugin/summernote/summernote.css">
    <link rel="stylesheet" href="./plugin/optisrcoll/optiscroll.css">
    <link rel="stylesheet" href="./css/problem.css">

    <script src="./js/jquery-1.11.0.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./plugin/summernote/summernote.js"></script>
    <script src="./plugin/summernote/lang/summernote-zh-CN.js"></script>
    <script src="./plugin/optisrcoll/jquery.optiscroll.js"></script>
    <script src="./js/problems.js"></script>
</head>
<body>
<div class="left pull-left optiscroll">
    <div class="list-group">
        <a class="list-group-item"><span class="answer_num">1</span>、<span class="answer_menu"></span></a>
    </div>
</div>
<div class="right pull-left optiscroll">
    <div style="text-align: right">
        <button class="btn btn-success btn-save" style="width: 100px">保存</button>
    </div>
    <div class="form-inline form-group">
        <h2 style="font-weight: bold">标题：</h2>
        <input type="text" class="form-control title_input" style="width:100%;font-size: 24px">
    </div>
    <i class="fa fa-arrow-up fa-2x p_top_btn"></i>
    <div class="p_add_btn circle">
        <i class="fa fa-plus fa-2x p_add_btn_img"></i>
    </div>
    <hr>
    <br>
    <div class="p_content">
        <div class="answer form-group form-inline">
            <h4>第<span class="p_num">1</span>题，答案：</h4>
            <input type="text" class="form-control answer_input" style="width: 30%">
        </div>
        <div id="editor0" class="editor"></div>
    </div>
</div>
</body>
</html>