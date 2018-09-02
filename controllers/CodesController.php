<?php
class CodesController extends BaseController{

  public function accessRules(){
    return [
        [
            'allow' => true,
            'roles' => ['admin'],
        ],
    ];
  }

  public function actionIndex(){

    $codes = new Codes();
    $total = $codes->select('count(1) as count')
      ->find()
      ->count;
    $noactivated = $codes->select('count(1) as count')
      ->eq('activated_time', 0)
      ->find()
      ->count;
    $total_txt = $codes->select('count(1) as count')
      ->join('services', 'services.sid = codes.service and services.parent = 0')
      ->where('services.type = "txt"')
      ->find()
      ->count;
    if( empty($total_txt) ) $total_txt = 0;
    $total_txtgroup = $codes->select('count(1) as count')
      ->join('services', 'services.sid = codes.service and services.parent = 0')
      ->where('services.type = "txtgroup"')
      ->find()
      ->count;
    if( empty($total_txtgroup) ) $total_txtgroup = 0;
    $total_page = $codes->select('count(1) as count')
      ->join('services', 'services.sid = codes.service and services.parent = 0')
      ->where('services.type = "page"')
      ->find()
      ->count;
    if( empty($total_page) ) $total_page = 0;

    if( Request::get('action') == 'ajax' ){
      header('Content-type: application/json');

      $start = Request::post('start');
      $search = Request::post('search');
      $columns = Request::post('columns');

      $length = Request::post('length');
      if( $length > 1000 ) $length = 1000;

      $activatedType = 'no';
      if( !empty($columns) && !empty($columns[1]) && !empty($columns[1]['search']['value']) ){
        $activatedType = $columns[1]['search']['value'];
      }

      $accountType = 'all';
      if( !empty($columns) && !empty($columns[2]) && !empty($columns[2]['search']['value']) ){
        $accountType = $columns[2]['search']['value'];
      }

      //filter
      $codes = new Codes();

      $whereAdd = ['1'];
      if( !empty($search['value']) ){
        array_push($whereAdd, ' AND (
          codes.code LIKE "%'.addslashes($search['value']).'%"
        )');
      }

      if( $activatedType != 'all' ){
        if( $activatedType == 'no' ){
          array_push($whereAdd, ' AND codes.activated_time = 0');
        }else{
          array_push($whereAdd, ' AND codes.activated_time > 0');
        }
      }

      if( $accountType != 'all' ){
        array_push($whereAdd, ' AND services.type = "'.$accountType.'"');
        $codes->join('services', 'services.sid = codes.service and services.parent = 0');
        //$codes->group('codes.id');
      }

      $codes->where(implode('', $whereAdd));
      $codes->limit($start, $length);
      $items = $codes->findAll();
      //end filter

      //count filter
      $codes->select('count(1) as count');
      $codes->where(implode('', $whereAdd));
      $filtered = $codes->find();
      $filtered = $filtered->count;
      //end count filter

      $data = [];
      foreach($items as $item){
        array_push($data, [
          'id' => $item->id,
          'code' => $item->code,
          'created_time' => date('d.m.Y', $item->created_time),
          'activated_add_time' => $item->getActivatedTimeTitle(),
          'service' => $item->service,
          'status' => $item->getStatus(),
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

    return $this->render('codes/index', array(
      'total' => $total,
      'noactivated' => $noactivated,
      'total_txt' => $total_txt,
      'total_txtgroup' => $total_txtgroup,
      'total_page' => $total_page,
      'getServicesNames' => Services::getServicesNameBySid(),
      'model' => $codes,
    ));
  }

  public function actionEdit(){
    $codes = new Codes();
    $codes->eq('id', Request::get('id'))->find();

    if( empty($codes->data) ){
      return $this->redirect('codes/index');
    }

    $model = new CodesEditForm();
    $model->load($codes->data);
    $model->service = [$model->service];

    if( Request::issetPost() ){
      $model->load($_POST);

      if( $model->validate() ){
        $model->setActivatedTime();

        $codes->load($model->data);
        $codes->service = $model->service[0];
        $codes->update();

        return $this->redirect('codes/index');
      }
    }

    return $this->render('codes/edit', [
      'model' => $model,
      'dropdownData' => Services::getDropdownData(['rar', 'zip']),
      'servicesNames' => Services::getServicesNameBySid(),
    ]);
  }

  public function actionDelete(){
    $deleteList = Request::post('delete');

    if( !empty($deleteList) ){
      $codes = new Codes();
      $items = $codes->in('id', array_values($deleteList))->findAll();
      foreach($items as $item){
        $item->delete();
      }
    }

    $total = $codes->select('count(1) as count')
      ->find()
      ->count;
    $noactivated = $codes->select('count(1) as count')
      ->eq('activated_time', 0)
      ->find()
      ->count;

    return json_encode([
      'total' => $total,
      'noactivated' => $noactivated,
    ]);
  }

  public function actionGenerate(){
    $model = new CodesGenerateForm();
    $result = false;

    if( Request::issetPost() ){
      $model->load($_POST);

      if( $model->validate() ){
        $model->setActivatedTime();

        $result = [];
        $csvResult = [];

        $codes_num = $model->codes_num;

        for($i=0; $i<$codes_num; $i++){
          if( $i >= 10000 ) break;

          $codes = new Codes();
          $codes->load($model->data);
          $codes->service = $model->service[0];
          $codes->created_time = time();
          $codes->code = self::generateCode();

          if( $codes->validate() ){
            $codes->insert();
          }else{
            $codes_num ++;
            continue;
          }

          $post_message = trim(Request::post('message_'.$model->language));

          $message = $post_message;
          $message = str_replace('{code}', $codes->code, $message);
          $message = str_replace('{time}', $model->getActivatedTimeTitle($model->language), $message);
          $result[] = $message;

          $message = $post_message;
          $message = str_replace('{code}', $codes->code.' / ', $message);
          $message = str_replace('{time}', $model->getActivatedTimeTitle($model->language), $message);
          $csvResult[] = $message;
        }

        $result = implode(PHP_EOL.'================'.PHP_EOL, $result);

        //csv
        $csvResult = array_map(function($item){
          $item = str_replace("
", " ", $item);
          $item = str_replace('  ', ' ', $item);
          return $item;
        }, $csvResult);
        $csvResult = '"'.implode('"'.PHP_EOL.'"', $csvResult).'"';
        $csvResult = iconv('utf-8', 'cp1251', $csvResult);
        file_put_contents(__DIR__.'/../files/generate.csv', $csvResult);
        //end csv
      }
    }

    $languagesData = $this->app->language->data();
    $languages = [];
    $messages = [];
    foreach($languagesData as $id=>$data){
      $languages[$data['language_title']] = $id;

      $messages['message_'.$id] = $data['generate_codes_message'];
    }

    return $this->render('codes/generate', [
      'model' => $model,
      'dropdownData' => Services::getDropdownData(['rar', 'zip']),
      'servicesNames' => Services::getServicesNameBySid(),
      'result' => $result,
      'languages' => $languages,
      'messages' => $messages,
    ]);
  }

  public static function generateCode(){
    $letters = implode("", [
      "ABCDEFGHIGKLMNOPQRSTUVWXYZ",
      "1234567890",
    ]);

    $code = [];

    for($i=0; $i<7; $i++){
      $password = "";
      for($a=0; $a<5; $a++){
        $password.= $letters[rand(0, strlen($letters))];
      }
      $code[] = $password;
    }

    return implode('-', $code);
  }
}
