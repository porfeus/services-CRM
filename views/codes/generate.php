<?php
$this->registerJsFile('design/multiselect/jquery.multiselect.js');
$this->registerCssFile('design/multiselect/jquery.multiselect.css');
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?=App::t('Генерация кодов продления')?></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<ul class="breadcrumb">
<li><a href="<?=App::get()->controllerName?>"><?=App::t('Коды продления')?></a></li>
<li class="active"><?=App::t('Генерация кодов продления')?></li>
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
                  <label>Список кодов продления</label>
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
                  <?=Form::dropDownList($model, 'service', $dropdownData, ['multiple' => 'multiple'])
                  ?>
                  <?=Form::textInput($model, 'codes_num')?>
                  <?=Form::dropDownList($model, 'activated_type', $model->getActivatedTypes())?>
                  <?=Form::textInput($model, 'activated_num')?>
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
</script>
