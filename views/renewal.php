<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
              <?=App::t('Активация кода продления')?>
            </div>
            <div class="panel-body">
              <form class="form-inline text-center" method="post">
                <div class="form-group<?=(($has_error)? ' has-error':'')?><?=(($has_success)? ' has-success':'')?>">
                  <input name="code" type="text" class="form-control" id="exampleInputName2" placeholder="<?=App::t('Введите код')?>" size="60" value="<?=$model->code?>">
                  <button type="submit" class="btn btn-success"><?=App::t('Активировать')?></button>
                  <p class="help-block text-left"><?=(($has_error)? '<i class="fa fa-warning" style="color: #e00e18"></i>':'')?> <?=$message?></p>
                </div>
              </form>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->

        <?php if( !empty($buttons) ){ ?>
        <div class="panel panel-default">
            <div class="panel-heading">
              <?=App::t('Кнопки продления')?>
            </div>
            <div class="panel-body" style="line-height: 50px;">
              <?=$buttons?>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
        <?php } ?>
    </div>
    <!-- /.col-lg-6 -->
</div>
<!-- /.row -->
