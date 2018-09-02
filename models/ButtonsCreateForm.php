<?php
class ButtonsCreateForm extends Buttons{

	public function defaultValues(){
		return [
			'activated_num' => 0,
		];
	}

	public function attributeLabels(){
		return array_merge(parent::attributeLabels(), [
			'activated_num' => App::t('Тариф (дней)'),
		]);
	}
	public function rules(){
		return array_merge(parent::rules(), [
			[['activated_num'], 'required'],
			[['activated_num'], 'number'],
		]);
	}
}
