<?php
$email = $this->app->user->identity->email;
if(
  $this->app->user->identity->need_email &&
  empty($email)
){

  if( empty($email_message) ){
    $email_message = '<span style="color: #e00e18"><i class="fa fa-info-circle"></i> '.App::t('Если Вы хотите получать напоминание об окончании действия аккаунта, или системные уведомления, укажите ваш e-mail').'</span>';
  }

?>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-danger">
            <div class="panel-heading">
              <?=App::t('Запрос e-mail')?>
            </div>
            <div class="panel-body">
              <form class="form-inline text-center" method="post">
                <input type="hidden" name="action" value="save-email" />
                <div class="form-group<?=(($email_error)? ' has-error':'')?><?=(($email_success)? ' has-success':'')?>">
                  <input name="email" type="email" class="form-control" placeholder="<?=App::t('Введите e-mail')?>" size="30" value="<?=$email?>">
                  <button type="submit" class="btn btn-success"><?=App::t('Сохранить')?></button>
                  <p class="help-block text-left"><?=(($email_error)? '<i class="fa fa-warning" style="color: #e00e18"></i>':'')?> <?=$email_message?></p>
                </div>
              </form>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-6 -->
</div>
<!-- /.row -->
<?php } ?>

<!-- /.row -->
<div class="row">
    <div class="col-md-12">
        <form method="post" id="delete-form">
          <div class="panel panel-default">
              <?php if( !empty($mainService) && !$mainService->blank('html_header') ){ ?>
              <div class="panel-heading">
                  <?=$mainService->html_header?>
              </div>
              <?php } ?>
              <!-- /.panel-heading -->
              <div class="panel-body">
                  <table width="100%" class="dataTables table table-striped table-hover ">
                      <thead>
                          <tr>
                              <th><?=$model->getLabel('name')?></th>
                              <?php if( $mainService->type == 'page' ){ ?>
                              <th><?=$model->getLabel('note')?></th>
                              <?php } ?>
                              <th><?=App::t('Действие')?></th>
                          </tr>
                      </thead>
                  </table>
                  <!-- /.table-responsive -->
              </div>
              <!-- /.panel-body -->
              <?php if( !empty($mainService) && !$mainService->blank('html_footer') ){ ?>
              <div class="panel-footer">
                  <?=$mainService->html_footer?>
              </div>
              <?php } ?>
          </div>
          <!-- /.panel -->
        </form>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<script>
$(document).ready(function() {
    window.dataTable = $('.dataTables').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url" :"./services?action=ajax",
          "type": "post"
        },
        "columns":[
            { "data": function ( row, type, set ) {
                var archiveTimeout = getArchivesTimeouts[row.id] || '';
                if( archiveTimeout ) {
                  archiveTimeout = ' <span style="color: #337ab7">(<?=App::t('архив будет удалён через')?>: '+ archiveTimeout +')</span>';
                }
                return '<span style="float: left">'+ row.name + archiveTimeout +'</span>';
            } },
            <?php if( $mainService->type == 'page' ){ ?>
            { "data": function ( row, type, set ) {
                  if( row.note != '' ){
                    return '<a data-note="'+ row.note +'" class="btn btn-success" onclick="noteLink(event, this)"><?=App::t('Примечание')?></a>';
                  }
                  return '';
              } },
            <?php } ?>
            { "data": "serviceLink" }
        ],
        "searching": false,
        "info": false,
        "paginate": false,
        "responsive": true,
        "sort": false,
        "pageLength": <?=App::get()->config['services_show_num']?>,
        "language": {
          "infoFiltered": "",
          "paginate": {
          "first": "<?=App::t('Первая')?>",
          "last": "<?=App::t('Последняя')?>",
          "next": "<?=App::t('Далее')?>",
          "previous": "<?=App::t('Назад')?>"
        },
        "emptyTable": "<?=App::t('Таблица пуста')?>",
        "info": "<?=App::t('Страница _PAGE_ из _PAGES_')?>",
        "infoEmpty": "<?=App::t('Нет записей для отображения')?>",
        "lengthMenu": "",
        "loadingRecords": "<?=App::t('Пожалуйста, ждите...')?>",
        "processing": "<?=App::t('Пожалуйста, ждите...')?>",
        "search": "<?=App::t('Поиск:')?>",
        "zeroRecords": "<?=App::t('Нет записей для отображения')?>"
      }
    });

    $('#type-filter').on('change', function(){
      window.dataTable
        .column( 1 )
        .search( this.value )
        .draw();
    });
});

function noteLink(e, o){
  e.preventDefault();

  if( $(o).text() == '<?=App::t('Примечание')?>' ){
    var html = '';
    html += $(o).data('note');
    $(o).parents('tr').after('<tr><td colspan="20" style="text-align:left">'+ html +'</td></tr>');
    $(o).text('<?=App::t('Скрыть')?>')
    $(o).addClass('btn-warning').removeClass('btn-success');
  }else{
    $(o).text('<?=App::t('Примечание')?>');
    $(o).addClass('btn-success').removeClass('btn-warning');
    $(o).parents('tr').next().remove();
  }
}

var getArchivesTimeouts = <?=json_encode($this->app->user->identity->archivesTimeOutTitles())?>;
</script>
