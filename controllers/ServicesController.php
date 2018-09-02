<?php
class ServicesController extends BaseController{

  public function accessRules(){
    return [
        [
            'allow' => true,
            'roles' => ['admin'],
        ],
        [
            'allow' => true,
            'actions' => ['index', 'get', 'page'],
            'roles' => ['user'],
        ],
    ];
  }

  public static function accessFilter($services){
    if( App::get()->user->role == 'user' ){
      $userInfo = App::get()->user->identity;
      $userInfo->loadServices();
      $services->in('sid', $userInfo->services);
    }
  }

  public function actionIndex(){
    $data = $this->getIndexData();

    if( $this->app->user->role == 'admin' ){
      $view = 'services/index';
    }else{
      $view = 'services/index-user';

      $emailForm = new EmailForm();
      $userInfo = $this->app->user->identity;

      if( Request::issetPost() && Request::post('action') == 'save-email' ){
        $emailForm->load($_POST);

        if( $emailForm->validate() ){

          $userInfo->email = $emailForm->email;
          $userInfo->update();

          $data['email_success'] = 1;
          Request::session('modal_info', [
            'title' => App::t('Информация'),
            'message' => App::t('Системные сообщения подключены'),
          ]);
        }else{
          $data['email_error'] = 1;
          $data['email_message'] = $emailForm->getError('email');
        }
      }
    }

    return $this->render($view, $data);
  }

  public function getIndexData(){

    $services = new Services();

    $services->select('count(1) as count')->eq('parent', '0');
    self::accessFilter($services);
    $total = $services->find()->count;

    $services->select('count(1) as count')->eq('parent', '0');
    $services->eq('type', 'txt');
    self::accessFilter($services);
    $total_txt = $services->find()->count;

    $services->select('count(1) as count')->eq('parent', '0');
    $services->eq('type', 'txtgroup');
    self::accessFilter($services);
    $total_txtgroup = $services->find()->count;

    $services->select('count(1) as count')->eq('parent', '0');
    $services->eq('type', 'zip');
    self::accessFilter($services);
    $total_zip = $services->find()->count;

    $services->select('count(1) as count')->eq('parent', '0');
    $services->eq('type', 'rar');
    self::accessFilter($services);
    $total_rar = $services->find()->count;

    $services->select('count(1) as count')->eq('parent', '0');
    $services->eq('type', 'page');
    self::accessFilter($services);
    $total_page = $services->find()->count;


    if( $this->app->user->role == 'user' ){
      $userInfo = $this->app->user->identity;
      if( $userInfo->type != 'archive' ){
        $userInfo->archivesTimeSet(); //Активируем новые архивы
      }
    }

    if( Request::get('action') == 'ajax' ){
      header('Content-type: application/json');

      $start = Request::post('start');
      $search = Request::post('search');
      $columns = Request::post('columns');

      $length = Request::post('length');

      if( $start < 0 ) $start = 0;
      if( $length < 0 ) $length = $this->app->config['services_show_num'];
      if( $length > 1000 ) $length = 1000;

      $itemsType = 'all';
      if( !empty($columns) && !empty($columns[1]) && !empty($columns[1]['search']['value']) ){
        $itemsType = $columns[1]['search']['value'];
      }

      //filter
      $whereAdd = ['1'];

      if( $this->app->user->role == 'admin' ){
        array_push($whereAdd, ' and (parent = 0)');
      }else{
        array_push($whereAdd, ' and (type != "txtgroup")');

        //Если пришло время спрятать архивы в групповых текстовых файлах
        $userInfo = $this->app->user->identity;
        $oldArchives = $userInfo->archivesTimeOld();
        array_push($whereAdd, ' and (id NOT IN("'.implode('", "', $oldArchives).'"))');
      }

      if( !empty($search['value']) ){
        array_push($whereAdd, ' and (
          name LIKE "%'.addslashes($search['value']).'%" OR
          sid LIKE "%'.addslashes($search['value']).'%"
        )');
      }

      if( $this->app->user->role == 'user' ){
        $userInfo = $this->app->user->identity;
        $userInfo->loadServices();
        array_push($whereAdd, ' and (
          sid IN ("'.implode('","', $userInfo->services).'")
        )');
      }

      if( $itemsType != 'all' ){
        array_push($whereAdd, ' AND type = "'.addslashes($itemsType).'"');
      }

      $services->where(implode('', $whereAdd));
      $services->limit($start, $length);
      if( $this->app->user->role == 'user' ){
        $services->orderby('sort asc, id asc');
      }
      $items = $services->findAll();
      //end filter

      //count filter
      $services->select('count(1) as count');
      $services->where(implode('', $whereAdd));
      $filtered = $services->find();
      $filtered = $filtered->count;
      //end count filter

      $data = [];
      foreach($items as $item){
        array_push($data, [
          'id' => $item->id,
          'sid' => $item->sid,
          'name' => $item->name,
          'type' => $item->type,
          'note' => $item->note,
          'serviceLink' => $item->serviceLink(),
          'typeTitle' => Services::getTypeTitle($item->type),
        ]);
      }

      $json_data = array(
          "draw"            => intval( $_REQUEST['draw'] ),
          "recordsTotal"    => intval( $total ),
          "recordsFiltered" => intval( $filtered ),
          "data"            => $data
      );
      echo json_encode($json_data);
      exit;
    }

    $returnData = array(
      'total' => $total,
      'total_txt' => $total_txt,
      'total_txtgroup' => $total_txtgroup,
      'total_zip' => $total_zip,
      'total_rar' => $total_rar,
      'total_page' => $total_page,
      'model' => $services,
    );

    if( $this->app->user->role == 'user' ){
      $userInfo = $this->app->user->identity;
      $returnData['mainService'] = $userInfo->getMainService();
    }

    return $returnData;
  }

  public function actionChilds(){
    $model = new Services();
    $model->eq('id', Request::get('id'))->find();

    if( empty($model->data) ) exit;

    return $this->renderPartial('services/_childs', [
      'model' => $model,
    ]);
  }

  public function actionCreate(){
    $model = new ServicesCreateForm();

    if( Request::issetPost() ){
      $model->load(array_merge($_POST, $_FILES));

      if( $model->validate() ){

        $services = new Services();
        $services->load($_POST);
        $services->insert();

        if( $model->type == 'txtgroup' ){ //Группа сервисов
          foreach( $model->names as $i=>$value ){
            $childServices = new Services();
            $childServices->parent = $services->id;
            $childServices->type = $model->types[$i];
            $childServices->name = $model->names[$i];
            $childServices->sid = $model->sid;
            $childServices->insert();

            if( !is_dir(dirname($childServices->filePath())) ){
              mkdir(dirname($childServices->filePath()));
            }
            copy($_FILES['files']['tmp_name'][$i], $childServices->filePath());
          }
        }else{ //Один сервис
          if( !is_dir(dirname($services->filePath())) ){
            mkdir(dirname($services->filePath()));
          }

          if( $model->type == 'page' ){
            file_put_contents($services->filePath(), $model->code);

            //Добавляем информация о блоках
            $boxinfo = [];
            if( !$model->blank('boxnames') ){
              $boxinfo['columns_num'] = $model->columns_num;
              $boxinfo['boxs'] = [];
              foreach( $model->boxnames as $i=>$value ){
                $boxinfo['boxs'][] = [
                  'name' => $model->boxnames[$i],
                  'color' => $model->boxcolors[$i],
                  'value' => $model->boxvalues[$i],
                ];
              }
            }
            $services->saveBoxInfo($boxinfo);
            $services->boxinfo = serialize($boxinfo);
            $services->update();
            //Конец. Добавляем информация о блоках

          }else{
            copy($_FILES['file']['tmp_name'], $services->filePath());
          }
        }

        if( $services->type == 'txtgroup' ){
          die('services/edit?id='.$services->id);
        }else{
          die('services/index');
        }
      }else{
        die(json_encode($model->errors));
      }
    }

    return $this->render('services/create', [
      'model' => $model,
    ]);
  }

  public function actionEdit(){
    $services = new Services();
    $services->eq('id', Request::get('id'))->find();

    if( empty($services->data) ){
      return $this->redirect('services/index');
    }

    $oldFile = $services->filePath();

    $model = new ServicesEditForm();
    $model->load($services->data);

    if( Request::issetPost() ){
      $model->load(array_merge($_POST, $_FILES));

      if( $model->validate() ){
        $services->load($_POST);
        $services->update();

        if( $model->type == 'txtgroup' ){ //Группа сервисов
          //Редактируем текущие
          if( !$model->blank('child') ) foreach( $model->child as $id=>$data ){
            $childServices = new Services();
            $childServices->eq('id', $id)->find();
            if( empty($childServices->data) ) continue;

            $childServices->name = $data['name'];
            $childServices->sort = $data['sort'];
            $childServices->sid = $model->sid;
            $childServices->update();
          }

          //Добавляем новые
          foreach( $model->names as $i=>$value ){
            if( empty($value) ) continue;

            $childServices = new Services();
            $childServices->parent = $services->id;
            $childServices->type = $model->types[$i];
            $childServices->name = $model->names[$i];
            $childServices->sid = $model->sid;
            $childServices->insert();

            if( !is_dir(dirname($childServices->filePath())) ){
              mkdir(dirname($childServices->filePath()));
            }
            copy($_FILES['files']['tmp_name'][$i], $childServices->filePath());
          }
        }else{ //Один сервис
          if( !is_dir(dirname($services->filePath())) ){
            mkdir(dirname($services->filePath()));
          }

          if( $model->type == 'page' || !empty($_FILES['file']['tmp_name']) ){
            if( is_file( $oldFile ) ){
              unlink($oldFile);
            }

            if( $model->type == 'page' ){
              file_put_contents($services->filePath(), $model->code);

              //Добавляем информация о блоках
              $boxinfo = [];
              if( !$model->blank('boxnames') ){
                $boxinfo['columns_num'] = $model->columns_num;
                $boxinfo['boxs'] = [];
                foreach( $model->boxnames as $i=>$value ){
                  $boxinfo['boxs'][] = [
                    'name' => $model->boxnames[$i],
                    'color' => $model->boxcolors[$i],
                    'value' => $model->boxvalues[$i],
                  ];
                }
              }
              $services->saveBoxInfo($boxinfo);
              $services->boxinfo = serialize($boxinfo);
              $services->update();
              //Конец. Добавляем информация о блоках
            }else{
              copy($_FILES['file']['tmp_name'], $services->filePath());
            }
          }
        }

        die('{}');
      }else{
        die(json_encode($model->errors));
      }
    }else{
      if( $model->type == 'page' && is_file($model->filePath()) ){
        $model->code = file_get_contents( $model->filePath() );
      }
    }

    return $this->render('services/edit', [
      'model' => $model,
    ]);
  }

  public function actionDelete(){
    $deleteList = Request::post('delete');

    if( !empty($deleteList) ){
      $services = new Services();
      $items = $services->in('id', array_values($deleteList))->findAll();
      foreach($items as $item){
        $item->delete();
      }
    }

    return $total = $services->select('count(1) as count')
      ->find()
      ->count;
  }

  public function actionGet(){
    $id = Request::get('id');
    $type = Request::get('type');


    $service = new Services();
    $service->eq('id', $id);
    if( $this->app->user->role != 'admin' ){
      self::accessFilter($service);
      $_SESSION['page_user'] = 'user';
    }else{
      $_SESSION['page_user'] = 'admin';
    }
    $service = $service->find();

    if( empty($service->data) ){
      die(App::t('Доступ запрещен!'));
    }

    switch($type){
      case "text":
        $file = __DIR__.'/../files/txt/'.$service->id.'.txt';
        header('Content-type: text/plain; charset=utf-8');
        echo iconv('cp1251', 'utf-8', file_get_contents($file));
      break;

      case "archive":
        if( !in_array($service->type, ['txt', 'rar', 'zip']) ) exit;
        $file = __DIR__.'/../files/'.$service->type.'/'.$service->id.'.'.$service->type;
        header('Content-Disposition: attachment; filename="'.$service->name.'.'.$service->type);
        readfile($file);
      break;

      case "page":
        header('Location: '.Request::rootdir().''.$service->alias);
      break;
    }
  }

  public function actionPage(){
    $alias = basename($_SERVER['REQUEST_URI']);

    $service = new Services();
    $service->eq('alias', $alias);
    if( $this->app->user->role != 'admin' ){
      self::accessFilter($service);
    }
    $service = $service->find();

    if( empty($service->data) ){
      die(App::t('Доступ запрещен!'));
    }

    echo '<!DOCTYPE html>
    <html>
     <head>
     <meta charset="utf-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <meta name="description" content="">
     <meta name="author" content="">
     <base href="'.Request::rootdir().'" />
     <title>'.$service->title.'</title>
     <link href="design/bootstrap/css/bootstrap.min.css" rel="stylesheet">
     <script src="design/jquery/jquery.min.js"></script>
     <script src="design/bootstrap/js/bootstrap.min.js"></script>
     </head>
     <body>
     ';
     include( $service->filePath() );
     include( $service->boxinfoFilePath() );
     echo '
     </body>
    </html>
    ';
  }
}
