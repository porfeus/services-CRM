<?php
$this->registerJsFile('design/multiselect/jquery.multiselect.js');
$this->registerCssFile('design/multiselect/jquery.multiselect.css');
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?=App::t('Редактирование кнопки продления')?></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<ul class="breadcrumb">
<li><a href="<?=App::get()->controllerName?>"><?=App::t('Кнопки продления')?></a></li>
<li class="active"><?=App::t('Редактирование кнопки продления')?></li>
</ul>

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">

            </div>
            <div class="panel-body">
              <form role="form" method="post">
                  <?=Form::dropDownList($model, 'service', $dropdownData, ['multiple' => 'multiple'])
                  ?>
                  <?=Form::textInput($model, 'activated_num')?>
                  <?=Form::textInput($model, 'link')?>
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
