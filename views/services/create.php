<?php
$this->registerJsFile('design/form/jquery.form.js');
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?=App::t('Создание услуги')?></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<ul class="breadcrumb">
<li><a href="<?=App::get()->controllerName?>"><?=App::t('Услуги')?></a></li>
<li class="active"><?=App::t('Создание услуги')?></li>
</ul>

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">

            </div>
            <div class="panel-body">
              <form id="services_form" method="post" enctype="multipart/form-data">
                  <?=Form::dropDownList($model, 'type', Services::getDropdownTypesData())?>
                  <?=Form::textInput($model, 'sid')?>
                  <?=Form::textInput($model, 'name')?>
                  <?=Form::fileInput($model, 'file')?>
                  <div id="txtgroup_files_block">
                    <b>Загрузить файлы</b>
                    <div id="txtgroup_files_station" style="padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd; margin-bottom: 20px;">

                    </div>
                  </div>
                  <?=Form::textInput($model, 'alias')?>
                  <?=Form::textInput($model, 'title')?>
                  <?=Form::textArea($model, 'note', ['rows' => 5])?>
                  <?=Form::textArea($model, 'code', ['rows' => 10])?>
                  <div id="page_box_block">
                    <b>Блоки страницы</b>
                    <div id="page_box_station" style="padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd; margin-bottom: 20px;">

                    </div>
                  </div>
                  <?=Form::textArea($model, 'html_header', ['rows' => 5])?>
                  <?=Form::textArea($model, 'html_footer', ['rows' => 5])?>
                  <button type="submit" class="btn btn-success"><?=App::t('Сохранить')?></button>
              </form>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-6 -->
</div>
<!-- /.row -->

<div class="txtgroup_file_template" style="display: none">
  <div class="form-inline" style="margin-top: 10px;">
    <div class="form-group">
      <b>Название файла</b>
      <input type="text" name="names[]" value=""  class="form-control" style="width: 200px;" />
      <b style="margin-left:10px;">Формат</b>
      <select name="types[]"  class="form-control">
        <option value="">Выбрать</option>
        <option value="txt"><?=Services::getTypeTitle('txt')?></option>
        <option value="zip"><?=Services::getTypeTitle('zip')?></option>
        <option value="rar"><?=Services::getTypeTitle('rar')?></option>
      </select>
      <b style="margin-left:10px;">Файл</b>
      <input type="file" name="files[]" value=""  class="form-control" style="width: 200px;" />
      <span class="txtgroup_button_station"></span>
    </div>
  </div>
</div>

<div class="page_box_template" style="display: none">
  <div class="box-block" style="background-color: #F4F4F4; padding: 5px; margin-top: 20px;">
    <div class="form-inline">
      <div class="form-group">
          <b>Название</b>
          <input type="text" name="boxnames[]" value=""  class="form-control" />
      </div>
      <b style="margin-left:10px;">Цвет фона</b>
      <select name="boxcolors[]" class="form-control">
        <option value="default">Серый</option>
        <option value="primary">Синий</option>
        <option value="success">Зеленый</option>
        <option value="info">Голубой</option>
        <option value="warning">Бежевый</option>
        <option value="danger">Розовый</option>
      </select>
      <span class="page_button_station" style="float: right;"></span>
    </div>
    <div class="form-group">
        <label style="margin-top: 10px;">Содержимое</label>
        <textarea name="boxvalues[]" rows="5" class="form-control"></textarea>
    </div>
  </div>
</div>

<script>
$('[name="type"]').on('change', function(){
  $('.file-block').hide();
  $('.alias-block, .title-block, .note-block, .code-block').hide();
  $('input, select, textarea', '#txtgroup_files_station, #page_box_station').removeAttr('required');

  //Настройка для групповых файлов
  $('#txtgroup_files_block').hide();
  if( this.value == 'txtgroup' ){
    $('#txtgroup_files_block').show();

    var template = $('.txtgroup_file_template').clone();
    template
      .find('.txtgroup_button_station')
      .append('<button class="btn btn-primary txtgroup_add_file">Добавить еще файл</button>');
    $('#txtgroup_files_station').html('').append(template.html());

    //Устанавливаем атрибут "обязательно"
    $('input, select', '#txtgroup_files_station').attr('required', 'required');

    $('.txtgroup_add_file').on('click', function(e){
      e.preventDefault();
      var template2 = $('.txtgroup_file_template').clone();
      template2
        .find('.txtgroup_button_station')
        .append('<button class="btn btn-danger txtgroup_del_file">Удалить</button>');
      $('#txtgroup_files_station').prepend(template2.html());

      //Устанавливаем атрибут "обязательно"
      $('input, select', '#txtgroup_files_station').attr('required', 'required');

      $('.txtgroup_del_file').on('click', function(e){
        e.preventDefault();
        $(this).parents('.form-inline').remove();
      });
    });
  }
  //Конец. Настройка для групповых файлов

  //Настройка для page
  $('#page_box_block').hide();
  if( this.value == 'page' ){
    $('.alias-block, .title-block, .note-block, .code-block').show();
    $('#page_box_block').show();

    $('#page_box_station').html('').append('<button class="btn btn-primary page_add_block">Добавить блок</button>');

    //Устанавливаем атрибут "обязательно"
    $('input, select, textarea', '#page_box_station').attr('required', 'required');

    $('.page_add_block').on('click', function(e){
      e.preventDefault();

      if( $('.box-block').length == 1 ){
        $('#page_box_station').prepend(
          '<div class="form-group columns-block">'+
          '<label>Число колонок</label>'+
          '<select name="columns_num" class="form-control">'+
            '<option value="1">1</option>'+
            '<option value="2">2</option>'+
            '<option value="3">3</option>'+
            '<option value="4">4</option>'+
            '<option value="6">6</option>'+
          '</select>'+
          '</div>'
        );
      }

      var template2 = $('.page_box_template').clone();
      template2
        .find('.page_button_station')
        .append('<button class="btn btn-danger page_del_block">Удалить</button>');
      $('.page_add_block').before(template2.html());

      //Устанавливаем атрибут "обязательно"
      $('input, select, textarea', '#page_box_station').attr('required', 'required');

      $('.page_add_block').text('Добавить еще блок');

      $('.page_del_block').on('click', function(e){
        e.preventDefault();
        $(this).parents('.box-block').remove();

        if( $('.box-block').length == 1 ){
          $('.page_add_block').text('Добавить блок');
          $('.columns-block').remove();
        }
      });
    });
  }
  //Конец. Настройка для page
  else
  if( this.value ){
    $('.file-block').show();
  }
});
$('[name="type"]').triggerHandler('change');

$('#services_form').ajaxForm(function(data) {
    if( !showFormErrors(data) ){
      location = data;
    }
});
</script>
