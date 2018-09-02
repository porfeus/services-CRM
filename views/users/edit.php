<?php
$this->registerJsFile('design/multiselect/jquery.multiselect.js');
$this->registerCssFile('design/multiselect/jquery.multiselect.css');
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?=App::t('Редактирование аккаунта')?>: <?=$model->login?></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<ul class="breadcrumb">
<li><a href="<?=App::get()->controllerName?>"><?=App::t('Аккаунты')?></a></li>
<li class="active"><?=App::t('Редактирование аккаунта')?></li>
</ul>

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">

            </div>
            <div class="panel-body">
              <form role="form" method="post">
                  <?=Form::dropDownList($model, 'services', $dropdownData, ['multiple' => 'multiple'])
                  ?>
                  <?=Form::textInput($model, 'login')?>
                  <?=Form::textInput($model, 'password')?>
                  <?=((!$model->getData('email', ''))?
                    '<input type="hidden" name="need_email" value="0" />'.
                    Form::dropDownList($model, 'need_email', [
                    App::t('Да') => 1,
                    App::t('Нет') => 0,
                  ]):'')?>
                  <?=Form::textInput($model, 'email')?>
                  <div class="form-group">
                    <button type="button" class="btn btn-primary" id="activation_time_button"><?=App::t('Добавить/отнять время активации')?></button>
                  </div>
                  <div id="activation_time_block" style="display: none">
                    <?=Form::dropDownList($model, 'activated_type', $model->getActivatedTypes())?>
                    <?=Form::textInput($model, 'activated_num')?>
                  </div>
                  <input type="hidden" name="users_limit" value="1" />
                  <?=Form::dropDownList($model, 'users_limit', [1,2,3,4,5,6,7,8,9,10])?>
                  <?=Form::textArea($model, 'note', ['rows' => 5])?>
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
<script>
$('#activation_time_button').on('click', function(){
  $('#activation_time_block').show();
  $(this).hide();
});


//Отключаем запрос мыла и лимит человек при выборе только архива
$(function(){
  function disableElements(){
    var totalCheckedNum = $('.ms-options :checkbox:checked').length;

    var archivesCheckedNum = $(
      'span:contains("<?=Services::getTypeTitle('zip')?>"),'+
      'span:contains("<?=Services::getTypeTitle('rar')?>")'
    ).parent().find(':checkbox:checked').length;

    if( totalCheckedNum == 1 && archivesCheckedNum ){
      $('select[name="need_email"],select[name="users_limit"]').attr('disabled', true);
    }else{
      $('select[name="need_email"],select[name="users_limit"]').attr('disabled', false);
    }
  }
  $('.ms-options :checkbox').on('click', disableElements);
  disableElements();
});
//Конец. Отключаем запрос мыла и лимит человек при выборе только архива
</script>
