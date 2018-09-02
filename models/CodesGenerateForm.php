<?php
class CodesGenerateForm extends Codes{

	public function defaultValues(){
		return [
			'activated_num' => 0,
		];
	}

	public function attributeLabels(){
		return array_merge(parent::attributeLabels(), [
			'codes_num' => App::t('Количество'),
			'activated_type' => App::t('Время действия'),
			'activated_num' => App::t('Укажите время'),
			'language' => App::t('Язык сообщения'),
		]);
	}

	public function rules(){
		return [
			[['codes_num', 'activated_num', 'activated_type', 'language', 'service'], 'required'],
			[['codes_num', 'activated_num'], 'number'],
		];
	}
}
