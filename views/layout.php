<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <base href="<?=Request::basedir()?>" />

    <title><?=App::t('Заголовок сайта')?></title>

    <!-- Bootstrap Core CSS -->
    <link href="../design/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="../design/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="../design/datatables-responsive/dataTables.responsive.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../css/style.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../design/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- Languages for Bootstrap -->
    <link href="../design/languages/languages.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery -->
    <script src="../design/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../design/bootstrap/js/bootstrap.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="../design/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../design/datatables-plugins/select.js"></script>
    <script src="../design/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../design/datatables-responsive/dataTables.responsive.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../js/common.js"></script>

    <?php
    //отключаем копирование для пользователей (отключено)
    if(App::get()->user->role == 'user' && false){ ?>
    <script src="../js/disable_copy.js"></script>
    <link href="../css/disable_copy.css" rel="stylesheet">
    <?php } ?>

    <?php
    if(App::get()->user->role == 'user'){ ?>
    <style>
      .dataTables{
        border-radius: 4px !important;
        border-collapse: initial !important;
        box-shadow: 0px 0px 6px 0px #337ab7;
      }
    </style>
    <?php } ?>

    <!-- tooltip -->
    <script src="../design/tooltip/tooltip.js"></script>
    <link href="../design/tooltip/tooltip.css" rel="stylesheet" type="text/css">
</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only"><?=App::t('Смена навигации')?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <!-- /.navbar-header -->
            <?php if(App::get()->user->role == 'admin'){ //admin ?>
            <div class="collapse navbar-collapse">
              <ul class="nav navbar-nav navbar-top-links navbar-left" id="main-links">
                <li>
                    <a href="services">
                        <i class="fa fa-briefcase fa-fw"></i> <?=App::t('Услуги')?>
                    </a>
                </li>
                  <li>
                      <a href="users">
                          <i class="fa fa-users fa-fw"></i> <?=App::t('Управление аккаунтами')?>
                      </a>
                  </li>
                  <li>
                      <a href="codes">
                          <i class="fa fa-key fa-fw"></i> <?=App::t('Коды продления')?>
                      </a>
                  </li>
                  <li>
                      <a href="buttons">
                          <i class="fa fa-briefcase fa-fw"></i> <?=App::t('Кнопки продления')?>
                      </a>
                  </li>
                  <!-- /.dropdown -->
              </ul>

              <ul class="nav navbar-nav navbar-top-links navbar-right">
                  <li class="dropdown">
                      <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                          <i class="fa fa-user fa-fw"></i> <?=App::get()->user->identity->login?> <i class="fa fa-caret-down"></i>
                      </a>
                      <ul class="dropdown-menu">
                          <li>
                              <a href="main/logout"><i class="fa fa-sign-out fa-fw"></i> <?=App::t('Выход')?></a>
                          </li>
                      </ul>
                      <!-- /.dropdown-user -->
                  </li>
              </ul>
              <!-- /.navbar-top-links -->
            </div>
        </nav>
            <?php } // end admin ?>

            <?php if(App::get()->user->role == 'user'){ // user ?>
            <div style="height: 50px; background: url(../img/<?=$this->app->config['bg_img-panel']?>) center center">

            </div>
            <div class="collapse navbar-collapse">
              <ul class="nav navbar-nav navbar-top-links navbar-left" id="main-links">
                  <li>
                      <a href="services" class="blue coloured">
                          <i class="fa fa-briefcase fa-fw"></i> <?=App::t('Панель управления')?>
                      </a>
                  </li>
                  <?php if( App::get()->user->identity->type != 'archive' ){ ?>
                  <li>
                      <a href="renewal" class="green coloured">
                          <i class="fa fa-key fa-fw"></i> <?=App::t('Продлить аккаунт')?>
                      </a>
                  </li>
                  <?php } ?>
              </ul>

              <ul class="nav navbar-nav navbar-top-links navbar-right">
                <?=App::get()->language->dropdown()?>

                  <li class="dropdown">
                      <a class="dropdown-toggle red coloured" data-toggle="dropdown" href="#">
                          <i class="fa fa-user fa-fw"></i> <?=App::get()->user->identity->login?> <i class="fa fa-caret-down"></i>
                      </a>
                      <ul class="dropdown-menu">
                          <li>
                              <a href="main/logout"><i class="fa fa-sign-out fa-fw"></i> <?=App::t('Выход')?></a>
                          </li>
                      </ul>
                      <!-- /.dropdown-user -->
                  </li>
              </ul>
              <!-- /.navbar-top-links -->
            </div>
        </nav>
        <?php if( App::get()->user->identity->activationTimeout() ){ ?>
              <div class="text-center alert alert-danger" style="margin-bottom: 20px;">
                <?=App::t('Действие аккаунта приостановлено в связи с истечением времени. Вы можете продлить услугу, купив код продления.')?><br />
                <?=App::t('До полного удаления осталось')?>: <b><?=App::get()->user->identity->deleteTimeLeft()?></b>
              </div>
            <?php }else{ ?>
              <div class="text-center alert alert-info" style="margin-bottom: 20px;">

                <?php if( App::get()->user->identity->type != 'archive' ){ ?>
                  <?=App::t('E-mail')?>:
                  <?=App::get()->user->identity->getData('email', App::t('не указан'))?>,
                <?php } ?>
                <?=App::t('активирован')?>:
                <?=App::get()->user->identity->activatedDate()?>,
                <?=App::t('окончание активации')?>:
                <?=App::get()->user->identity->activatedEndDate()?>
                 (<?=App::t('Осталось')?>: <b><?=App::get()->user->identity->activatedTimeLeft()?></b>)

              </div>
            <?php } ?>

          <?php } // end user ?>

        <div id="page-wrapper">
          <?=$content?>
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer modal-save" style="display: none">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=App::t('Нет')?></button>
                    <button type="button" class="btn btn-primary modal-confirm"><?=App::t('Сохранить')?></button>
                </div>

                <div class="modal-footer modal-send" style="display: none">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=App::t('Нет')?></button>
                    <button type="button" class="btn btn-primary modal-confirm"><?=App::t('Отправить')?></button>
                </div>
                <div class="modal-footer modal-info" style="display: none">
                    <button type="button" class="btn btn-primary" data-dismiss="modal"><?=App::t('Закрыть')?></button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <?php /*if( Request::session('confirm_language') ){
      Request::session('confirm_language', '');
    ?>
      <script>
      $('#myModal .modal-title').text('<?=App::t('Требуется подтверждение')?>');
      $('#myModal .modal-body').text('<?=App::t('Сохранить выбранный вами язык?')?>');
      $('#myModal').modal('show').find('.modal-save').show();
      $('#myModal .modal-confirm').off('click').on('click', function(){
        $('#myModal').modal('hide');
        $.post( "main/ajax", { action: "save-language" } );
      });
      </script>
    <?php }*/ ?>
    <script>
    //select active navbar link
    $('#main-links > li').each(function(){
      var href = $(this).find('a').attr('href').replace('.', '');
      if( location.pathname.replace(/index$/, '').match(new RegExp(href + "$")) ){
        $(this).addClass('active');
      }
    });
    </script>

    <?php
    $modalInfo = Request::session('modal_info');
    if( !empty($modalInfo) ){
      Request::session('modal_info', '');
    ?>
      <script>
      showModalInfo('<?=$modalInfo['title']?>', '<?=$modalInfo['message']?>');
      </script>
    <?php } ?>

    <?php if( isset($servicesNames) ){ ?>
      <script>
      $('.services-block, .service-block').css('position', 'relative');
      $('select[multiple]').find('option[value=""]').remove();

      $('select[multiple]').multiselect({
          columns  : <?=$this->app->config['services_columns']?>,
          search   : true,
          selectAll: true,
          texts: {
              placeholder    : '<?=App::t('Выбрать')?>',
              search         : '<?=App::t('Найти')?>',
              selectedOptions: ' выбрано',
              selectAll      : '',//Выбрать все     // select all text
              unselectAll    : '',//Обнулить выбор   // unselect all text
            //  noneSelected   : 'None Selected'   // None selected text
          },
      });
      </script>
      <script>
      var servicesNames = <?=json_encode($servicesNames)?>;

      //Меняем заголовок на название сервиса
      //Также видоизменяем список чекбоксов для удобного показа подсказок
      $('.ms-options-wrap input:checkbox').each(function(){
        var title = $(this).attr('title');
        $(this).parent()[0].lastChild.data = '';
        $(this).parent().append('<span title="'+ servicesNames[title] +'">'+ title +'</span>');
      });

      //Подключаем плагин всплывающих подсказок
      $('.ms-options-wrap span').tooltip({position: 'r', showDelay: 10});
      </script>
      <script>
      //Запрещаем одновременно выбирать несколько услуг
      $('.ms-options :checkbox').on('click', function(){
        var issetChecked = $('.ms-options :checkbox:checked').length;
        $('.ms-options :checkbox').not(':checked').attr('disabled', !!issetChecked);

        //Разрешаем выбрать текстовый файл с архивом
        var archivesParent = $(
          'span:contains("<?=Services::getTypeTitle('zip')?>"),'+
          'span:contains("<?=Services::getTypeTitle('rar')?>")'
        ).parent();

        var textParent = $(
          'span:contains("<?=Services::getTypeTitle('txt')?>")'
        ).parent();

        //Если выбран архив и не выбран текстовый файл
        if(
          archivesParent.find(':checkbox:checked').length &&
          !textParent.find(':checkbox:checked').length
        ){
          textParent.find(':checkbox').attr('disabled', false);
        }
        //Если выбран выбран текстовый файл и не выбран архив
        if(
          !archivesParent.find(':checkbox:checked').length &&
          textParent.find(':checkbox:checked').length
        ){
          archivesParent.find(':checkbox').attr('disabled', false);
        }
        //Конец. Разрешаем выбрать текстовый файл с архивом

      }).first().triggerHandler('click');
      //Конец. Запрещаем одновременно выбирать несколько услуг
      </script>
    <?php } ?>
    <script>
    $('.dataTables').on( 'draw.dt', function () {
      //Подключаем плагин всплывающих подсказок для ссылок
      $('.dataTables .tip, .dataTables a > i').tooltip();
    });
    $(document).on('click', function(){
      $('.tooltip').remove();
    })
    </script>
</body>

</html>
