<?php
$this->registerJsFile('design/multiselect/jquery.multiselect.js');
$this->registerCssFile('design/multiselect/jquery.multiselect.css');
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?=App::t('Изменение времени кода продления')?></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<ul class="breadcrumb">
<li><a href="<?=App::get()->controllerName?>"><?=App::t('Коды продления')?></a></li>
<li class="active"><?=App::t('Изменение времени кода продления')?></li>
</ul>

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
            </div>
            <div class="panel-body">
              <form role="form" method="post">
                  <?=Form::text($model, 'code')?>
                  <?=Form::dropDownList($model, 'service', $dropdownData, ['multiple' => 'multiple'])
                  ?>
                  <?=Form::dropDownList($model, 'activated_type', $model->getActivatedTypes())?>
                  <?=Form::textInput($model, 'activated_num')?>
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
