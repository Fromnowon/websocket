<?php
require_once './module/sqlConn.php';
require_once './module/sqlHandler.php';
$conn = sql_conn("localhost", "root", "8ud7fh", 'my_contest');
$sql_handler = new sqlHandler($conn);
$SERVER_IP = $sql_handler->select('server_ip', "`id`=1")[0]['ip'];
?>
<!-- Modal -->
<div class="modal fade" id="input_num_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">配置</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>服务器：</label>
                    <input type="text" class="form-control server">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-info btn_jump">确定</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function () {
  let ip = '<?php echo $SERVER_IP;?>';
  $('.server').val(ip);
  $('#input_num_modal').modal({
    keyboard: false,
    backdrop: 'static',
  });
  $('.btn_jump').click(function () {
    //连接ws服务器
    if ($('.server').val().length == 0) {
      alert('请填写服务器地址');
      return;
    }
    let webSocket = new WebSocket("ws://" + $('.server').val() + ":8083");
    webSocket.onopen = function () {
      //连接成功
      webSocket.send(JSON.stringify({code: -1, reason: '配置完成，跳转', content: 'close'}));
      window.location.href = './client.php?server=' + $('.server').val();
    };
    webSocket.onerror = function () {
      alert('发生错误，请联系管理员！');
    };
    webSocket.onmessage = function (ev) {

    };
  })
})
</script>
