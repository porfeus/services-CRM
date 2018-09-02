<?php
class ButtonsController extends BaseController{

  public function accessRules(){
    return [
        [
            'allow' => true,
            'roles' => ['admin'],
        ],
    ];
  }

  public function actionIndex(){

    $buttons = new Buttons();
    $total = $buttons->select('count(1) as count')
      ->find()
      ->count;
    $total_txt = $buttons->select('count(1) as count')
      ->join('services', 'services.sid = buttons.service and services.parent = 0')
      ->where('services.type = "txt"')
      ->find()
      ->count;
    if( empty($total_txt) ) $total_txt = 0;
    $total_txtgroup = $buttons->select('count(1) as count')
      ->join('services', 'services.sid = buttons.service and services.parent = 0')
      ->where('services.type = "txtgroup"')
      ->find()
      ->count;
    if( empty($total_txtgroup) ) $total_txtgroup = 0;
    $total_page = $buttons->select('count(1) as count')
      ->join('services', 'services.sid = buttons.service and services.parent = 0')
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

      $accountType = 'all';
      if( !empty($columns) && !empty($columns[2]) && !empty($columns[2]['search']['value']) ){
        $accountType = $columns[2]['search']['value'];
      }

      //filter
      $buttons = new Buttons();

      $whereAdd = ['1'];
      if( !empty($search['value']) ){
        array_push($whereAdd, ' AND (
          link LIKE "%'.addslashes($search['value']).'%"
        )');
      }

      if( $accountType != 'all' ){
        array_push($whereAdd, ' AND services.type = "'.$accountType.'"');
        $buttons->join('services', 'services.sid = buttons.service and services.parent = 0');
        //$buttons->group('buttons.id');
      }

      $buttons->where(implode('', $whereAdd));
      $buttons->limit($start, $length);
      $items = $buttons->findAll();
      //end filter

      //count filter
      $buttons->select('count(1) as count');
      $buttons->where(implode('', $whereAdd));
      $filtered = $buttons->find();
      $filtered = $filtered->count;
      //end count filter

      $data = [];
      foreach($items as $item){
        array_push($data, [
          'id' => $item->id,
          'link' => $item->link,
          'created_time' => date('d.m.Y', $item->created_time),
          'activated_add_time' => $item->getActivatedTimeTitle(),
          'service' => $item->service,
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

    return $this->render('buttons/index', array(
      'total' => $total,
      'total_txt' => $total_txt,
      'total_txtgroup' => $total_txtgroup,
      'total_page' => $total_page,
      'getServicesNames' => Services::getServicesNameBySid(),
      'model' => $buttons,
    ));
  }

  public function actionCreate(){
    $model = new ButtonsCreateForm();

    if( Request::issetPost() ){
      $model->load($_POST);
      $model->activated_type = 'D'; // manual set

      if( $model->validate() ){
        $model->setActivatedTime();

        $button = new Buttons();
        $button->load($model->data);
        $button->service = $model->service[0];
        $button->created_time = time();
        $button->insert();

        return $this->redirect('buttons/index');
      }
    }

    return $this->render('buttons/create', [
      'model' => $model,
      'dropdownData' => Services::getDropdownData(['rar', 'zip']),
      'servicesNames' => Services::getServicesNameBySid(),
    ]);
  }

  public function actionEdit(){
    $buttons = new Buttons();
    $buttons->eq('id', Request::get('id'))->find();

    if( empty($buttons->data) ){
      return $this->redirect('buttons/index');
    }

    $model = new ButtonsCreateForm();
    $model->load($buttons->data);
    $model->service = [$model->service];

    $model->activated_num = round($model->activated_add_time / 86400);

    if( Request::issetPost() ){
      $model->load($_POST);
      $model->activated_add_time = 0; // manual set
      $model->activated_type = 'D'; // manual set

      if( $model->validate() ){
        $model->setActivatedTime();

        $buttons->load($model->data);
        $buttons->service = $model->service[0];
        $buttons->update();

        return $this->redirect('buttons/index');
      }
    }

    return $this->render('buttons/edit', [
      'model' => $model,
      'dropdownData' => Services::getDropdownData(['rar', 'zip']),
      'servicesNames' => Services::getServicesNameBySid(),
    ]);
  }

  public function actionDelete(){
    $deleteList = Request::post('delete');

    if( !empty($deleteList) ){
      $buttons = new Buttons();
      $items = $buttons->in('id', array_values($deleteList))->findAll();
      foreach($items as $item){
        $item->delete();
      }
    }

    $total = $buttons->select('count(1) as count')
      ->find()
      ->count;

    return json_encode([
      'total' => $total,
    ]);
  }
}
