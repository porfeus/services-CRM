<?php
class Services extends BaseModel{
	public $table = 'services';
	public $primaryKey = 'id';
	public $relations = array(
		'childServices' => array(self::HAS_MANY, 'Services', 'parent', array('orderby'=>'sort asc')),
	);

	static $typesTitle = [
		'txt' => 'Текстовые файлы',
		'txtgroup' => 'Группа текстовых файлов',
		'zip' => 'ZIP файлы',
		'rar' => 'RAR файлы',
		'page' => 'PAGE страницы',
	];

	public function beforeDelete(){
		if( is_file($this->filePath()) ){
			@unlink($this->filePath());
		}
		if( is_file($this->boxinfoFilePath()) ){
			@unlink($this->boxinfoFilePath());
		}

		if( $this->type == 'txtgroup' ){
			foreach( $this->childServices as $childModel ){
				$childModel->delete();
			}
		}
	}

	public function attributeLabels(){
		return [
			'id' => App::t('#'),
			'parent' => App::t('ID группы услуг'),
			'sort' => App::t('Порядок'),
			'type' => App::t('Формат'),
			'name' => App::t('Название'),
			'sid' => App::t('ID'),
			'alias' => App::t('Алиас'),
			'title' => App::t('Заголовок'),
			'boxinfo' => App::t('Данные блоков'),
			'note' => App::t('Примечание'),
			'html_header' => App::t('Инфо над услугой'),
			'html_footer' => App::t('Инфо под услугой'),
		];
	}

	public function rules(){
		return [
			[['type', 'name', 'sid'], 'required'],
			[['sid'], 'letters'],
			[['alias'], 'unique'],
			[['sid'], 'filterUnique'],
		];
	}

  public function filterUnique($attribute, $rule){
    $value = $this->{$attribute};
    $id = intval($this->id);

    if( $value == '' ) return true;

    $model = new static();
    $model->select('count(1) as count');
    $model->eq($attribute, $value);
    $model->ne('id', $id);
    $model->eq('parent', '0'); //ignore childs
    $model->find();

    if( !$model->count ) return true;
    $this->addError($attribute, App::t('Значение должно быть уникальным'));
    return false;
  }

	public static function getTypeTitle($type){
		return self::$typesTitle[$type];
	}

	public static function getDropdownTypesData($type = ''){
		$arr = self::$typesTitle;
		if( $type == 2 ){
			unset($arr['zip']);
			unset($arr['rar']);
		}
		return array_flip($arr);
	}

  public function letters($attribute){
    $value = trim($this->{$attribute});
    if( $value == '' || !preg_match('@[^A-z]@', $value) ) return true;
    $this->addError($attribute, App::t('Значение должно состоять только из букв латинского алфавита'));
    return false;
  }

	public function filePath(){
		return Request::rootpath().
			'files/'.$this->type.'/'.$this->id.'.'.str_replace('page', 'php', $this->type);
	}

	public function boxinfoFilePath(){
		return Request::rootpath().
			'files/'.$this->type.'/'.$this->id.'_box.'.str_replace('page', 'php', $this->type);
	}

	public function saveBoxInfo($boxinfo){
		$rows = '';
		$cols = '';
		$num = 0;


		if( $boxinfo['columns_num'] == 6 ){
			$colNum = 2;
		}else
		if( $boxinfo['columns_num'] == 4 ){
			$colNum = 3;
		}else
		if( $boxinfo['columns_num'] == 3 ){
			$colNum = 4;
		}else
		if( $boxinfo['columns_num'] == 2 ){
			$colNum = 6;
		}else{
			$colNum = 12;
		}
		foreach( $boxinfo['boxs'] as $data ){
			$num ++;
			$cols .= '
			<div class="col-sm-'.$colNum.'">
				<div class="panel panel-'.$data['color'].'">
				  <div class="panel-heading">'.$data['name'].'</div>
				  <div class="panel-body">
				    '.$data['value'].'
				  </div>
				</div>
			</div>
			';

			if( $boxinfo['columns_num'] == $num ){
				$rows .= '<div class="row">'.$cols.'</div>';
				$cols = '';
				$num = 0;
			}
		}

		if( !empty($cols) ){
			$rows .= '<div class="row">'.$cols.'</div>';
		}

		$html = '<div class="container">'.$rows.'</div>';
		file_put_contents($this->boxinfoFilePath(), $html);
	}

	public static function getDropdownData($ignore = [1]){
		$services = new self();
		$services = $services
			->eq('parent', '0')
			->notin('type', $ignore)
			->findAll();

		$list = [];
		foreach( $services as $model ){
			if( !isset($list[$model->type]) ) $list[$model->type] = [];
			$list[$model->type][] = $model->sid;
		}

		$finalList = [];

		foreach($list as $type=>$item){
			$item['label'] = self::getTypeTitle($type);
			$finalList[] = $item;
		}

		return $finalList;
	}

	public static function getServicesNameBySid(){
		$services = new self();
		$services = $services
			->eq('parent', '0')
			->findAll();

		$list = [];
		foreach( $services as $model ){
			$list[$model->sid] = $model->name;
		}

		return $list;
	}

	public function serviceLink(){
		if( $this->type == 'txt' ){
			return '<a href="services/get?type=text&id='. $this->id .'" target="_blank">'.App::t('Открыть').' <i class="fa fa-angle-double-right "></i></a> <a href="services/get?type=archive&id='. $this->id .'" style="margin-left:10px;">'.App::t('Скачать').' <i class="fa fa-angle-double-right "></i></a>';
		}else
		if( $this->type == 'zip' || $this->type == 'rar' ){
			return '<a href="services/get?type=archive&id='. $this->id .'">'.App::t('Скачать').' <i class="fa fa-angle-double-right "></i></a>';
		}else
		if( $this->type == 'page' ){
			return '<a href="services/get?type=page&id='. $this->id .'" target="_blank">'.App::t('Перейти').' <i class="fa fa-angle-double-right "></i></a>';
		}else
		if( $this->type == 'txtgroup' ){
			return '<a data-id="'. $this->id .'" href="#" onclick="loadFiles(event, this)">'.App::t('Список файлов').' <i class="fa fa-angle-double-right "></i></a>';
		}else{
			return '-';
		}
	}
}
