<?php
class RenevalForm extends Codes{
	public $info;

	public function attributeLabels(){
		return [
			'id' => App::t('#'),
			'code' => App::t('Код'),
		];
	}

	public function rules(){
		return [
			[['code'], 'required'],
			[['code'], 'checkCode'],
		];
	}

	public function checkCode($attribute){
    $value = $this->{$attribute};

		$userInfo = App::get()->user->identity;
		$userInfo->loadServices();

		$codes = new self();
		$codes
			->eq('code', $value)
			->in('service', $userInfo->services)
			->find();

		if( empty($codes->data) ){
			$this->addError($attribute, App::t('Неправильный код активации'));
	    return false;
		}
		if( $codes->activated_time > 0 ){
			$this->addError($attribute, App::t('Данный код активации уже использован'));
	    return false;
		}
		$this->info = $codes;
		return true;
  }
}
