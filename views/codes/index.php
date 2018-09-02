<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?=App::t('Коды продления')?></h1>
        <div class="form-inline" style="float: right">
          <div class="form-group">
            <label for="activated-filter"><?=App::t('Активация')?>:</label>
            <select class="form-control" id="activated-filter">
              <option value="no"><?=App::t('Неактивированные')?></option>
              <option value="yes"><?=App::t('Активированные')?></option>
              <option value="all"><?=App::t('Любые')?></option>
            </select>
          </div>
          <div class="form-group" style="margin-left:10px;">
            <label for="type-filter"><?=App::t('Типы услуг')?>:</label>
            <select class="form-control" id="type-filter">
              <option value="all"><?=App::t('Любые')?></option>
              <option value="txt"><?=Services::getTypeTitle('txt')?> (<?=$total_txt?>)</option>
              <option value="txtgroup"><?=Services::getTypeTitle('txtgroup')?> (<?=$total_txtgroup?>)</option>
              <option value="page"><?=Services::getTypeTitle('page')?> (<?=$total_page?>)</option>
            </select>
          </div>
        </div>
        <p>
          <a class="btn btn-primary" href="codes/generate"><?=App::t('Сгенерировать')?></a>
        </p><br />
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-md-12">
        <form method="post" id="delete-form">
          <div class="panel panel-default">
              <div class="panel-heading">
                  <?=App::t('Всего кодов')?>: <span id="total-items"><?=$total?></span>,
                  <?=App::t('неактивированных')?>: <span id="noactivated-items"><?=$noactivated?></span>
              </div>
              <!-- /.panel-heading -->
              <div class="panel-body">
                  <table width="100%" class="dataTables table table-striped table-hover ">
                      <thead>
                          <tr>
                              <th>
                                  <input type="checkbox" onclick="checkAll(this)" />
                              </th>
                              <th style="text-align: left"><?=$model->getLabel('code')?></th>
                              <th><?=$model->getLabel('created_time')?></th>
                              <th><?=$model->getLabel('activated_add_time')?></th>
                              <th><?=$model->getLabel('service')?></th>
                              <th style="min-width:255px;"><?=App::t('Информация')?></th>
                              <th><?=App::t('Действие')?></th>
                          </tr>
                      </thead>
                  </table>
                  <!-- /.table-responsive -->
              </div>
              <!-- /.panel-body -->
          </div>
          <!-- /.panel -->
        </form>
        <p>
          <a type="submit" class="btn btn-danger" id="delete-button"><?=App::t('Удалить отмеченное')?></a>
        </p>
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
          "url" :"./codes?action=ajax",
          "type": "post"
        },
        "columns":[
            { "data": function ( row, type, set ) {
                return '<input type="checkbox" name="delete[]" value="'+ row.id +'" />';
            } },
            { "data": function ( row, type, set ) {
                return '<span style="float: left">'+ row.code +'</span>';
            } },
            { "data": "created_time" },
            { "data": "activated_add_time" },
            { "data": function ( row, type, set ) {
                return '<span style="cursor: help" class="tip" title="'+
                (getServicesNames[row.service] || 'Услуга не найдена') +'">'+ row.service +'</span>';
            } },
            { "data": "status" },
            { "data": function ( row, type, set ) {
                return [
                  '<a href="codes/edit?id='+ row.id +'"><i class="glyphicon glyphicon-pencil" title="<?=App::t('Редактировать')?>"></i></a>',
                  '<a href="#" data-id="'+ row.id +'" onclick="deleteLink(event, this)"><i class="glyphicon glyphicon-trash" title="<?=App::t('Удалить')?>"></i></a>'
                ].join(' ');
            } }
        ],
        "responsive": true,
        "sort": false,
        "pageLength": <?=App::get()->config['codes_show_num']?>,
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

    $('#activated-filter').on('change', function(){
      window.dataTable
        .column( 1 )
        .search( this.value )
        .draw();
    });

    $('#type-filter').on('change', function(){
      window.dataTable
        .column( 2 )
        .search( this.value )
        .draw();
    });

    $('#delete-button').on('click', function(){
      if( !confirm("<?=App::t('Удалить выбранное')?>?") ) return;
      $.post('codes/delete', $( "#delete-form" ).serialize(), function(data){
        data = JSON.parse(data);
        $('#total-items').text( data.total );
        $('#noactivated-items').text( data.noactivated );

        window.dataTable.draw();
        $('.dataTables').find('[type="checkbox"]').prop('checked', false);
      });
    });
});

function deleteLink(e, o){
  e.preventDefault();
  if( !confirm("<?=App::t('Удалить выбранное')?>?") ) return;
  $.post('codes/delete', 'delete[0]='+ $(o).data('id') +'', function(data){
    data = JSON.parse(data);
    $('#total-items').text( data.total );
    $('#noactivated-items').text( data.noactivated );

    window.dataTable.draw();
  });
}

var getServicesNames = <?=json_encode($getServicesNames)?>;
</script>
