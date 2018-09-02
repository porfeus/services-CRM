<?php
class CodesEditForm extends Codes{

	public function defaultValues(){
		return [
			'activated_num' => 0,
		];
	}

	public function attributeLabels(){
		return array_merge(parent::attributeLabels(), [
			'activated_type' => App::t('Время действия'),
			'activated_num' => App::t('Укажите время'),
		]);
	}

	public function rules(){
		return array_merge(parent::rules(), [
			[['activated_num', 'activated_type'], 'required'],
			[['activated_num'], 'number'],
		]);
	}
}
