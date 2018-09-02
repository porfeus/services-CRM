<?php
class ServicesCreateForm extends Services{

	public function attributeLabels(){
		return array_merge(parent::attributeLabels(), [
			'file' => App::t('Загрузить файл'),
			'code' => App::t('Содержимое'),
			'names' => App::t('Название файла'),
			'types' => App::t('Формат'),
			'files' => App::t('Файл'),
			'boxnames' => App::t('Название блока'),
			'boxcolors' => App::t('Цвет блока'),
			'boxvalues' => App::t('Содержимое'),
			'columns_num' => App::t('Число колонок'),
		]);
	}

	public function rules(){
		return array_merge(parent::rules(), [
			[['file'], 'file'],
			[['alias', 'title'], 'page'],
		]);
	}

	public function file($attribute){
		$value = $this->{$attribute};

		if( $this->type == 'page' || $this->type == 'txtgroup' ) return true;

		if( empty($value['tmp_name']) ){
			$this->addError($attribute, App::t('Выберите файл'));
			return false;
		}

		$format = array_pop(explode('.', $value['name']));
		if( strcasecmp($format, $this->type) != 0 ){
			$this->addError($attribute, App::t('Неправильный формат файла'));
			return false;
		}

		return true;
  }

	public function page($attribute){
    $value = $this->{$attribute};
    if( trim($value) != '' || $this->type != 'page' ) return true;
    $this->addError($attribute, App::t('Значение не может быть пустым'));
    return false;
  }
}
