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
    <script src="js/jquery-1.11.0.min.js"></script>
</head>
<body>

</body>
<script>
    var webSocket = new WebSocket("ws://<?php echo $ip;?>:8083");
    //ws事件
    webSocket.onmessage=function (ev) {
        alert(ev.data);
    }
</script>
</html>