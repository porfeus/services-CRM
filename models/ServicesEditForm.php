<?php
class ServicesEditForm extends ServicesCreateForm{

	public function attributeLabels(){
		return array_merge(parent::attributeLabels(), [
			'child' => App::t('Дочерние услуги'),
		]);
	}

	public function rules(){
		return array_filter(parent::rules(), function($item){
			//Пропускаем требование файла при редактировании
			if( $item[0][0] == 'file' ) return false;
			return true;
		});
	}
}
