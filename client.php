<?php
$_ENV['COMPUTERNAME'] = isset($_ENV['COMPUTERNAME']) ? $_ENV['COMPUTERNAME'] : "";
$ip = gethostbyname($_ENV['COMPUTERNAME']);
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            overflow-x: hidden;
        }

        .draw {
            margin: 10px;
        }
    </style>
</head>
<body>
<div class="draw">
    <div class="js-signature" data-border="1px solid black"
         data-line-width="5" data-line-color="#000000" data-background="#DCDCDC" data-auto-fit="true"></div>
    <div style="margin-top: 10px">
        <button id="clearBtn" style="width: 10%" class="btn btn-danger pull-left" onclick="clearCanvas();">清除</button>
        <button id="clearBtn" style="width: 10%;margin-left: 20px" class="btn btn-danger pull-left"
                onclick="wsClose();">断开连接
        </button>
        <button id="saveBtn" style="width: 10%" class="btn btn-success pull-right" onclick="saveSignature();" disabled>
            提交
        </button>
    </div>
</div>

<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/jq-signature.js"></script>
<script type="text/javascript">
    var webSocket = new WebSocket("ws://<?php echo $ip;?>:8083");
    $(document).on('ready', function () {
        //动态高度
        $('.js-signature').attr('data-height', $(window).height() - 100);

        if ($('.js-signature').length) {
            $('.js-signature').jqSignature();
        }
        $('.js-signature').on('touchmove', function (event) {
            event.preventDefault();//阻止浏览器的默认事件
        })

        //ws事件
        webSocket.onmessage = function (ev) {
            alert(ev.data);
        }
    });

    function wsClose() {
        webSocket.send(JSON.stringify({
            code: -1, ip: '<?php echo $ip;?>', content: 'close'
        }));
        webSocket.close();
    }

    function clearCanvas() {
        $('.js-signature').jqSignature('clearCanvas');
        $('#saveBtn').attr('disabled', true);
    }

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

    $('.js-signature').on('jq.signature.changed', function () {
        $('#saveBtn').attr('disabled', false);
    });
</script>
</body>
</html>