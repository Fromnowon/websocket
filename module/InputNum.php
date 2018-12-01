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
                    <input type="text" class="form-control server" value="192.168.2.113">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-info btn_jump">确定</button>
            </div>
        </div>
    </div>
</div>
<script>
var num = null;
var ip = '192.168.2.113';
var webSocket = new WebSocket("ws://" + ip + ":8083");
webSocket.onopen = function () {

};
webSocket.onerror = function () {
  alert('发生错误，请联系管理员！');
};
webSocket.onmessage = function (ev) {

};
$(function () {
  $('#input_num_modal').modal({
    keyboard: false,
    backdrop: 'static',
  });
  $('.btn_jump').click(function () {
    var server = $('.server').val();
    if (server.length > 0) {
      webSocket.send(JSON.stringify({code: -1, reason: '配置完成，开始跳转', content: 'close'}));
      webSocket.close();
      window.location.href = './client.php?server=' + server;
    }
  })
})
</script>
