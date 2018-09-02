<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?=App::t('Услуги')?></h1>
        <p style="float: right;">
          <select class="form-control" id="type-filter">
            <option value="all"><?=App::t('Любые')?></option>
            <option value="txt"><?=Services::getTypeTitle('txt')?> (<?=$total_txt?>)</option>
            <option value="txtgroup"><?=Services::getTypeTitle('txtgroup')?> (<?=$total_txtgroup?>)</option>
            <option value="page"><?=Services::getTypeTitle('page')?> (<?=$total_page?>)</option>
            <option value="zip"><?=Services::getTypeTitle('zip')?> (<?=$total_zip?>)</option>
            <option value="rar"><?=Services::getTypeTitle('rar')?> (<?=$total_rar?>)</option>
          </select>
        </p>
        <p>
          <a class="btn btn-success" href="services/create"><?=App::t('Создать услугу')?></a>
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
                  <?=App::t('Всего записей')?>: <span id="total-items"><?=$total?></span>
              </div>
              <!-- /.panel-heading -->
              <div class="panel-body">
                  <table width="100%" class="dataTables table table-striped table-hover ">
                      <thead>
                          <tr>
                              <th>
                                  <input type="checkbox" onclick="checkAll(this)" />
                              </th>
                              <th style="text-align: left"><?=$model->getLabel('sid')?></th>
                              <th style="text-align: left"><?=$model->getLabel('name')?></th>
                              <th><?=$model->getLabel('type')?></th>
                              <th><?=App::t('Действие')?></th>
                              <th><?=App::t('Админ')?></th>
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
          "url" :"./services?action=ajax",
          "type": "post"
        },
        "columns":[
            { "data": function ( row, type, set ) {
                return '<input type="checkbox" name="delete[]" value="'+ row.id +'" />';
            } },
            { "data": function ( row, type, set ) {
                return '<span style="float: left">'+ row.sid +'</span>';
            } },
            { "data": function ( row, type, set ) {
                return '<span style="float: left">'+ row.name +'</span>';
            } },
            { "data": "typeTitle" },
            { "data": "serviceLink" },
            { "data": function ( row, type, set ) {
                return [
                  '<a href="services/edit?id='+ row.id +'"><i class="glyphicon glyphicon-pencil" title="<?=App::t('Редактировать')?>"></i></a>',
                  '<a href="#" data-id="'+ row.id +'" onclick="deleteLink(event, this)"><i class="glyphicon glyphicon-trash" title="<?=App::t('Удалить')?>"></i></a>'
                ].join(' ');
            } }
        ],
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

    $('#delete-button').on('click', function(){
      if( !confirm("<?=App::t('Удалить выбранное')?>?") ) return;
      $.post('services/delete', $( "#delete-form" ).serialize(), function(totalItems){
        window.dataTable.draw();
        $('.dataTables').find('[type="checkbox"]').prop('checked', false);
        $('#total-items').text( totalItems );
      });
    });
});

function deleteLink(e, o){
  e.preventDefault();
  if( !confirm("<?=App::t('Удалить выбранное')?>?") ) return;
  $.post('services/delete', 'delete[0]='+ $(o).data('id') +'', function(totalItems){
    window.dataTable.draw();
    $('#total-items').text( totalItems );
  });
}

function deleteLink2(e, o){ //child services
  e.preventDefault();
  if( !confirm("<?=App::t('Удалить выбранное')?>?") ) return;
  $.post('services/delete', 'delete[0]='+ $(o).data('id') +'', function(totalItems){
    $(o).parents('tr')[0].parentNode.removeChild($(o).parents('tr')[0]);
  });
}

function loadFiles(e, o){
  e.preventDefault();

  if( $(o).parents('tr').next().hasClass('childs') ){
    $(o).parents('tr').next().remove();
  }else{
    var childsHtml = '';
    $.ajax({
      url: "services/childs?id="+ $(o).data('id'),
      cache: false,
      async: false
    }).done(function(data) {
      childsHtml = data;
    });

    var html = '';
    html += '<div class="text-center"><b><?=App::t('Список файлов')?>:</b></div>';
    html += childsHtml;
    $(o).parents('tr').after('<tr class="childs"><td colspan="20" style="text-align:left">'+ html +'</td></tr>');
  }
}
</script>
