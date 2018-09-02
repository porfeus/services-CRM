<?php
class MainController extends BaseController{

  public function accessRules(){
    return [
        [
            'allow' => true,
            'roles' => ['admin', 'user'],
        ],
        [
            'allow' => false,
            'actions' => ['renewal'],
            'roles' => ['admin'],
        ],
        [
            'allow' => false,
            'actions' => ['download'],
            'roles' => ['user'],
        ],
        [
            'allow' => true,
            'actions' => ['login', 'install'],
            'roles' => ['guest'],
        ],
    ];
  }

  public function actionLogin(){

    $error_message = '';
    $login_error = false;
    $captcha_error = false;
    $need_agree = false;
    $agree_error = false;

    if( Request::issetPost() ){
      if(
        !Request::session('captcha_off') &&
        (
          !Request::post('captcha') ||
          Request::post('captcha') != $_SESSION['captcha']['code']
        ) &&
        (
          (PAGE_TYPE == 'admin' && $this->app->config['show_captcha_admin']) ||
          (PAGE_TYPE == 'user' && $this->app->config['show_captcha_user'])
        )
      ){
        $error_message = App::t('Неправильно введен проверочный код');
        $captcha_error = true;
      }else
      if( $this->app->user->login(Request::post('login'), Request::post('password')) ){
        Request::session('captcha_off', 1);

        $identity = $this->app->user->identity;

        if( $this->app->user->role == 'user' ){

          // Проверка лимита пользователей
          if( $identity->usersOnlineLimited() ){
            $error_message = App::t('Достиг лимит пользователей онлайн на Вашем аккаунте');
            $this->app->user->logout();
          }

          // Проверка блокировки пользователей
          if( $identity->banned ){
            $error_message = App::t('Действие аккаунта заблокировано Администратором. Для выяснения причин обратитесь в службу поддержки');
            $this->app->user->logout();
          }

          // Проверка отсутствия активации
          if( empty($error_message) && !$identity->activated_time ){

            if( Request::post('agree') || !$this->app->config['need_agree'] ){
              $identity->activated_time = time();
              $identity->update();
            }else{
              $need_agree = true;
              if( Request::post('agree') == '0' )  $agree_error = true;
              $error_message = App::t('Согласитесь с условиями');
              Request::session('login', '');
            }
          }

          // Сохранение входных данных
          if( empty($error_message) ){
            $identity->ip_old = $identity->ip_new;
            $identity->ip_new = $_SERVER['REMOTE_ADDR'];
            $identity->last_enter_time = time();
            $identity->last_update_time = time();
            $identity->language = $this->app->language->getActiveId();
            $identity->usersOnlineSet();
            $identity->update();
          }
        }

        // Редирект на защищенную страницу
        if( empty($error_message) ){
          return $this->redirect('main/index');
        }

      }else{
        $error_message = App::t('Неправильный логин или пароль');
        $login_error = true;
      }
    }

    include(__DIR__."/../captcha/simple-php-captcha.php");
    $_SESSION['captcha'] = simple_php_captcha();

    return $this->renderPartial('login-'.PAGE_TYPE, array(
      'error_message' => $error_message,
      'login_error' => $login_error,
      'captcha_error' => $captcha_error,
      'need_agree' => $need_agree,
      'agree_error' => $agree_error,
    ));
  }

  public function actionLogout(){
    // Сохранение выходных данных
    if( $this->app->user->role == 'user' ){
      $identity = $this->app->user->identity;
      $identity->last_update_time = 0;
      $identity->usersOnlineDel();
      $identity->update();
    }

    $this->app->user->logout();
    return $this->redirect('main/login');
  }

  public function actionIndex(){
    switch( $this->app->user->role ){
      case 'user':
        return $this->redirect('services');
      break;
      case 'admin':
        return $this->redirect('users');
      break;
    }
  }

  public function actionDownload(){
    $basename = basename(Request::get('file'));
    $file = __DIR__.'/../files/'.$basename;
    header('Content-Disposition: attachment; filename="'.$basename);
    readfile($file);
  }

  /*
  public function actionSettings(){
    return $this->render('settings');
  }
  */
  public function actionAjax(){
    /*
    switch( Request::post('action') ){
      case 'save-language':
        $this->app->language->saveActive();
      break;
    }
    */
  }

  public function actionRenewal(){
    $model = new RenevalForm();
    $userInfo = $this->app->user->identity;

    //Если тип аккаунта - архивы и активация истекла
    if(
      $userInfo->activationTimeout() &&
      $userInfo->type == 'archive'
    ){
	  $userInfo->delete();
      return self::actionLogout();
    }

    if( $this->app->user->identity->type == 'archive' ){
      return $this->redirect('main/index');
    }

    if( Request::issetPost() ){
      $model->load($_POST);
      $model->code = trim($model->code);

      if( $model->validate() ){
        //Загружаем инфо кода и пользователя
        $codeInfo = $model->info;

        //Добавляем дни активации пользователю
        $userInfo->setActivatedTime($codeInfo->activated_add_time);
        $userInfo->tariff_time += $codeInfo->activated_add_time;
        $userInfo->update();

        //Помечаем код использованным
        $codeInfo->activated_time = time();
        $codeInfo->user_id = $userInfo->id;
        $codeInfo->update();

        $has_success = 1;
        $message = 'Аккаунт успешно активирован!';
      }else{
        $has_error = 1;
        $message = $model->getError('code');
      }
    }

    return $this->render('renewal', [
      'model' => $model,
      'has_success' => $has_success,
      'has_error' => $has_error,
      'message' => $message,
      'buttons' => Buttons::showButtons(),
    ]);
  }

  public function actionInstall(){
    if( Request::get('password') != $this->app->config['admin_password'] ){
      die( App::t('Доступ запрещен!') );
    }

    $result = $this->app->pdo->query('SELECT 1 FROM sites');

    if( !$result ){
      $this->app->pdo->exec(file_get_contents('../config/sql.sql'));
      echo App::t('База импортирована.');
    }else{
      echo App::t('База не нуждается в импорте.');
    }
  }
}
