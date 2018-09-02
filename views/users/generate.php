<?php
$this->registerJsFile('design/multiselect/jquery.multiselect.js');
$this->registerCssFile('design/multiselect/jquery.multiselect.css');
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?=App::t('Генерация аккаунтов')?></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<ul class="breadcrumb">
<li><a href="<?=App::get()->controllerName?>"><?=App::t('Аккаунты')?></a></li>
<li class="active"><?=App::t('Генерация аккаунтов')?></li>
</ul>

<?php
if($result){
?>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
              Результат генерации
            </div>
            <div class="panel-body">
              <div class="form-group">
                  <label>Список аккаунтов</label>
                  <textarea class="form-control" rows="10"><?=$result?></textarea>
              </div>

              <a href="main/download?file=generate.csv" class="btn btn-primary">
                <i class="fa fa-save fa-fw"></i>Скачать список
              </a>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-6 -->
</div>
<!-- /.row -->
<?php
}
?>

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
              Форма генерации
            </div>
            <div class="panel-body">
              <form role="form" method="post">
                  <?=Form::dropDownList($model, 'services', $dropdownData, ['multiple' => 'multiple'])
                  ?>
                  <?=Form::dropDownList($model, 'login_type', ['ID+RANDOM', 'ID+LOGIN'])?>
                  <?=Form::textInput($model, 'login_prefix')?>
                  <input type="hidden" name="need_email" value="0" />
                  <?=Form::dropDownList($model, 'need_email', [
                    App::t('Да') => 1,
                    App::t('Нет') => 0,
                  ])?>
                  <?=Form::textInput($model, 'accounts_num')?>
                  <?=Form::dropDownList($model, 'activated_type', $model->getActivatedTypes())?>
                  <?=Form::textInput($model, 'activated_num')?>
                  <input type="hidden" name="users_limit" value="1" />
                  <?=Form::dropDownList($model, 'users_limit', [1,2,3,4,5,6,7,8,9,10])?>
                  <?=Form::dropDownList($model, 'language', $languages)?>
                  <?php
                  foreach($messages as $name=>$message){
                    $model->{$name} = Request::post($name, $message);
                    echo Form::textArea($model, $name, ['rows' => 5, 'label' => 'Сообщение']);
                  }
                  ?>
                  <button type="submit" class="btn btn-success"><?=App::t('Сгенерировать')?></button>
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
var messagesSelector = ['.message_<?=implode("-block', '.message_", array_values($languages))?>-block'].join(', ');
$(messagesSelector).hide();
if( $('[name="language"]').val() ){
  $('.message_'+ $('[name="language"]').val() +'-block').show();
}

$('[name="language"]').on('change', function(){
  $(messagesSelector).hide();
  $('.message_'+ this.value +'-block').show();
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
