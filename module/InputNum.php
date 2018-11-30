<!-- Modal -->
<div class="modal fade" id="input_num_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">请输入参赛号码</h4>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control input_num">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary input_num_btn disabled">确定</button>
            </div>
        </div>
    </div>
</div>
<script>
$('#input_num_modal').modal({
  keyboard: false,
  backdrop: 'static'
});
$('.input_num').on('input propertychange', function () {
  if (!isNaN($('.input_num').val()) && $('.input_num').val() != '') {
    $('.input_num_btn').removeClass('disabled');
    $('.input_num').parent().removeClass('has-error');
  } else {
    $('.input_num').parent().addClass('has-error');
    $('.input_num_btn').addClass('disabled');
  }
})
$('.input_num_btn').click(function () {
  if (!$(this).hasClass('disabled'))
    window.location.href = "./client.php?num=" + $('.input_num').val();

})
</script>
